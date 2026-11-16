<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Support\DatabaseDumper;
use App\Support\GoogleDriveBackupClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BackupDatabaseToGoogleDrive extends Command
{
    protected $signature = 'backup:google-drive {--keep-local : Keep the local compressed backup after upload}';

    protected $description = 'Dump the database to a gzipped SQL file, upload it to Google Drive, and prune old backups';

    public function handle(DatabaseDumper $dumper, GoogleDriveBackupClient $drive): int
    {
        if (!config('backup.enabled', true)) {
            $this->warn('Google Drive backup is disabled via GOOGLE_DRIVE_BACKUP_ENABLED.');
            return self::SUCCESS;
        }

        if (!$drive->isConfigured()) {
            $message = 'Google Drive backup credentials are not configured in .env.';
            $this->error($message);
            $this->recordHistory([
                'status' => 'failed',
                'filename' => null,
                'size_bytes' => 0,
                'method' => null,
                'duration_ms' => 0,
                'drive_file_id' => null,
                'pruned_count' => 0,
                'error' => $message,
                'ran_at' => now(config('backup.timezone', 'Asia/Dhaka'))->toIso8601String(),
            ]);
            return self::FAILURE;
        }

        $lock = Cache::lock('google-drive-backup', 600);
        if (!$lock->get()) {
            $this->warn('Another Google Drive backup is already in progress. Skipping.');
            return self::SUCCESS;
        }

        $startedAt = microtime(true);
        $localPath = null;
        $filename = null;
        $sizeBytes = 0;
        $method = null;

        try {
            $this->info('Creating compressed database dump...');
            $dump = $dumper->dumpToGzip();
            $localPath = $dump['path'];
            $filename = $dump['filename'];
            $sizeBytes = $dump['size_bytes'];
            $method = $dump['method'];

            $this->info("Dump ready ({$filename}, {$sizeBytes} bytes via {$method}). Uploading to Google Drive...");
            $folderId = (string) config('backup.google_drive.folder_id');
            $uploaded = $drive->uploadFile($localPath, $folderId, $filename);

            $retention = (int) config('backup.retention_count', 14);
            $prune = $drive->pruneOldBackups($folderId, $retention);

            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            $this->recordHistory([
                'status' => 'success',
                'filename' => $uploaded['name'],
                'size_bytes' => $sizeBytes,
                'method' => $method,
                'duration_ms' => $durationMs,
                'drive_file_id' => $uploaded['id'],
                'pruned_count' => $prune['deleted'],
                'error' => null,
                'ran_at' => now(config('backup.timezone', 'Asia/Dhaka'))->toIso8601String(),
            ]);

            $this->info("Backup uploaded to Google Drive (ID: {$uploaded['id']}). Pruned {$prune['deleted']} old backup(s).");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

            Log::error('Google Drive database backup failed', [
                'error' => $e->getMessage(),
                'filename' => $filename,
            ]);

            $this->recordHistory([
                'status' => 'failed',
                'filename' => $filename,
                'size_bytes' => $sizeBytes,
                'method' => $method,
                'duration_ms' => $durationMs,
                'drive_file_id' => null,
                'pruned_count' => 0,
                'error' => $e->getMessage(),
                'ran_at' => now(config('backup.timezone', 'Asia/Dhaka'))->toIso8601String(),
            ]);

            $this->error('Backup failed: ' . $e->getMessage());

            return self::FAILURE;
        } finally {
            if ($localPath && !$this->option('keep-local') && is_file($localPath)) {
                @unlink($localPath);
            }
            $lock->release();
        }
    }

    protected function recordHistory(array $entry): void
    {
        try {
            $existing = Setting::where('key', 'google_drive_backup_history')->value('value');
            $history = is_string($existing) ? json_decode($existing, true) : [];
            if (!is_array($history)) {
                $history = [];
            }

            array_unshift($history, $entry);
            $history = array_slice($history, 0, 10);

            Setting::updateOrCreate(
                ['key' => 'google_drive_backup_history'],
                ['value' => json_encode($history), 'type' => 'json']
            );
            Cache::forget('global_settings');
        } catch (\Throwable $ignore) {
            // Never let history persistence crash the backup command itself.
        }
    }
}
