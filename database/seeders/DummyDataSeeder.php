<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\JobPost;
use App\Models\PaymentMethod;
use App\Models\Payment;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Users (Generic Institutional Roles)
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@cab-academy.com'],
            [
                'name' => 'Lead Faculty Member',
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'email_verified_at' => now(),
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'student@cab-academy.com'],
            [
                'name' => 'Executive Learner',
                'password' => bcrypt('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        // 2. Courses
        $course1 = Course::firstOrCreate(
            ['title' => 'Certificate Course in Modern Web Architecture'],
            [
                'description' => 'Comprehensive masterclass covering enterprise application design, secure APIs, and responsive architectures.',
                'price' => 49.99,
                'is_published' => true,
                'duration' => '20 Hours',
                'level' => 'Advanced',
                'category' => 'Technology',
                'instructor_id' => $instructor->id,
            ]
        );

        $course2 = Course::firstOrCreate(
            ['title' => 'Executive Visual Design & Branding Strategy'],
            [
                'description' => 'Master institutional design systems, typography hierarchies, and corporate branding identity.',
                'price' => 29.99,
                'is_published' => true,
                'duration' => '15 Hours',
                'level' => 'Beginner',
                'category' => 'Design',
                'instructor_id' => $instructor->id,
            ]
        );

        // 3. Sections & Lessons for Course 1
        if ($course1->sections()->count() === 0) {
            $section1 = $course1->sections()->create(['title' => 'Module 1: Orientation & Foundations', 'order' => 1]);
            $section1->lessons()->create([
                'title' => 'Introduction & Learning Objectives',
                'content' => 'Welcome to the executive program. Review syllabus and setup instructions.',
                'order' => 1,
            ]);
            $section1->lessons()->create([
                'title' => 'Development Workspace Setup',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'order' => 2,
            ]);

            $section2 = $course1->sections()->create(['title' => 'Module 2: Practical Architecture', 'order' => 2]);
            $section2->lessons()->create([
                'title' => 'Core Architecture Patterns',
                'content' => 'Review structural models and design patterns.',
                'order' => 1,
            ]);
        }

        // 4. Job Posts (Generic Institutional Employers)
        JobPost::firstOrCreate(
            ['title' => 'Enterprise Web Specialist'],
            [
                'company' => 'Enterprise Digital Solutions Ltd.',
                'location' => 'Dhaka / Remote',
                'salary_range' => '$40k - $60k',
                'description' => 'Seeking an experienced software professional to lead enterprise web implementations.',
                'apply_link' => '#',
                'is_active' => true,
            ]
        );

        JobPost::firstOrCreate(
            ['title' => 'Senior UI/UX Specialist'],
            [
                'company' => 'Global Digital Product Agency',
                'location' => 'Dhaka / Hybrid',
                'salary_range' => '$70k - $90k',
                'description' => 'Lead institutional product design and user research for digital services.',
                'apply_link' => '#',
                'is_active' => true,
            ]
        );

        // 5. Payment Methods
        $bkash = PaymentMethod::firstOrCreate(
            ['provider_name' => 'bKash'],
            [
                'account_number' => '01917-054664',
                'account_type' => 'Personal',
                'instructions' => 'Send money to this number and enter your TrxID.',
                'is_active' => true,
            ]
        );

        // 6. Enroll Student in Course via Payment
        if (Payment::where('user_id', $student->id)->count() === 0) {
            $payment = Payment::create([
                'user_id' => $student->id,
                'course_id' => $course1->id,
                'payment_method_id' => $bkash->id,
                'sender_number' => '01700000000',
                'transaction_id' => 'TRX' . strtoupper(Str::random(8)),
                'amount' => $course1->price,
                'status' => 'approved',
            ]);

            // Enroll student
            $student->enrolledCourses()->attach($course1->id);
        }
    }
}
