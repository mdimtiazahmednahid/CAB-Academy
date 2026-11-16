<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\DatabaseDumper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleDriveBackupTest extends TestCase
{
    use RefreshDatabase;

    private string $tempBackupDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempBackupDir = storage_path('framework/testing/backups_' . uniqid());
        Config::set('backup.temp_directory', $this->tempBackupDir);
        Config::set('backup.enabled', true);
        Config::set('backup.retention_count', 2);
        Config::set('backup.cron_token', 'test-cron-secret-token');
        Config::set('backup.google_drive', [
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'refresh_token' => 'test-refresh-token',
            'folder_id' => 'folder-123',
        ]);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempBackupDir)) {
            foreach (glob($this->tempBackupDir . '/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($this->tempBackupDir);
        }

        parent::tearDown();
    }

    public function test_database_dumper_generates_valid_gzipped_sql(): void
    {
        User::factory()->create(['email' => 'dump-check@example.com']);

        $dump = app(DatabaseDumper::class)->dumpToGzip();

        $this->assertFileExists($dump['path']);
        $this->assertGreaterThan(0, $dump['size_bytes']);
        $this->assertStringEndsWith('.sql.gz', $dump['filename']);

        $sql = gzdecode((string) file_get_contents($dump['path']));
        $this->assertIsString($sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertStringContainsString('dump-check@example.com', $sql);
    }

    public function test_backup_command_uploads_prunes_and_cleans_temp_file(): void
    {
        $this->fakeGoogleDriveHttp();

        $this->artisan('backup:google-drive')
            ->assertExitCode(0);

        // Local temp file must be cleaned up automatically
        $remaining = is_dir($this->tempBackupDir) ? (glob($this->tempBackupDir . '/*.sql.gz') ?: []) : [];
        $this->assertCount(0, $remaining);

        // Old backup beyond retention_count=2 ('old-file-3') must be deleted
        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && str_contains($request->url(), 'https://www.googleapis.com/drive/v3/files/old-file-3'));

        Http::assertNotSent(fn ($request) => $request->method() === 'DELETE'
            && str_contains($request->url(), 'old-file-2'));

        $historyJson = Setting::where('key', 'google_drive_backup_history')->value('value');
        $history = json_decode((string) $historyJson, true);
        $this->assertIsArray($history);
        $this->assertSame('success', $history[0]['status']);
        $this->assertSame('new-drive-file-id', $history[0]['drive_file_id']);
        $this->assertSame(1, $history[0]['pruned_count']);
    }

    public function test_admin_backup_now_endpoint_enforces_strict_admin_authorization(): void
    {
        $this->fakeGoogleDriveHttp();

        $this->post(route('admin.settings.backup_now'))
            ->assertRedirect(route('login'));

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)
            ->post(route('admin.settings.backup_now'))
            ->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->from(route('admin.settings'))
            ->post(route('admin.settings.backup_now'))
            ->assertRedirect(route('admin.settings'))
            ->assertSessionHas('success');
    }

    public function test_web_cron_endpoint_requires_valid_token(): void
    {
        $this->fakeGoogleDriveHttp();

        $this->get('/cron/backup-google-drive')->assertForbidden();
        $this->get('/cron/backup-google-drive?token=wrong-token')->assertForbidden();

        $this->getJson('/cron/backup-google-drive?token=test-cron-secret-token')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    private function fakeGoogleDriveHttp(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'fake-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ], 200),
            'https://www.googleapis.com/upload/drive/v3/files*' => Http::response(
                '',
                200,
                ['Location' => 'https://upload.example.test/resumable-session-1']
            ),
            'https://upload.example.test/resumable-session-1' => Http::response([
                'id' => 'new-drive-file-id',
                'name' => 'acs_backup_test.sql.gz',
                'size' => '1024',
            ], 200),
            'https://www.googleapis.com/drive/v3/files?*' => Http::response([
                'files' => [
                    ['id' => 'new-drive-file-id', 'name' => 'acs_backup_3.sql.gz'],
                    ['id' => 'old-file-2', 'name' => 'acs_backup_2.sql.gz'],
                    ['id' => 'old-file-3', 'name' => 'acs_backup_1.sql.gz'],
                ],
            ], 200),
            'https://www.googleapis.com/drive/v3/files/old-file-3' => Http::response('', 204),
        ]);
    }
}
