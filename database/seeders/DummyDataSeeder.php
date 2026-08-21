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
        // 1. Users
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@cab-academy.com'],
            [
                'name' => 'John Instructor',
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'email_verified_at' => now(),
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'student@cab-academy.com'],
            [
                'name' => 'Jane Student',
                'password' => bcrypt('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        // 2. Courses
        $course1 = Course::firstOrCreate(
            ['title' => 'Advanced Web Development'],
            [
                'description' => 'Master full-stack web development with modern technologies like Laravel and Vue.js.',
                'price' => 49.99,
                'is_published' => true,
                'duration' => '20 Hours',
                'level' => 'Advanced',
                'category' => 'Programming',
                'instructor_id' => $instructor->id,
            ]
        );

        $course2 = Course::firstOrCreate(
            ['title' => 'Graphic Design Masterclass'],
            [
                'description' => 'Learn Photoshop, Illustrator, and design theory from scratch.',
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
            $section1 = $course1->sections()->create(['title' => 'Getting Started', 'order' => 1]);
            $section1->lessons()->create([
                'title' => 'Introduction to the Course',
                'content' => 'Welcome to Advanced Web Development! Here is what we will cover...',
                'order' => 1,
            ]);
            $section1->lessons()->create([
                'title' => 'Setting up your environment',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'order' => 2,
            ]);

            $section2 = $course1->sections()->create(['title' => 'Deep Dive into Laravel', 'order' => 2]);
            $section2->lessons()->create([
                'title' => 'Routing and Controllers',
                'content' => 'Let\'s learn how routing works in Laravel.',
                'order' => 1,
            ]);
        }

        // 4. Job Posts
        JobPost::firstOrCreate(
            ['title' => 'Junior Web Developer'],
            [
                'company' => 'Tech Corp Inc.',
                'location' => 'Remote',
                'salary_range' => '$40k - $60k',
                'description' => 'We are looking for a passionate Junior Web Developer to join our fast-growing team.',
                'apply_link' => 'https://example.com/apply',
                'is_active' => true,
            ]
        );

        JobPost::firstOrCreate(
            ['title' => 'UI/UX Designer'],
            [
                'company' => 'Creative Studios',
                'location' => 'New York, NY',
                'salary_range' => '$70k - $90k',
                'description' => 'Join us as a lead designer and shape the future of our digital products.',
                'apply_link' => 'https://example.com/apply',
                'is_active' => true,
            ]
        );

        // 5. Payment Methods
        $bkash = PaymentMethod::firstOrCreate(
            ['provider_name' => 'bKash'],
            [
                'account_number' => '01711111111',
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
                'sender_number' => '01712345678',
                'transaction_id' => 'TRX' . strtoupper(Str::random(8)),
                'amount' => $course1->price,
                'status' => 'approved',
            ]);

            // Enroll student
            $student->enrolledCourses()->attach($course1->id);
        }
    }
}
