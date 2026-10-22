<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
