<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\SiteTraffic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTrafficCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_visits_increment_views_and_unique_visitors_without_double_counting_reload_or_404(): void
    {
        $first = $this->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/126.0'])
            ->get('/');
        $first->assertOk();

        $statsAfterFirst = SiteTraffic::stats();
        $this->assertSame(1, $statsAfterFirst['total_views']);
        $this->assertSame(1, $statsAfterFirst['unique_visitors']);
        $this->assertSame(1, $statsAfterFirst['today_views']);

        // Reloading the exact same page must increment views by 1 per reload, NOT unique_visitors
        $reload = $this->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/126.0'])
            ->get('/');
        $reload->assertOk();

        $statsAfterReload = SiteTraffic::stats();
        $this->assertSame(2, $statsAfterReload['total_views']);
        $this->assertSame(1, $statsAfterReload['unique_visitors']);
        $this->assertSame(2, $statsAfterReload['today_views']);

        // Navigating to another page also increments views by 1, keeping unique_visitors at 1
        $second = $this->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/126.0'])
            ->get('/courses');
        $second->assertOk();

        $statsAfterSecond = SiteTraffic::stats();
        $this->assertSame(3, $statsAfterSecond['total_views']);
        $this->assertSame(1, $statsAfterSecond['unique_visitors']);
        $this->assertSame(3, $statsAfterSecond['today_views']);

        // 404 or non-HTML requests must NOT increment traffic counters
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/126.0'])
            ->get('/non-existent-route-check')
            ->assertNotFound();

        $this->assertSame(3, SiteTraffic::stats()['total_views']);
        $this->assertSame(1, SiteTraffic::stats()['unique_visitors']);
    }

    public function test_bots_and_admin_routes_do_not_increment_public_traffic_and_admin_sees_stats(): void
    {
        $this->withHeaders(['User-Agent' => 'facebookexternalhit/1.1'])
            ->get('/')
            ->assertOk();

        $this->assertSame(0, SiteTraffic::stats()['total_views']);

        $admin = User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/126.0'])
            ->get(route('admin.dashboard'));

        $adminResponse->assertOk();
        $adminResponse->assertSee('Site Traffic');
        $this->assertSame(0, SiteTraffic::stats()['total_views']);

        // Logged-in admin browsing/reloading public pages must also not inflate counts
        $this->actingAs($admin)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 Chrome/126.0'])
            ->get('/')
            ->assertOk();

        $this->assertSame(0, SiteTraffic::stats()['total_views']);
        $this->assertSame(0, SiteTraffic::stats()['unique_visitors']);
    }
}
