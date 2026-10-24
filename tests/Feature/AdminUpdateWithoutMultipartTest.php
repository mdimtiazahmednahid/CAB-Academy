<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUpdateWithoutMultipartTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@cab-academy.com',
            'name' => 'Original Admin Name',
        ]);
    }

    public function test_admin_can_update_profile_name_via_standard_post(): void
    {
        $response = $this->actingAs($this->admin)->post(route('profile.update'), [
            'name' => 'Updated Executive Admin',
            'email' => 'admin@cab-academy.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Updated Executive Admin',
        ]);
    }

    public function test_admin_can_update_instructor_via_standard_post(): void
    {
        $instructor = Instructor::create([
            'name' => 'Old Instructor Name',
            'title' => 'Associate Fellow',
            'expertise' => 'Customs & Port Procedure',
            'is_active' => true,
            'order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.instructors.update', $instructor), [
            'name' => 'Faculty Chair – Trade & Customs',
            'title' => 'Senior Fellow',
            'expertise' => 'Export – Import Law',
            'is_active' => '1',
            'order' => 2,
        ]);

        $response->assertRedirect(route('admin.instructors.index'));
        $this->assertDatabaseHas('instructors', [
            'id' => $instructor->id,
            'name' => 'Faculty Chair – Trade & Customs',
            'title' => 'Senior Fellow',
        ]);
    }

    public function test_admin_can_update_blog_via_standard_post(): void
    {
        $blog = BlogPost::create([
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'category' => 'Customs',
            'excerpt' => 'Initial excerpt',
            'content' => 'Initial body content',
            'author_name' => 'Faculty Member',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.blogs.update', $blog), [
            'title' => 'Updated Article Title',
            'category' => 'Customs',
            'excerpt' => 'Updated excerpt text',
            'content' => 'Updated body content here',
            'author_name' => 'Senior Faculty Fellow',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $this->assertDatabaseHas('blog_posts', [
            'id' => $blog->id,
            'title' => 'Updated Article Title',
        ]);
    }

    public function test_admin_can_create_course_with_base64_cover_image(): void
    {
        $dummyImageBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => 'Executive Customs Clearance Masterclass',
            'short_description' => 'Comprehensive customs automation and compliance guide.',
            'category' => 'customs',
            'level' => 'Advanced',
            'price' => 5000,
            'duration' => '12 Hours',
            'cover_image_base64' => $dummyImageBase64,
            'is_published' => '1',
        ]);

        $response->assertSessionHas('success');
        $course = Course::where('title', 'Executive Customs Clearance Masterclass')->first();
        $this->assertNotNull($course);
        $this->assertNotNull($course->cover_image);
        $this->assertStringStartsWith('course_covers/', $course->cover_image);
        $this->assertStringContainsString('/storage/course_covers/', $course->cover_image_url);
    }

    public function test_admin_can_update_course_with_base64_cover_image_and_instructor(): void
    {
        $instructorUser = User::factory()->create([
            'role' => 'instructor',
            'name' => 'Dr. Farhana Rahman',
            'email' => 'farhana@cab-academy.com',
        ]);

        $course = Course::create([
            'title' => 'Original Course Title',
            'slug' => 'original-course-title',
            'price' => 3000,
            'is_published' => true,
        ]);

        $dummyImageBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->admin)->post(route('admin.courses.update', $course), [
            'title' => 'Updated Export Logistics Masterclass',
            'short_description' => 'Updated logistics course syllabus.',
            'category' => 'export-import',
            'instructor_id' => $instructorUser->id,
            'cover_image_base64' => $dummyImageBase64,
            'is_published' => '1',
        ]);

        $course->refresh();
        $this->assertEquals('Updated Export Logistics Masterclass', $course->title);
        $this->assertEquals($instructorUser->id, $course->instructor_id);
        $this->assertNotNull($course->cover_image);
        $this->assertStringStartsWith('course_covers/', $course->cover_image);
    }

    public function test_admin_instructor_update_syncs_with_linked_user(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'name' => 'Original Faculty Name',
            'email' => 'faculty@cab-academy.com',
            'profile_picture' => null,
        ]);

        $instructor = Instructor::create([
            'user_id' => $user->id,
            'name' => 'Original Faculty Name',
            'email' => 'faculty@cab-academy.com',
            'title' => 'Faculty Fellow',
            'expertise' => 'Supply Chain Law',
            'is_active' => true,
            'order' => 1,
        ]);

        $dummyAvatarBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->admin)->post(route('admin.instructors.update', $instructor), [
            'name' => 'Prof. Tariqul Islam, PhD',
            'email' => 'faculty@cab-academy.com',
            'title' => 'Chief Academic Advisor & Senior Fellow',
            'expertise' => 'International Trade & Port Logistics',
            'avatar_base64' => $dummyAvatarBase64,
            'is_active' => '1',
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.instructors.index'));
        $instructor->refresh();
        $user->refresh();

        $this->assertEquals('Prof. Tariqul Islam, PhD', $instructor->name);
        $this->assertEquals('Prof. Tariqul Islam, PhD', $user->name);
        $this->assertNotNull($instructor->avatar);
        $this->assertEquals($instructor->avatar, $user->profile_picture);
        $this->assertStringContainsString('/storage/instructors/', $instructor->avatar_url);
    }

    public function test_landing_page_renders_dynamic_faculty_section(): void
    {
        Instructor::create([
            'name' => 'Distinguished Senior Fellow Test',
            'title' => 'Executive Director, Port Operations',
            'expertise' => 'Customs & Port Logistics',
            'is_active' => true,
            'order' => 1,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Distinguished Faculty &amp; Industry Fellows', false);
        $response->assertSee('Distinguished Senior Fellow Test');
        $response->assertSee('Executive Director, Port Operations');
    }

    public function test_admin_can_upload_course_cover_via_multipart_file(): void
    {
        Storage::fake('public');

        $course = Course::create([
            'title' => 'Course For Multipart Test',
            'price' => 2500,
        ]);

        $file = UploadedFile::fake()->image('cover_masterclass.jpg', 1200, 800);

        $response = $this->actingAs($this->admin)->post(route('admin.courses.update', $course), [
            'title' => 'Course For Multipart Test Updated',
            'price' => 3000,
            'cover_image' => $file,
        ]);

        $response->assertSessionHas('success');
        $course->refresh();

        $this->assertNotNull($course->cover_image);
        $this->assertStringStartsWith('course_covers/', $course->cover_image);
        Storage::disk('public')->assertExists($course->cover_image);
    }

    public function test_admin_can_upload_instructor_avatar_via_multipart_file(): void
    {
        Storage::fake('public');

        $instructor = Instructor::create([
            'name' => 'Faculty Fellow Multipart',
            'title' => 'Customs Advisor',
            'expertise' => 'Trade Finance',
            'is_active' => true,
            'order' => 1,
        ]);

        $file = UploadedFile::fake()->image('faculty_headshot.png', 400, 400);

        $response = $this->actingAs($this->admin)->post(route('admin.instructors.update', $instructor), [
            'name' => 'Faculty Fellow Multipart Updated',
            'title' => 'Senior Customs Advisor',
            'expertise' => 'Trade Finance & Compliance',
            'avatar_file' => $file,
            'is_active' => '1',
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.instructors.index'));
        $instructor->refresh();

        $this->assertNotNull($instructor->avatar);
        $this->assertStringStartsWith('instructors/', $instructor->avatar);
        Storage::disk('public')->assertExists($instructor->avatar);
    }
}
