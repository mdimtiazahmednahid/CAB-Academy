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

        // 2. Create Instructor (Md. Delwar Hossain Bhuiyan)
        $instructor = User::firstOrCreate(
            ['email' => 'delwar@ipdpbd.com'],
            [
                'name' => 'Md. Delwar Hossain Bhuiyan',
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'profile_picture' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80'
            ]
        );

        // 3. Create Student
        $student = User::firstOrCreate(
            ['email' => 'student@cab-academy.com'],
            ['name' => 'Alice Student', 'password' => bcrypt('password'), 'role' => 'student']
        );

        // 4. Create IPDP-Style Courses
        $courses = [
            [
                'title' => 'L/C, Banking, Customs & Shipping for Import – Export',
                'description' => 'A comprehensive 8-day masterclass covering letter of credit (L/C), international banking, bill of entry, customs clearance, port logistics, and commercial document processing.',
                'short_description' => 'New Entrepreneurs and professionals will know A to Z Import Export business procedure, documentation, and L/C management.',
                'price' => 9000.00,
                'is_published' => true,
                'level' => 'Executive',
                'category' => 'Export – Import',
                'duration' => '8 Days (Fridays)',
                'start_date' => now()->addDays(2),
                'venue' => 'Mirpur-Pallabi Campus & Online Live via Zoom',
                'target_audience' => 'Importers, Exporters, Supply Chain Executives, Commercial Managers, C&F Agents, Bank Trade Officers, and New Entrepreneurs.',
                'thumbnail' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Commercial Guideline with Banking, Customs, Insurance, C&F Agent, Logistics',
                'description' => 'Hands-on practical workshop covering commercial documentation, HS codes, insurance policies, customs inspection, shipping lines, and bonded warehouse compliance.',
                'short_description' => 'Step-by-step guideline for commercial executives to navigate customs, shipping lines, and bank compliance without penalties.',
                'price' => 7000.00,
                'is_published' => true,
                'level' => 'Certificate',
                'category' => 'Export – Import',
                'duration' => '2-Day Long Workshop',
                'start_date' => now()->addDays(9),
                'venue' => 'Mirpur-Pallabi Campus & Online Live',
                'target_audience' => 'Commercial officers, clearing agents, logistics personnel, and business owners.',
                'thumbnail' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Supply Chain Management & Strategic Sourcing Masterclass',
                'description' => 'Explore vendor negotiation strategies, warehousing 4.0, inventory safety stock modeling, freight forwarding cost reductions, and ERP procurement modules.',
                'short_description' => 'Optimize supply chain flow, minimize lead time, and maximize vendor efficiency across industrial setups.',
                'price' => 8000.00,
                'is_published' => true,
                'level' => 'Executive',
                'category' => 'Supply Chain Management',
                'duration' => '4 Weeks Workshop',
                'start_date' => now()->addDays(14),
                'venue' => 'Online Live Interactive (Zoom)',
                'target_audience' => 'Procurement heads, supply chain managers, warehouse leads, and plant managers.',
                'thumbnail' => 'https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Financial Accounting, VAT Assessment & Corporate Tax Management',
                'description' => 'Learn practical filing of monthly VAT returns, corporate tax calculations, withholding tax certificates, and double-entry auditing standards under current Bangladesh Finance Acts.',
                'short_description' => 'Practical handbook on corporate tax deductions, VAT rebates, and audited financial statements.',
                'price' => 8500.00,
                'is_published' => true,
                'level' => 'Professional',
                'category' => 'Accounting',
                'duration' => '6 Sessions',
                'start_date' => now()->addDays(21),
                'venue' => 'Mirpur-Pallabi Campus / Zoom',
                'target_audience' => 'Accountants, finance managers, tax advisors, auditors, and startup founders.',
                'thumbnail' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Labor Law, HR Compliance & Talent Management',
                'description' => 'Understand Bangladesh Labor Act provisions, grievance management, executive recruitment, salary structuring, and workplace safety compliance standards.',
                'short_description' => 'Essential legal compliance, payroll handling, and dispute resolution for modern HR leaders.',
                'price' => 6000.00,
                'is_published' => true,
                'level' => 'Executive',
                'category' => 'Human Resource',
                'duration' => '3 Days Workshop',
                'start_date' => now()->addDays(28),
                'venue' => 'Mirpur-Pallabi Training Room',
                'target_audience' => 'HR executives, admin managers, factory HR personnel, and managing directors.',
                'thumbnail' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Civil Construction Project Planning, Quality & Cost Estimation',
                'description' => 'Advanced project budgeting, BOQ preparation, contractor agreements, site inspection standards, and Primavera/MS Project tracking for civil engineers.',
                'short_description' => 'From land feasibility to final handover: practical project estimation and QA/QC procedures.',
                'price' => 12000.00,
                'is_published' => true,
                'level' => 'Advanced',
                'category' => 'Civil Construction Project',
                'duration' => '8 Weeks',
                'start_date' => now()->addDays(35),
                'venue' => 'Classroom Mirpur-Pallabi Campus',
                'target_audience' => 'Civil engineers, site supervisors, project directors, real estate developers.',
                'thumbnail' => 'https://images.unsplash.com/photo-1541888946425-d0fbb18615f8?auto=format&fit=crop&w=800&q=80',
            ]
        ];

        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(
                ['title' => $courseData['title']],
                array_merge($courseData, [
                    'instructor_id' => $instructor->id,
                    'views' => rand(150, 950)
                ])
            );

            // Create Sections & Lessons if none exist
            if ($course->sections()->count() == 0) {
                $section1 = Section::create([
                    'course_id' => $course->id,
                    'title' => 'Module 1: Fundamentals & Legal Framework',
                    'order' => 1
                ]);
                Lesson::create([
                    'section_id' => $section1->id,
                    'title' => 'Program Orientation & Key Terminologies',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'order' => 1
                ]);
                Lesson::create([
                    'section_id' => $section1->id,
                    'title' => 'Regulatory Guidelines & Compliance Standards',
                    'content' => 'Review the official government gazettes, forms, and mandatory regulatory checklists.',
                    'order' => 2
                ]);

                $section2 = Section::create([
                    'course_id' => $course->id,
                    'title' => 'Module 2: Practical Case Studies & Documentation',
                    'order' => 2
                ]);
                Lesson::create([
                    'section_id' => $section2->id,
                    'title' => 'Executing Real-World Case Studies',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'order' => 1
                ]);

                // Create a Quiz
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'title' => 'Executive Assessment Quiz',
                    'passing_score' => 70,
                    'time_limit' => 20,
                    'is_published' => true,
                ]);

                $q1 = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => 'Which document is mandatory for customs clearance before port delivery?'
                ]);
                Option::create(['question_id' => $q1->id, 'option_text' => 'Bill of Entry / Bill of Export', 'is_correct' => true]);
                Option::create(['question_id' => $q1->id, 'option_text' => 'Personal Identity Card', 'is_correct' => false]);
                Option::create(['question_id' => $q1->id, 'option_text' => 'Visiting Card', 'is_correct' => false]);
            }
        }

        // 7. Create Job Posts
        if (JobPost::count() == 0) {
            JobPost::create([
                'title' => 'Assistant Manager - Commercial & Shipping',
                'company' => 'Apex Global Logistics',
                'location' => 'Dhaka (Mirpur-Pallabi)',
                'salary_range' => '৳50,000 - ৳70,000',
                'description' => 'Looking for experienced Commercial Officer with strong background in Import-Export, L/C, and Customs.',
                'apply_link' => 'https://example.com/careers',
                'is_active' => true,
            ]);

            JobPost::create([
                'title' => 'Supply Chain Analyst',
                'company' => 'Transcom Logistics',
                'location' => 'Gazipur / Remote',
                'salary_range' => '৳40,000 - ৳60,000',
                'description' => 'Responsible for inventory tracking, procurement audits, and shipping schedule verification.',
                'apply_link' => 'https://example.com/careers',
                'is_active' => true,
            ]);
        }

        // 8. Create Payment Methods (bKash, Nagad, Bank Transfer)
        PaymentMethod::updateOrCreate(
            ['provider_name' => 'bKash Merchant'],
            [
                'account_number' => '01917-054664',
                'account_type' => 'Merchant',
                'instructions' => "1. Go to your bKash app and select 'Make Payment'.\n2. Enter Merchant Number: 01917-054664.\n3. Enter the exact Course Fee.\n4. Enter Reference: Course Title or Your Mobile.\n5. Enter your PIN and confirm.\n6. Copy the TrxID and submit it below.",
                'is_active' => true,
            ]
        );

        PaymentMethod::updateOrCreate(
            ['provider_name' => 'Nagad Personal / Merchant'],
            [
                'account_number' => '01917-054664',
                'account_type' => 'Merchant',
                'instructions' => "1. Dial *167# or open Nagad App.\n2. Select Payment.\n3. Enter Number: 01917-054664.\n4. Input Amount and Reference.\n5. Save TrxID and submit below.",
                'is_active' => true,
            ]
        );

        PaymentMethod::updateOrCreate(
            ['provider_name' => 'Bank Transfer'],
            [
                'account_number' => '102-120-998877',
                'account_type' => 'Corporate Account',
                'instructions' => "Bank Name: Dutch-Bangla Bank Limited (DBBL)\nBranch: Mirpur Branch, Dhaka\nAccount Name: CAB Academy Foundation\nA/C Number: 102-120-998877\nRouting: 090260458\nPlease upload deposit slip or bank receipt.",
                'is_active' => true,
            ]
        );
        
        // 9. Default Global Settings
        Setting::updateOrCreate(['key' => 'site_name'], ['value' => 'CAB Academy']);
        Setting::updateOrCreate(['key' => 'site_logo'], ['value' => '/images/logo.png']);
        Setting::updateOrCreate(['key' => 'primary_color'], ['value' => '#1F6F54']);
        Setting::updateOrCreate(['key' => 'contact_email'], ['value' => 'commercialacademy868@gmail.com']);
        Setting::updateOrCreate(['key' => 'contact_phone'], ['value' => '01917-054664']);
        Setting::updateOrCreate(['key' => 'contact_address'], ['value' => 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216']);
    }
}
