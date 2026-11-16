<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleDriveBackupClient
{
    public const CHUNK_SIZE_BYTES = 4194304; // 4 MB (multiple of 256 KB required by Google Drive)

    public function isConfigured(): bool
    {
        $cfg = config('backup.google_drive', []);

        return !empty($cfg['client_id'])
            && !empty($cfg['client_secret'])
            && !empty($cfg['refresh_token'])
            && !empty($cfg['folder_id']);
    }

    public function getAccessToken(): string
    {
        $cfg = config('backup.google_drive', []);

        if (!$this->isConfigured()) {
            throw new RuntimeException('Google Drive backup credentials are incomplete. Please configure GOOGLE_DRIVE_CLIENT_ID, GOOGLE_DRIVE_CLIENT_SECRET, GOOGLE_DRIVE_REFRESH_TOKEN, and GOOGLE_DRIVE_FOLDER_ID.');
        }

        $response = Http::asForm()
            ->timeout(30)
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'refresh_token' => $cfg['refresh_token'],
                'grant_type' => 'refresh_token',
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Failed to refresh Google Drive OAuth token: ' . $response->body());
        }

        $token = (string) $response->json('access_token', '');
        if ($token === '') {
            throw new RuntimeException('Google OAuth response did not include an access_token.');
        }

        return $token;
    }

    /**
     * Upload a local gzipped backup file to Google Drive via Resumable Upload.
     *
     * @return array{id: string, name: string, size: int}
     */
    public function uploadFile(string $localPath, string $folderId, ?string $filename = null): array
    {
        if (!is_file($localPath) || !is_readable($localPath)) {
            throw new RuntimeException("Backup file [{$localPath}] does not exist or is not readable.");
        }

        $fileSize = (int) filesize($localPath);
        if ($fileSize <= 0) {
            throw new RuntimeException("Backup file [{$localPath}] is empty.");
        }

        $filename = $filename ?: basename($localPath);
        $accessToken = $this->getAccessToken();

        // 1. Initiate Resumable Upload Session
        $initResponse = Http::withToken($accessToken)
            ->timeout(30)
            ->withHeaders([
                'X-Upload-Content-Type' => 'application/gzip',
                'X-Upload-Content-Length' => (string) $fileSize,
            ])
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable&fields=id,name,size', [
                'name' => $filename,
                'parents' => [$folderId],
            ]);

        if (!$initResponse->successful()) {
            throw new RuntimeException('Failed to initiate Google Drive resumable upload: ' . $initResponse->body());
        }

        $uploadUri = (string) $initResponse->header('Location');
        if ($uploadUri === '') {
            throw new RuntimeException('Google Drive resumable upload did not return a Location header.');
        }

        // 2. Stream file chunks
        $handle = @fopen($localPath, 'rb');
        if ($handle === false) {
            throw new RuntimeException("Unable to open [{$localPath}] for upload.");
        }

        try {
            $offset = 0;
            $uploadedMeta = null;

            while ($offset < $fileSize) {
                $chunk = fread($handle, self::CHUNK_SIZE_BYTES);
                if ($chunk === false || $chunk === '') {
                    throw new RuntimeException('Unexpected EOF while reading backup file for upload.');
                }

                $chunkLen = strlen($chunk);
                $rangeEnd = $offset + $chunkLen - 1;

                $putResponse = Http::withToken($accessToken)
                    ->timeout(120)
                    ->withHeaders([
                        'Content-Type' => 'application/gzip',
                        'Content-Length' => (string) $chunkLen,
                        'Content-Range' => "bytes {$offset}-{$rangeEnd}/{$fileSize}",
                    ])
                    ->withBody($chunk, 'application/gzip')
                    ->put($uploadUri);

                $status = $putResponse->status();

                if ($status === 308) {
                    $offset += $chunkLen;
                    continue;
                }

                if ($status >= 200 && $status < 300) {
                    $uploadedMeta = $putResponse->json();
                    break;
                }

                throw new RuntimeException("Google Drive chunk upload failed (HTTP {$status}): " . $putResponse->body());
            }

            if (!is_array($uploadedMeta) || empty($uploadedMeta['id'])) {
                throw new RuntimeException('Google Drive upload completed without returning file metadata.');
            }

            return [
                'id' => (string) $uploadedMeta['id'],
                'name' => (string) ($uploadedMeta['name'] ?? $filename),
                'size' => (int) ($uploadedMeta['size'] ?? $fileSize),
            ];
        } finally {
            fclose($handle);
        }
    }

    /**
     * Delete old backup files beyond the retention count.
     *
     * @return array{kept: int, deleted: int, deleted_ids: array<int, string>}
     */
    public function pruneOldBackups(string $folderId, int $keepCount = 14): array
    {
        $keepCount = max(1, $keepCount);
        $accessToken = $this->getAccessToken();

        $escapedFolder = str_replace("'", "\\'", $folderId);
        $query = "'{$escapedFolder}' in parents and name contains 'acs_backup_' and trashed = false";

        $listResponse = Http::withToken($accessToken)
            ->timeout(30)
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q' => $query,
                'orderBy' => 'createdTime desc',
                'pageSize' => 100,
                'fields' => 'files(id,name,createdTime,size)',
            ]);

        if (!$listResponse->successful()) {
            throw new RuntimeException('Failed to list Google Drive backups for pruning: ' . $listResponse->body());
        }

        $files = $listResponse->json('files', []);
        if (!is_array($files) || count($files) <= $keepCount) {
            return [
                'kept' => is_array($files) ? count($files) : 0,
                'deleted' => 0,
                'deleted_ids' => [],
            ];
        }

        $toDelete = array_slice($files, $keepCount);
        $deletedIds = [];

        foreach ($toDelete as $file) {
            $fileId = (string) ($file['id'] ?? '');
            if ($fileId === '') {
                continue;
            }

            $delResponse = Http::withToken($accessToken)
                ->timeout(30)
                ->delete("https://www.googleapis.com/drive/v3/files/{$fileId}");

            if ($delResponse->successful() || $delResponse->status() === 404) {
                $deletedIds[] = $fileId;
            }
        }

        return [
            'kept' => $keepCount,
            'deleted' => count($deletedIds),
            'deleted_ids' => $deletedIds,
        ];
    }
}
