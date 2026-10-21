<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authenticated_user_can_toggle_wishlist()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();

        $response = $this->actingAs($user)->postJson("/wishlist/{$course->id}/toggle");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'added', 'count' => 1]);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Toggle again to remove
        $response2 = $this->actingAs($user)->postJson("/wishlist/{$course->id}/toggle");
        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'removed', 'count' => 0]);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_wishlist_page_renders_saved_courses()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->first();

        $user->wishlists()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)->get('/wishlist');

        $response->assertStatus(200);
        $response->assertSee($course->title);
    }
}
