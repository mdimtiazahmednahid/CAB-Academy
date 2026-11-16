<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaManifestAndOfflineTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_manifest_endpoint_returns_valid_manifest_with_icons_and_shortcuts(): void
    {
        $response = $this->get('/manifest.json');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertJsonStructure([
                'name',
                'short_name',
                'start_url',
                'scope',
                'display',
                'background_color',
                'theme_color',
                'icons' => [
                    ['src', 'sizes', 'type', 'purpose'],
                ],
                'shortcuts' => [
                    ['name', 'short_name', 'url'],
                ],
            ]);
    }

    public function test_pwa_offline_fallback_and_service_worker_assets_exist(): void
    {
        $this->get('/offline')
            ->assertOk()
            ->assertSee("You're Currently Offline", false)
            ->assertSee('Retry Connection');

        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('images/icons/icon-180x180.png'));
        $this->assertFileExists(public_path('images/icons/icon-192x192.png'));
        $this->assertFileExists(public_path('images/icons/icon-512x512.png'));
        $this->assertFileExists(public_path('images/icons/icon-maskable-512x512.png'));
    }
}
