<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $course = Course::where('is_published', true)->first();
        $response = $this->post(route('courses.enroll', $course), []);
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_enroll_in_free_course_directly()
    {
        $user = User::factory()->create();
        $course = Course::create([
            'title' => 'Free Orientation Masterclass',
            'slug' => 'free-orientation-masterclass-' . uniqid(),
            'description' => 'Free introduction module',
            'price' => 0.00,
            'is_published' => true,
        ]);

        $this->actingAs($user);
        $response = $this->post(route('courses.enroll', $course), []);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'You have successfully enrolled in the course!');
        $this->assertTrue($user->fresh()->enrolledCourses->contains($course));
    }

    public function test_authenticated_user_is_redirected_to_checkout_for_paid_course()
    {
        $user = User::factory()->create();
        $course = Course::where('is_published', true)->where('price', '>', 0)->first();

        $this->actingAs($user);
        $response = $this->post(route('courses.enroll', $course), []);
        $response->assertRedirect(route('courses.checkout', $course));
        $this->assertFalse($user->fresh()->enrolledCourses->contains($course));
    }
}
