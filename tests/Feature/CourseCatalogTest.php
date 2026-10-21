<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_view_public_course_catalog(): void
    {
        $response = $this->get('/catalog');
        $response->assertStatus(200);
        $response->assertSee('Course Categories');
        $response->assertSee('L/C, Banking, Customs');
    }

    public function test_courses_alias_route_works(): void
    {
        $response = $this->get('/courses');
        $response->assertStatus(200);
    }

    public function test_guest_can_filter_by_export_import_category(): void
    {
        $response = $this->get('/category/export-import');
        $response->assertStatus(200);
        $response->assertSee('Export – Import');
        $response->assertSee('L/C, Banking, Customs');
    }

    public function test_export_import_direct_alias_redirects(): void
    {
        $response = $this->get('/export-import');
        $response->assertRedirect('/category/export-import');
    }

    public function test_guest_can_view_single_course_overview(): void
    {
        $course = Course::where('is_published', true)->first();
        $this->assertNotNull($course);

        $response = $this->get("/courses/{$course->id}");
        $response->assertStatus(200);
        $response->assertSee($course->title);
        $response->assertSee('Course Curriculum');
        $response->assertSee('Enroll Now');
    }

    public function test_guest_clicking_enroll_redirects_to_login(): void
    {
        $course = Course::where('is_published', true)->first();
        $response = $this->post("/courses/{$course->id}/enroll");

        $response->assertRedirect(route('login'));
        $this->assertEquals(route('courses.show', $course), session('url.intended'));
    }

    public function test_enrolled_student_can_see_learning_player_access(): void
    {
        $student = User::where('role', 'student')->first();
        $course = Course::where('is_published', true)->first();
        
        $student->enrolledCourses()->attach($course->id);

        $response = $this->actingAs($student)->get("/courses/{$course->id}");
        $response->assertStatus(200);
        $response->assertSee('Your Learning Progress');
        $response->assertSee('Enrolled in Course');
    }

    public function test_storage_streaming_route_serves_files_without_symlinks(): void
    {
        $testPath = storage_path('app/public/test_folder');
        if (!file_exists($testPath)) {
            mkdir($testPath, 0755, true);
        }
        file_put_contents($testPath . '/sample.txt', 'Hello InfinityFree');

        $response = $this->get('/storage/test_folder/sample.txt');
        $response->assertStatus(200);
        $this->assertEquals('Hello InfinityFree', $response->streamedContent());

        // Cleanup
        unlink($testPath . '/sample.txt');
        rmdir($testPath);
    }

    public function test_guest_can_express_enroll_with_instant_seat_reservation(): void
    {
        $course = Course::where('is_published', true)->first();

        $response = $this->postJson("/courses/{$course->id}/express-enroll", [
            'name' => 'Fahim Rahman',
            'phone' => '01917054664',
            'email' => 'fahim@example.com',
            'training_mode' => 'in_person',
            'payment_method_id' => 2,
            'sender_number' => '01917054664',
            'transaction_id' => 'BKASH9921',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fahim@example.com',
            'name' => 'Fahim Rahman',
        ]);

        $this->assertDatabaseHas('payments', [
            'sender_number' => '01917054664',
            'transaction_id' => 'BKASH9921',
            'course_id' => $course->id,
        ]);
    }

    public function test_authenticated_user_can_express_enroll(): void
    {
        $student = User::where('role', 'student')->first();
        $course = Course::where('is_published', true)->first();

        $response = $this->actingAs($student)->postJson("/courses/{$course->id}/express-enroll", [
            'name' => $student->name,
            'phone' => '01917054664',
            'training_mode' => 'online_zoom',
            'payment_method_id' => 2,
            'sender_number' => '01917054664',
            'transaction_id' => 'ZOOM-ENROLL-123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'transaction_id' => 'ZOOM-ENROLL-123',
        ]);
    }
}
