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
        // 0. Seed Authentic Course Categories
        $categories = [
            [
                'name' => 'Export – Import',
                'slug' => 'export-import',
                'icon' => 'fa-ship',
                'description' => 'L/C, Banking, Customs, Bond Management, and International Trade Procedures',
                'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=600&q=80',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Supply Chain Management',
                'slug' => 'supply-chain-management',
                'icon' => 'fa-truck-fast',
                'description' => 'Procurement, Logistics, Warehouse, and Inventory Optimization',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=600&q=80',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'slug' => 'accounting',
                'icon' => 'fa-calculator',
                'description' => 'Financial Reporting, Auditing, Corporate Tax, and Budgeting',
                'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax Management',
                'slug' => 'income-tax-management',
                'icon' => 'fa-file-invoice-dollar',
                'description' => 'Tax Assessment, VAT & Customs, Returns, and Legal Compliance',
                'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=600&q=80',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Human Resource',
                'slug' => 'human-resource',
                'icon' => 'fa-people-group',
                'description' => 'HR Operations, Labor Law, Talent Acquisition, and Performance Appraisal',
                'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=600&q=80',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Civil Construction Project',
                'slug' => 'civil-construction-project',
                'icon' => 'fa-helmet-safety',
                'description' => 'Project Planning, Site Supervision, Quality Control, and Estimating',
                'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=600&q=80',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Fire Safety',
                'slug' => 'fire-safety',
                'icon' => 'fa-fire-extinguisher',
                'description' => 'Industrial Safety, Disaster Management, NFPA Codes, and Compliance',
                'image' => 'https://images.unsplash.com/photo-1582139329536-e7284fece509?auto=format&fit=crop&w=600&q=80',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Leadership & Soft Skills',
                'slug' => 'leadership',
                'icon' => 'fa-award',
                'description' => 'Executive Leadership, Communication, Conflict Resolution, and Negotiations',
                'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=600&q=80',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Soft Skills',
                'slug' => 'soft-skills',
                'icon' => 'fa-comments',
                'description' => 'Effective Corporate Presentation, Workplace Communication, and Etiquette',
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Value Added Tax',
                'slug' => 'value-added-tax',
                'icon' => 'fa-receipt',
                'description' => 'VAT Registration, VDS, Mushak Forms, and Auditing Standards',
                'image' => 'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Law & Compliance',
                'slug' => 'law',
                'icon' => 'fa-scale-balanced',
                'description' => 'Company Law, Corporate Secretarial Practices, and Contract Management',
                'image' => 'https://images.unsplash.com/photo-1453733197783-64768dd7467b?auto=format&fit=crop&w=600&q=80',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Project Management',
                'slug' => 'project-management',
                'icon' => 'fa-diagram-project',
                'description' => 'Agile, PMP Methodologies, Risk Mitigation, and Resource Allocation',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80',
                'order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }

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

        // 2. Create Faculty User Account (Institutional Role - No Personal / Real Names)
        $instructor = User::withTrashed()->where('email', 'faculty@cab-academy.com')->first();
        if ($instructor) {
            $instructor->restore();
            $instructor->update([
                'name' => 'Faculty Chair - Trade & Commercial Operations',
                'role' => 'instructor',
                'profile_picture' => null
            ]);
        } else {
            $instructor = User::create([
                'email' => 'faculty@cab-academy.com',
                'name' => 'Faculty Chair - Trade & Commercial Operations',
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'profile_picture' => null
            ]);
        }

        // Clean up any old instructor records with personal names
        Instructor::query()->forceDelete();

        // Seed Executive Faculty Roster (Strictly Institutional Roles - No Personal/Real/IPDP Names)
        $instructorRoster = [
            [
                'name' => 'Faculty Chair – Trade & Customs',
                'title' => 'Executive Fellow in Trade Finance & Customs Clearance',
                'expertise' => 'Banking, L/C, Customs & Shipping Procedure',
                'avatar' => null,
                'bio' => 'Senior academic advisory board with extensive commercial experience guiding international trade, customs regulations, and banking documentation.',
                'email' => 'trade.faculty@cab-academy.com',
                'phone' => '01917-054664',
                'order' => 1,
                'is_active' => true,
                'user_id' => $instructor->id,
            ],
            [
                'name' => 'Faculty Chair – Supply Chain',
                'title' => 'Executive Fellow in SCM Strategy & Procurement',
                'expertise' => 'Supply Chain Strategy & Sourcing Excellence',
                'avatar' => null,
                'bio' => 'Operations faculty specializing in strategic sourcing, warehouse safety stock modeling, freight optimization, and global logistics.',
                'email' => 'scm.faculty@cab-academy.com',
                'phone' => '01917-054664',
                'order' => 2,
                'is_active' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Faculty Chair – Tax & VAT',
                'title' => 'Executive Fellow in Corporate Tax & NBR Compliance',
                'expertise' => 'VAT, Corporate Taxation & Financial Audits',
                'avatar' => null,
                'bio' => 'Advisory faculty specializing in corporate tax assessment, monthly VAT returns, withholding tax certificates, and statutory audit compliance.',
                'email' => 'tax.faculty@cab-academy.com',
                'phone' => '01917-054664',
                'order' => 3,
                'is_active' => true,
                'user_id' => null,
            ],
            [
                'name' => 'Faculty Chair – Law & Governance',
                'title' => 'Executive Fellow in Corporate Law & Labor Relations',
                'expertise' => 'Labor Laws, Compliance & Organizational Governance',
                'avatar' => null,
                'bio' => 'Corporate legal faculty specializing in company law, statutory filings, employment regulations, domestic inquiries, and workplace compliance.',
                'email' => 'law.faculty@cab-academy.com',
                'phone' => '01917-054664',
                'order' => 4,
                'is_active' => true,
                'user_id' => null,
            ],
        ];

        foreach ($instructorRoster as $inst) {
            Instructor::create($inst);
        }

        // Purge any legacy user with real/personal names from earlier seeders
        User::withTrashed()->where('email', 'delwar@cabacademy.com')->forceDelete();

        // 3. Create Student (Generic Professional Learner)
        $student = User::updateOrCreate(
            ['email' => 'student@cab-academy.com'],
            [
                'name' => 'Executive Learner',
                'password' => bcrypt('password'),
                'role' => 'student',
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
                'venue' => 'Mirpur-Pallabi Campus & Online Live via Zoom',
                'target_audience' => 'Importers, Exporters, Supply Chain Executives, Commercial Managers, C&F Agents, Bank Trade Officers, and New Entrepreneurs.',
                'thumbnail' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
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
                'venue' => 'Mirpur-Pallabi Campus / Zoom',
                'target_audience' => 'Accountants, finance managers, tax advisors, auditors, and startup founders.',
                'thumbnail' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80',
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
                'venue' => 'Mirpur-Pallabi Campus & Zoom',
                'target_audience' => 'Company secretaries, corporate advocates, compliance managers, and executive directors.',
                'thumbnail' => 'https://images.unsplash.com/photo-1453733197783-64768dd7467b?auto=format&fit=crop&w=800&q=80',
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
            ],
            [
                'title' => 'Civil Construction Project Planning, BOQ & Cost Estimation (Day-Long)',
                'description' => 'Advanced project budgeting, BOQ preparation, contractor agreements, site inspection standards, and Primavera/MS Project tracking for civil engineers and site managers.',
                'short_description' => 'From land feasibility to final handover: practical project estimation, BOQ preparation, and QA/QC procedures for engineers.',
                'price' => 4000.00,
                'is_published' => true,
                'level' => 'Day Long',
                'category' => 'Civil Construction Project',
                'duration' => 'Full Day Masterclass (9 AM - 6 PM)',
                'start_date' => now()->addDays(15),
                'venue' => 'Classroom Mirpur-Pallabi Campus',
                'target_audience' => 'Civil engineers, site supervisors, project directors, and real estate developers.',
                'thumbnail' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'title' => 'Fire Safety, Disaster Management & Factory Compliance Masterclass (Day-Long)',
                'description' => 'Comprehensive training on fire hazard identification, risk assessment, fire prevention systems, sprinkler and hydrant operations, and compliance with national and international industrial fire safety codes.',
                'short_description' => 'Industrial safety, NFPA standards, emergency evacuation protocols, firefighting equipment handling, and international audit compliance.',
                'price' => 3000.00,
                'is_published' => true,
                'level' => 'Day Long',
                'category' => 'Fire Safety',
                'duration' => 'Full Day Workshop (9 AM - 6 PM)',
                'start_date' => now()->addDays(20),
                'venue' => 'Mirpur-Pallabi Campus',
                'target_audience' => 'EHS managers, safety officers, factory compliance heads, and facility managers.',
                'thumbnail' => 'https://images.unsplash.com/photo-1582139329536-e7284fece509?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        // Prune legacy courses not in the canonical 8
        Course::whereNotIn('title', array_column($courses, 'title'))->delete();

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

        // 7. Create Job Posts (Generic Institutional Employers - No Real Company Names)
        JobPost::query()->delete();
        JobPost::create([
            'title' => 'Commercial & Shipping Operations Specialist',
            'company' => 'Global Logistics & Trade Solutions Ltd.',
            'location' => 'Dhaka (Mirpur-Pallabi)',
            'salary_range' => '৳50,000 - ৳70,000',
            'description' => 'Seeking an experienced commercial officer with practical background in Import-Export operations, L/C handling, and port customs clearance.',
            'apply_link' => '#',
            'is_active' => true,
        ]);

        JobPost::create([
            'title' => 'Supply Chain & Sourcing Analyst',
            'company' => 'National Supply Chain & Freight Enterprise',
            'location' => 'Dhaka / Hybrid',
            'salary_range' => '৳40,000 - ৳60,000',
            'description' => 'Responsible for procurement tracking, vendor audit coordination, inventory modeling, and shipping schedule verification.',
            'apply_link' => '#',
            'is_active' => true,
        ]);

        JobPost::create([
            'title' => 'Corporate VAT & Tax Compliance Specialist',
            'company' => 'Industrial Advisory & Compliance Group',
            'location' => 'Dhaka (Motijheel C/A)',
            'salary_range' => '৳45,000 - ৳65,000',
            'description' => 'Manage monthly Mushak returns, withholding tax deduction certificates, and coordinate statutory audit compliance.',
            'apply_link' => '#',
            'is_active' => true,
        ]);

        // 8. Create Payment Methods (bKash, Nagad, Corporate Bank Transfer)
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
                'account_number' => '000-000-000000',
                'account_type' => 'Corporate Account',
                'instructions' => "Bank Name: Corporate Commercial Bank PLC\nBranch: Principal Corporate Branch, Dhaka\nAccount Name: CAB Academy\nA/C Number: 000-000-000000\nRouting: 000000000\nPlease upload deposit slip or bank receipt.",
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
        Setting::updateOrCreate(['key' => 'landing_cert_count'], ['value' => '4']);
        Setting::updateOrCreate(['key' => 'landing_daylong_count'], ['value' => '4']);
        Setting::updateOrCreate(['key' => 'landing_clients_title'], ['value' => 'Industry Sectors & Enterprise Training']);
        Setting::updateOrCreate(['key' => 'landing_clients_data'], ['value' => "International Trade & L/C\nSupply Chain Management\nCustoms & Freight Clearance\nCorporate Tax & VAT\nCommercial Banking & Finance\nIndustrial HR & Labor Laws\nCorporate Law & Compliance\nProject Planning & BOQ"]);
    }
}
