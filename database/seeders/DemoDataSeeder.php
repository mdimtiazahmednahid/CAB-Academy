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
use App\Models\Instructor;
use App\Models\Category;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Official ACS 4 Core Learning Departments & Legacy Category Mappings
        Category::syncCoreDepartments();

        // 1. Ensure Admin and Academy Director Exist with role = 'admin'
        $admin = User::withTrashed()->where('email', 'admin@cab-academy.com')->first();
        if ($admin) {
            $admin->restore();
            $admin->update(['name' => 'Academy Administrator', 'role' => 'admin', 'email_verified_at' => now()]);
        } else {
            $admin = User::create([
                'name' => 'Academy Administrator',
                'email' => 'admin@cab-academy.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        $owner = User::withTrashed()->where('email', 'commercialacademy868@gmail.com')->first();
        if ($owner) {
            $owner->restore();
            $owner->update(['name' => 'Academy Director', 'role' => 'admin', 'email_verified_at' => now()]);
        } else {
            User::create([
                'name' => 'Academy Director',
                'email' => 'commercialacademy868@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        // 2. Create Faculty User Accounts & Instructor Profiles (4 Learning Department Heads)
        $facultyDefinitions = [
            [
                'email' => 'trade.faculty@cab-academy.com',
                'name' => 'Faculty Chair – Commercial Management',
                'title' => 'Department Head – Commercial Management',
                'department' => 'Commercial Management',
                'department_role' => 'head',
                'expertise' => 'Export & Import, LC & Trade Finance, Customs & Bond Management',
                'avatar' => null,
                'bio' => 'Leads the Commercial Management Learning Department, guiding international trade, LC finance, customs & bond regulations, and shipping documentation.',
                'phone' => '01917-054664',
                'order' => 1,
            ],
            [
                'email' => 'scm.faculty@cab-academy.com',
                'name' => 'Faculty Chair – Supply Chain',
                'title' => 'Department Head – Supply Chain Management',
                'department' => 'Supply Chain Management',
                'department_role' => 'head',
                'expertise' => 'Procurement, Logistics, Warehouse, Planning & Supply Chain Analytics',
                'avatar' => null,
                'bio' => 'Leads the Supply Chain Management Learning Department, specializing in strategic sourcing, freight logistics, production planning, and supply chain analytics.',
                'phone' => '01917-054664',
                'order' => 2,
            ],
            [
                'email' => 'tax.faculty@cab-academy.com',
                'name' => 'Faculty Chair – Career Development',
                'title' => 'Department Head – Career Development',
                'department' => 'Career Development',
                'department_role' => 'head',
                'expertise' => 'Career Planning, CV & LinkedIn, Interview Prep & Job Readiness',
                'avatar' => null,
                'bio' => 'Leads the Career Development Learning Department, mentoring professionals in career planning, executive CV & LinkedIn branding, interview preparation, and corporate job readiness.',
                'phone' => '01917-054664',
                'order' => 3,
            ],
            [
                'email' => 'law.faculty@cab-academy.com',
                'name' => 'Faculty Chair – Leadership & Skills',
                'title' => 'Department Head – Professional Skills & Leadership Development',
                'department' => 'Professional Skills & Leadership Development',
                'department_role' => 'head',
                'expertise' => 'Business Communication, Team Leadership, Negotiation & Corporate Etiquette',
                'avatar' => null,
                'bio' => 'Leads the Professional Skills & Leadership Development Department, training executives in business communication, negotiation, public speaking, and strategic decision making.',
                'phone' => '01917-054664',
                'order' => 4,
            ],
        ];

        $facultyUsers = [];
        foreach ($facultyDefinitions as $f) {
            $user = User::withTrashed()->where('email', $f['email'])->first();
            if ($user) {
                $user->restore();
                $user->update([
                    'name' => $f['name'],
                    'role' => 'instructor',
                    'phone' => $f['phone'],
                    'email_verified_at' => now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $f['name'],
                    'email' => $f['email'],
                    'password' => bcrypt('password'),
                    'role' => 'instructor',
                    'phone' => $f['phone'],
                    'email_verified_at' => now(),
                ]);
            }
            $facultyUsers[$f['email']] = $user;

            $instData = [
                'user_id' => $user->id,
                'name' => $f['name'],
                'title' => $f['title'],
                'expertise' => $f['expertise'],
                'avatar' => $f['avatar'],
                'bio' => $f['bio'],
                'phone' => $f['phone'],
                'order' => $f['order'],
                'is_active' => true,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('instructors', 'department')) {
                $instData['department'] = $f['department'];
                $instData['department_role'] = $f['department_role'];
            }

            $inst = Instructor::withTrashed()->updateOrCreate(
                ['email' => $f['email']],
                $instData
            );
            if ($inst->trashed()) {
                $inst->restore();
            }
        }

        // Re-sync Core Departments so head_instructor_id is linked to each Department Head
        Category::syncCoreDepartments();

        // Purge any legacy user with real/personal names from earlier seeders
        User::withTrashed()->where('email', 'delwar@cabacademy.com')->forceDelete();

        // 3. Create Student (Generic Professional Learner)
        $student = User::updateOrCreate(
            ['email' => 'student@cab-academy.com'],
            [
                'name' => 'Executive Learner',
                'password' => bcrypt('password'),
                'role' => 'student',
                'email_verified_at' => now(),
                'preferences' => ['level' => 'Certificate', 'subjects' => ['Export – Import'], 'goals' => ['Certification']]
            ]
        );

        // 4. Create 8 Executive Courses (4 Certificate Programs + 4 Day Long Masterclasses)
        $courses = [
            // 4 Certificate Courses
            [
                'title' => 'Certificate Course in L/C, Banking, Customs & Shipping for Import – Export',
                'description' => 'A comprehensive 8-week executive certificate program designed for entrepreneurs, commercial managers, and international trade professionals covering letter of credit (L/C), international banking, bill of entry, customs clearance, port logistics, and commercial document processing.',
                'short_description' => 'Master complete import-export documentation, commercial banking, L/C issuance, port clearance, and customs regulations.',
                'price' => 9000.00,
                'is_published' => true,
                'level' => 'Certificate',
                'category' => 'Export – Import',
                'duration' => '8 Weeks (Every Friday)',
                'start_date' => now()->addDays(3),
                'venue' => 'Online Live Interactive (Zoom)',
                'target_audience' => 'Importers, Exporters, Supply Chain Executives, Commercial Managers, C&F Agents, Bank Trade Officers, and New Entrepreneurs.',
                'thumbnail' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'trade.faculty@cab-academy.com',
            ],
            [
                'title' => 'Certificate Course in Supply Chain Management & Strategic Sourcing',
                'description' => 'Explore end-to-end modern supply chain logistics, supplier scorecards, ERP procurement modules, inventory safety stock modeling, freight forwarding cost reductions, and international freight contracts.',
                'short_description' => 'Optimize supply chain flow, minimize lead time, safety stock modeling, and maximize vendor efficiency across industrial setups.',
                'price' => 8500.00,
                'is_published' => true,
                'level' => 'Certificate',
                'category' => 'Supply Chain Management',
                'duration' => '6 Weeks Workshop',
                'start_date' => now()->addDays(7),
                'venue' => 'Online Live Interactive (Zoom)',
                'target_audience' => 'Procurement heads, supply chain managers, warehouse leads, logistics officers, and plant managers.',
                'thumbnail' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'scm.faculty@cab-academy.com',
            ],
            [
                'title' => 'Certificate Course in Financial Accounting, VAT & Corporate Tax Management',
                'description' => 'Hands-on filing of monthly VAT returns, corporate tax calculations, withholding tax certificates, and double-entry auditing standards under current Bangladesh Finance Acts.',
                'short_description' => 'Practical handbook on corporate tax deductions, monthly VAT returns, withholding tax certificates, and audited financial statements.',
                'price' => 8000.00,
                'is_published' => true,
                'level' => 'Certificate',
                'category' => 'Accounting',
                'duration' => '8 Sessions (Fridays)',
                'start_date' => now()->addDays(12),
                'venue' => 'Online Live Interactive (Zoom)',
                'target_audience' => 'Accountants, finance managers, tax advisors, auditors, and startup founders.',
                'thumbnail' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'tax.faculty@cab-academy.com',
            ],
            [
                'title' => 'Certificate Course in Corporate Governance, Company Law & Secretarial Practice',
                'description' => 'In-depth mastery of the Companies Act 1994, board meetings and resolution drafting, corporate filings, shareholder agreements, RJSC compliance, and corporate compliance audit frameworks.',
                'short_description' => 'Comprehensive legal and compliance curriculum for company secretaries, legal counsels, and corporate directors navigating RJSC filings and statutory audits.',
                'price' => 9500.00,
                'is_published' => true,
                'level' => 'Certificate',
                'category' => 'Law & Compliance',
                'duration' => '6 Weeks (Fridays)',
                'start_date' => now()->addDays(16),
                'venue' => 'Online Live Interactive (Zoom)',
                'target_audience' => 'Company secretaries, corporate advocates, compliance managers, and executive directors.',
                'thumbnail' => 'https://images.unsplash.com/photo-1453733197783-64768dd7467b?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1453733197783-64768dd7467b?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'law.faculty@cab-academy.com',
            ],

            // 4 Day Long Masterclasses
            [
                'title' => 'Commercial Guideline with Customs, C&F Agent & Shipping Procedures (Day-Long)',
                'description' => 'Practical one-day intensive workshop covering commercial documentation, HS codes, insurance policies, customs inspection, shipping lines, and bonded warehouse compliance.',
                'short_description' => 'Step-by-step guideline for commercial executives to navigate customs, shipping lines, insurance policies, and bonded warehouses without penalties.',
                'price' => 3500.00,
                'is_published' => true,
                'level' => 'Day Long',
                'category' => 'Export – Import',
                'duration' => 'Full Day Workshop (9 AM - 6 PM)',
                'start_date' => now()->addDays(5),
                'venue' => 'Mirpur-Pallabi Campus & Online Live',
                'target_audience' => 'Commercial officers, clearing agents, logistics personnel, and business owners.',
                'thumbnail' => 'https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'trade.faculty@cab-academy.com',
            ],
            [
                'title' => 'Labor Law, HR Compliance & Industrial Disputes Masterclass (Day-Long)',
                'description' => 'Understand Bangladesh Labor Act provisions, grievance management, domestic inquiries, executive recruitment, salary structuring, and workplace safety compliance standards.',
                'short_description' => 'Essential legal compliance, payroll handling, domestic inquiry procedures, and dispute resolution for modern HR leaders.',
                'price' => 3500.00,
                'is_published' => true,
                'level' => 'Day Long',
                'category' => 'Human Resource',
                'duration' => 'Full Day Intensive (9 AM - 6 PM)',
                'start_date' => now()->addDays(10),
                'venue' => 'Mirpur-Pallabi Training Room',
                'target_audience' => 'HR executives, admin managers, factory HR personnel, and managing directors.',
                'thumbnail' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'law.faculty@cab-academy.com',
            ],
            [
                'title' => 'Career Planning, Executive CV & LinkedIn Branding Masterclass (Day-Long)',
                'description' => 'Intensive career acceleration workshop covering strategic career planning, ATS-optimized CV writing, LinkedIn executive branding, mock interview preparation, and corporate job readiness.',
                'short_description' => 'Build an executive CV and LinkedIn profile, master corporate interview preparation, and accelerate your career growth with mentorship.',
                'price' => 2500.00,
                'is_published' => true,
                'level' => 'Day Long',
                'category' => 'Career Development',
                'duration' => 'Full Day Masterclass (9 AM - 6 PM)',
                'start_date' => now()->addDays(15),
                'venue' => 'Online Live Interactive (Zoom)',
                'target_audience' => 'University graduates, management trainees, mid-career executives, and professionals seeking career advancement.',
                'thumbnail' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'tax.faculty@cab-academy.com',
            ],
            [
                'title' => 'Business Communication, Leadership & Corporate Negotiation Masterclass (Day-Long)',
                'description' => 'Comprehensive executive training on workplace business communication, team leadership, persuasive presentation & public speaking, commercial negotiation skills, strategic decision making, and corporate etiquette.',
                'short_description' => 'Master business communication, team management, public speaking, negotiation skills, and executive decision making.',
                'price' => 3000.00,
                'is_published' => true,
                'level' => 'Day Long',
                'category' => 'Professional Skills & Leadership Development',
                'duration' => 'Full Day Workshop (9 AM - 6 PM)',
                'start_date' => now()->addDays(20),
                'venue' => 'Mirpur-Pallabi Campus & Online Live',
                'target_audience' => 'Team leaders, corporate managers, commercial executives, and emerging organizational leaders.',
                'thumbnail' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80',
                'cover_image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80',
                'instructor_email' => 'law.faculty@cab-academy.com',
            ],
        ];

        foreach ($courses as $courseData) {
            $instEmail = $courseData['instructor_email'] ?? 'trade.faculty@cab-academy.com';
            unset($courseData['instructor_email']);
            $assignedInstructorUser = $facultyUsers[$instEmail] ?? $facultyUsers['trade.faculty@cab-academy.com'];
            $courseData['instructor_id'] = $assignedInstructorUser->id;
            $courseData['views'] = rand(150, 950);

            $course = Course::withTrashed()->updateOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );
            if ($course->trashed()) {
                $course->restore();
            }

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

        // 7. Create Job Posts (Generic Institutional Employers - No Real Company Names)
        JobPost::updateOrCreate(
            ['title' => 'Commercial & Shipping Operations Specialist'],
            [
                'company' => 'Global Logistics & Trade Solutions Ltd.',
                'location' => 'Dhaka (Mirpur-Pallabi)',
                'salary_range' => '৳50,000 - ৳70,000',
                'description' => 'Seeking an experienced commercial officer with practical background in Import-Export operations, L/C handling, and port customs clearance.',
                'apply_link' => '#',
                'is_active' => true,
            ]
        );

        JobPost::updateOrCreate(
            ['title' => 'Supply Chain & Sourcing Analyst'],
            [
                'company' => 'National Supply Chain & Freight Enterprise',
                'location' => 'Dhaka / Hybrid',
                'salary_range' => '৳40,000 - ৳60,000',
                'description' => 'Responsible for procurement tracking, vendor audit coordination, inventory modeling, and shipping schedule verification.',
                'apply_link' => '#',
                'is_active' => true,
            ]
        );

        JobPost::updateOrCreate(
            ['title' => 'Corporate VAT & Tax Compliance Specialist'],
            [
                'company' => 'Industrial Advisory & Compliance Group',
                'location' => 'Dhaka (Motijheel C/A)',
                'salary_range' => '৳45,000 - ৳65,000',
                'description' => 'Manage monthly Mushak returns, withholding tax deduction certificates, and coordinate statutory audit compliance.',
                'apply_link' => '#',
                'is_active' => true,
            ]
        );

        // 8. Create Payment Methods (bKash, Nagad, Corporate Bank Transfer)
        PaymentMethod::firstOrCreate(
            ['provider_name' => 'bKash Merchant'],
            [
                'account_number' => '01917-054664',
                'account_type' => 'Merchant',
                'instructions' => "1. Go to your bKash app and select 'Make Payment'.\n2. Enter Merchant Number: 01917-054664.\n3. Enter the exact Course Fee.\n4. Enter Reference: Course Title or Your Mobile.\n5. Enter your PIN and confirm.\n6. Copy the TrxID and submit it below.",
                'is_active' => true,
            ]
        );

        PaymentMethod::firstOrCreate(
            ['provider_name' => 'Nagad Personal / Merchant'],
            [
                'account_number' => '01917-054664',
                'account_type' => 'Merchant',
                'instructions' => "1. Dial *167# or open Nagad App.\n2. Select Payment.\n3. Enter Number: 01917-054664.\n4. Input Amount and Reference.\n5. Save TrxID and submit below.",
                'is_active' => true,
            ]
        );

        PaymentMethod::firstOrCreate(
            ['provider_name' => 'Bank Transfer'],
            [
                'account_number' => '000-000-000000',
                'account_type' => 'Corporate Account',
                'instructions' => "Bank Name: Corporate Commercial Bank PLC\nBranch: Principal Corporate Branch, Dhaka\nAccount Name: ACS Academy\nA/C Number: 000-000-000000\nRouting: 000000000\nPlease upload deposit slip or bank receipt.",
                'is_active' => true,
            ]
        );
        
        // 9. Default Global Settings (firstOrCreate so existing production settings are never overwritten)
        Setting::firstOrCreate(['key' => 'site_name'], ['value' => 'ACS Academy']);
        Setting::firstOrCreate(['key' => 'site_logo'], ['value' => '/images/logo.png']);
        Setting::firstOrCreate(['key' => 'primary_color'], ['value' => '#051c45']);
        Setting::firstOrCreate(['key' => 'contact_email'], ['value' => 'commercialacademy868@gmail.com']);
        Setting::firstOrCreate(['key' => 'contact_phone'], ['value' => '01917-054664']);
        Setting::firstOrCreate(['key' => 'contact_address'], ['value' => 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216']);
        Setting::firstOrCreate(['key' => 'landing_cert_count'], ['value' => '4']);
        Setting::firstOrCreate(['key' => 'landing_daylong_count'], ['value' => '4']);
        Setting::firstOrCreate(['key' => 'landing_clients_title'], ['value' => 'Industry Sectors & Enterprise Training']);
        Setting::firstOrCreate(['key' => 'landing_clients_data'], ['value' => "International Trade & L/C\nSupply Chain Management\nCustoms & Freight Clearance\nCorporate Tax & VAT\nCommercial Banking & Finance\nIndustrial HR & Labor Laws\nCorporate Law & Compliance\nProject Planning & BOQ"]);
    }
}
