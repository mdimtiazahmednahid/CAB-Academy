<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\JobPost;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Admin Exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@cab-academy.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        // 2. Create Instructor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@cab-academy.com'],
            ['name' => 'John Instructor', 'password' => bcrypt('password'), 'role' => 'instructor']
        );

        // 3. Create Student
        $student = User::firstOrCreate(
            ['email' => 'student@cab-academy.com'],
            ['name' => 'Alice Student', 'password' => bcrypt('password'), 'role' => 'student']
        );

        // 4. Create Courses
        $courses = [
            [
                'title' => 'Full Stack Web Development (MERN)',
                'description' => 'Master MongoDB, Express, React, and Node.js. Build production-ready web applications from scratch.',
                'price' => 299.99,
                'is_published' => true,
                'level' => 'intermediate',
                'category' => 'Web Development',
            ],
            [
                'title' => 'UI/UX Design Masterclass',
                'description' => 'Learn Figma, user research, wireframing, and prototyping to design stunning user interfaces.',
                'price' => 149.99,
                'is_published' => true,
                'level' => 'beginner',
                'category' => 'Design',
            ],
            [
                'title' => 'Advanced Data Science with Python',
                'description' => 'Dive deep into pandas, scikit-learn, and TensorFlow for advanced machine learning models.',
                'price' => 399.00,
                'is_published' => false,
                'level' => 'advanced',
                'category' => 'Data Science',
            ]
        ];

        foreach ($courses as $courseData) {
            $course = Course::firstOrCreate(
                ['title' => $courseData['title']],
                array_merge($courseData, [
                    'instructor_id' => $instructor->id,
                    'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80',
                    'cover_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1200&q=80',
                    'duration' => rand(10, 50),
                    'views' => rand(100, 1000)
                ])
            );

            // 5. Create Sections & Lessons if published
            if ($course->is_published && $course->sections()->count() == 0) {
                // Section 1
                $section1 = Section::create(['course_id' => $course->id, 'title' => 'Getting Started', 'order' => 1]);
                Lesson::create(['section_id' => $section1->id, 'title' => 'Introduction', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'order' => 1]);
                Lesson::create(['section_id' => $section1->id, 'title' => 'Setup Guide', 'content' => 'Please install the required tools before proceeding.', 'order' => 2]);

                // Section 2
                $section2 = Section::create(['course_id' => $course->id, 'title' => 'Core Concepts', 'order' => 2]);
                Lesson::create(['section_id' => $section2->id, 'title' => 'Fundamentals', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'order' => 1]);
                
                // 6. Create a Quiz
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'title' => 'Core Concepts Quiz',
                    'passing_score' => 80,
                    'time_limit' => 15, // minutes
                ]);

                $q1 = Question::create(['quiz_id' => $quiz->id, 'question_text' => 'Which of the following is correct?']);
                Option::create(['question_id' => $q1->id, 'option_text' => 'Option A', 'is_correct' => false]);
                Option::create(['question_id' => $q1->id, 'option_text' => 'Option B (Correct)', 'is_correct' => true]);
                Option::create(['question_id' => $q1->id, 'option_text' => 'Option C', 'is_correct' => false]);

                $q2 = Question::create(['quiz_id' => $quiz->id, 'question_text' => 'Is coding fun?']);
                Option::create(['question_id' => $q2->id, 'option_text' => 'Yes', 'is_correct' => true]);
                Option::create(['question_id' => $q2->id, 'option_text' => 'No', 'is_correct' => false]);
            }
        }

        // 7. Create Job Posts
        if (JobPost::count() == 0) {
            JobPost::create([
                'title' => 'Frontend React Engineer',
                'company' => 'NeuraSoft Inc',
                'location' => 'Remote',
                'salary_range' => '$80k - $120k',
                'description' => 'Looking for an experienced React developer to join our fast-growing team. 3+ years React experience required.',
                'apply_link' => 'https://neurasoft.top/careers',
                'is_active' => true,
            ]);

            JobPost::create([
                'title' => 'Product Designer',
                'company' => 'DesignStudio',
                'location' => 'New York, NY',
                'salary_range' => '$60/hr',
                'description' => 'Contract position for a senior product designer to help revamp our SaaS platform. Expert in Figma required.',
                'apply_link' => 'https://designstudio.com/jobs',
                'is_active' => true,
            ]);
        }

        // 8. Create Payment Methods
        if (PaymentMethod::count() == 0) {
            PaymentMethod::create([
                'provider_name' => 'Bank Transfer',
                'account_number' => '123456789',
                'account_type' => 'Corporate',
                'instructions' => "Transfer directly to our corporate bank account.\nBank: World Bank\nRouting: 987654321\nPlease include your order ID in the reference.",
                'is_active' => true,
            ]);
        }
        
        // 9. Default Global Settings
        if (Setting::count() < 3) {
            Setting::updateOrCreate(['key' => 'site_name'], ['value' => 'CAB Academy']);
            Setting::updateOrCreate(['key' => 'primary_color'], ['value' => '#1F6F54']);
            Setting::updateOrCreate(['key' => 'contact_email'], ['value' => 'support@cab-academy.com']);
        }

    }
}
