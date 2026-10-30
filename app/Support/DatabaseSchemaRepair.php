<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaRepair
{
    /**
     * Fast, idempotent schema verifier and self-healer.
     * Prevents 500 Internal Server Errors caused by un-run or partial migrations on shared hosting (e.g. InfinityFree).
     */
    public static function ensureSchema(bool $force = false): array
    {
        $lockFile = storage_path('framework/schema_verified_v6.lock');
        if (!$force && file_exists($lockFile)) {
            return ['status' => 'cached', 'repaired' => []];
        }

        $repaired = [];

        try {
            // 1. Users table columns
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) use (&$repaired) {
                    if (!Schema::hasColumn('users', 'role')) {
                        $table->string('role')->default('student');
                        $repaired[] = 'users.role';
                    }
                    if (!Schema::hasColumn('users', 'phone')) {
                        $table->string('phone', 50)->nullable();
                        $repaired[] = 'users.phone';
                    }
                    if (!Schema::hasColumn('users', 'preferences')) {
                        $table->json('preferences')->nullable();
                        $repaired[] = 'users.preferences';
                    }
                    if (!Schema::hasColumn('users', 'notification_preferences')) {
                        $table->json('notification_preferences')->nullable();
                        $repaired[] = 'users.notification_preferences';
                    }
                    if (!Schema::hasColumn('users', 'profile_picture')) {
                        $table->string('profile_picture')->nullable();
                        $repaired[] = 'users.profile_picture';
                    }
                    if (!Schema::hasColumn('users', 'cover_photo')) {
                        $table->string('cover_photo')->nullable();
                        $repaired[] = 'users.cover_photo';
                    }
                    if (!Schema::hasColumn('users', 'xp')) {
                        $table->integer('xp')->default(0);
                        $repaired[] = 'users.xp';
                    }
                    if (!Schema::hasColumn('users', 'current_streak')) {
                        $table->integer('current_streak')->default(0);
                        $repaired[] = 'users.current_streak';
                    }
                    if (!Schema::hasColumn('users', 'last_activity_date')) {
                        $table->date('last_activity_date')->nullable();
                        $repaired[] = 'users.last_activity_date';
                    }
                    if (!Schema::hasColumn('users', 'deleted_at')) {
                        $table->softDeletes();
                        $repaired[] = 'users.deleted_at';
                    }
                });
            }

            // 2. Courses table columns
            if (Schema::hasTable('courses')) {
                Schema::table('courses', function (Blueprint $table) use (&$repaired) {
                    if (!Schema::hasColumn('courses', 'short_description')) {
                        $table->text('short_description')->nullable();
                        $repaired[] = 'courses.short_description';
                    }
                    if (!Schema::hasColumn('courses', 'duration')) {
                        $table->string('duration')->nullable();
                        $repaired[] = 'courses.duration';
                    }
                    if (!Schema::hasColumn('courses', 'start_date')) {
                        $table->date('start_date')->nullable();
                        $repaired[] = 'courses.start_date';
                    }
                    if (!Schema::hasColumn('courses', 'venue')) {
                        $table->string('venue')->nullable();
                        $repaired[] = 'courses.venue';
                    }
                    if (!Schema::hasColumn('courses', 'target_audience')) {
                        $table->text('target_audience')->nullable();
                        $repaired[] = 'courses.target_audience';
                    }
                    if (!Schema::hasColumn('courses', 'price')) {
                        $table->decimal('price', 10, 2)->default(0);
                        $repaired[] = 'courses.price';
                    }
                    if (!Schema::hasColumn('courses', 'cover_image')) {
                        $table->string('cover_image')->nullable();
                        $repaired[] = 'courses.cover_image';
                    }
                    if (!Schema::hasColumn('courses', 'level')) {
                        $table->string('level')->nullable();
                        $repaired[] = 'courses.level';
                    }
                    if (!Schema::hasColumn('courses', 'category')) {
                        $table->string('category')->nullable();
                        $repaired[] = 'courses.category';
                    }
                    if (!Schema::hasColumn('courses', 'instructor_id')) {
                        $table->unsignedBigInteger('instructor_id')->nullable();
                        $repaired[] = 'courses.instructor_id';
                    }
                    if (!Schema::hasColumn('courses', 'views')) {
                        $table->unsignedBigInteger('views')->default(0);
                        $repaired[] = 'courses.views';
                    }
                    if (!Schema::hasColumn('courses', 'certificate_enabled')) {
                        $table->boolean('certificate_enabled')->default(false);
                        $repaired[] = 'courses.certificate_enabled';
                    }
                    if (!Schema::hasColumn('courses', 'batch')) {
                        $table->string('batch')->nullable();
                        $repaired[] = 'courses.batch';
                    }
                    if (!Schema::hasColumn('courses', 'show_batch')) {
                        $table->boolean('show_batch')->default(false);
                        $repaired[] = 'courses.show_batch';
                    }
                    if (!Schema::hasColumn('courses', 'enrollment_fields')) {
                        $table->json('enrollment_fields')->nullable();
                        $repaired[] = 'courses.enrollment_fields';
                    }
                    if (!Schema::hasColumn('courses', 'counselor_phone')) {
                        $table->string('counselor_phone', 50)->nullable();
                        $repaired[] = 'courses.counselor_phone';
                    }
                    if (!Schema::hasColumn('courses', 'whatsapp_message')) {
                        $table->text('whatsapp_message')->nullable();
                        $repaired[] = 'courses.whatsapp_message';
                    }
                    if (!Schema::hasColumn('courses', 'deleted_at')) {
                        $table->softDeletes();
                        $repaired[] = 'courses.deleted_at';
                    }
                });
            }

            // 3. Payments table columns & MySQL ENUM status normalization
            if (Schema::hasTable('payments')) {
                Schema::table('payments', function (Blueprint $table) use (&$repaired) {
                    if (!Schema::hasColumn('payments', 'invoice_number')) {
                        $table->string('invoice_number')->nullable();
                        $repaired[] = 'payments.invoice_number';
                    }
                    if (!Schema::hasColumn('payments', 'payment_type')) {
                        $table->string('payment_type')->default('tuition');
                        $repaired[] = 'payments.payment_type';
                    }
                    if (!Schema::hasColumn('payments', 'notes')) {
                        $table->text('notes')->nullable();
                        $repaired[] = 'payments.notes';
                    }
                    if (!Schema::hasColumn('payments', 'form_data')) {
                        $table->json('form_data')->nullable();
                        $repaired[] = 'payments.form_data';
                    }
                    if (!Schema::hasColumn('payments', 'verified_at')) {
                        $table->timestamp('verified_at')->nullable();
                        $repaired[] = 'payments.verified_at';
                    }
                    if (!Schema::hasColumn('payments', 'verified_by')) {
                        $table->unsignedBigInteger('verified_by')->nullable();
                        $repaired[] = 'payments.verified_by';
                    }
                });

                // Ensure MySQL payments.status is VARCHAR(50) rather than legacy ENUM('pending','approved','rejected')
                if (DB::getDriverName() === 'mysql') {
                    try {
                        DB::statement("ALTER TABLE `payments` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
                        DB::statement("ALTER TABLE `payments` MODIFY COLUMN `payment_method_id` BIGINT UNSIGNED NULL");
                    } catch (\Throwable $e) {
                        // Ignore if already modified
                    }
                }
            }

            // 4. Job Posts table columns
            if (Schema::hasTable('job_posts')) {
                Schema::table('job_posts', function (Blueprint $table) use (&$repaired) {
                    if (!Schema::hasColumn('job_posts', 'job_type')) {
                        $table->string('job_type')->default('Full-time')->nullable();
                        $repaired[] = 'job_posts.job_type';
                    }
                    if (!Schema::hasColumn('job_posts', 'company_logo')) {
                        $table->string('company_logo')->nullable();
                        $repaired[] = 'job_posts.company_logo';
                    }
                    if (!Schema::hasColumn('job_posts', 'deleted_at')) {
                        $table->softDeletes();
                        $repaired[] = 'job_posts.deleted_at';
                    }
                });
            }

            // 5. Instructors table & columns
            if (!Schema::hasTable('instructors')) {
                Schema::create('instructors', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('title');
                    $table->string('expertise');
                    $table->string('avatar')->nullable();
                    $table->text('bio')->nullable();
                    $table->string('email')->nullable();
                    $table->string('phone')->nullable();
                    $table->integer('order')->default(0);
                    $table->boolean('is_active')->default(true);
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                });
                $repaired[] = 'table:instructors';
            } else {
                Schema::table('instructors', function (Blueprint $table) use (&$repaired) {
                    if (!Schema::hasColumn('instructors', 'email')) {
                        $table->string('email')->nullable();
                        $repaired[] = 'instructors.email';
                    }
                    if (!Schema::hasColumn('instructors', 'phone')) {
                        $table->string('phone')->nullable();
                        $repaired[] = 'instructors.phone';
                    }
                    if (!Schema::hasColumn('instructors', 'user_id')) {
                        $table->unsignedBigInteger('user_id')->nullable();
                        $repaired[] = 'instructors.user_id';
                    }
                    if (!Schema::hasColumn('instructors', 'deleted_at')) {
                        $table->softDeletes();
                        $repaired[] = 'instructors.deleted_at';
                    }
                });
            }

            // 6. Categories table
            if (!Schema::hasTable('categories')) {
                Schema::create('categories', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('slug')->unique();
                    $table->string('icon')->default('fa-tag');
                    $table->text('description')->nullable();
                    $table->string('image')->nullable();
                    $table->integer('order')->default(0);
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
                $repaired[] = 'table:categories';
            }

            // 7. Blog Posts table & columns
            if (!Schema::hasTable('blog_posts')) {
                Schema::create('blog_posts', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('slug')->unique();
                    $table->string('category')->default('Trade & Commerce');
                    $table->text('excerpt')->nullable();
                    $table->text('meta_description')->nullable();
                    $table->longText('content');
                    $table->string('featured_image')->nullable();
                    $table->string('author_name')->default('ACS Academy Faculty');
                    $table->string('author_title')->default('Senior Industry Fellow');
                    $table->string('author_avatar')->nullable();
                    $table->string('read_time')->default('5 min read');
                    $table->boolean('is_published')->default(true);
                    $table->boolean('is_featured')->default(false);
                    $table->unsignedBigInteger('views_count')->default(0);
                    $table->timestamp('published_at')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                $repaired[] = 'table:blog_posts';
            } else {
                Schema::table('blog_posts', function (Blueprint $table) use (&$repaired) {
                    if (!Schema::hasColumn('blog_posts', 'is_featured')) {
                        $table->boolean('is_featured')->default(false);
                        $repaired[] = 'blog_posts.is_featured';
                    }
                    if (!Schema::hasColumn('blog_posts', 'views_count')) {
                        $table->unsignedBigInteger('views_count')->default(0);
                        $repaired[] = 'blog_posts.views_count';
                    }
                    if (!Schema::hasColumn('blog_posts', 'meta_description')) {
                        $table->text('meta_description')->nullable();
                        $repaired[] = 'blog_posts.meta_description';
                    }
                });
            }

            // 8. Financial Expenses table
            if (!Schema::hasTable('financial_expenses')) {
                Schema::create('financial_expenses', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('category')->default('campus_operations');
                    $table->decimal('amount', 10, 2);
                    $table->date('expense_date');
                    $table->string('paid_to');
                    $table->string('payment_method')->default('bank_transfer');
                    $table->string('reference_no')->nullable();
                    $table->string('receipt_file')->nullable();
                    $table->text('notes')->nullable();
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                $repaired[] = 'table:financial_expenses';
            }

            // 9. Wishlists, Certificates, Contact Inquiries, Notifications
            if (!Schema::hasTable('wishlists')) {
                Schema::create('wishlists', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('course_id');
                    $table->timestamps();
                    $table->unique(['user_id', 'course_id']);
                });
                $repaired[] = 'table:wishlists';
            }

            if (!Schema::hasTable('certificates')) {
                Schema::create('certificates', function (Blueprint $table) {
                    $table->id();
                    $table->string('certificate_number')->unique();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('course_id');
                    $table->date('issue_date');
                    $table->string('verification_hash')->nullable();
                    $table->timestamps();
                    $table->unique(['user_id', 'course_id']);
                });
                $repaired[] = 'table:certificates';
            }

            if (!Schema::hasTable('contact_inquiries')) {
                Schema::create('contact_inquiries', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email');
                    $table->string('phone')->nullable();
                    $table->string('inquiry_type')->default('admission');
                    $table->string('organization')->nullable();
                    $table->string('subject')->nullable();
                    $table->text('message');
                    $table->string('status')->default('new');
                    $table->timestamps();
                });
                $repaired[] = 'table:contact_inquiries';
            }

            if (!Schema::hasTable('notifications')) {
                Schema::create('notifications', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->string('type');
                    $table->morphs('notifiable');
                    $table->text('data');
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
                $repaired[] = 'table:notifications';
            }

            self::repairInstructorCourseLinks($repaired);
            self::ensureEssentialAdminAndPaymentMethods();

            @file_put_contents($lockFile, json_encode([
                'verified_at' => date('c'),
                'repaired' => $repaired,
            ]));
        } catch (\Throwable $e) {
            Log::warning('DatabaseSchemaRepair::ensureSchema warning: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'repaired' => $repaired];
        }

        return ['status' => 'ok', 'repaired' => $repaired];
    }

    /**
     * Self-heal instructor <-> user <-> course foreign key links.
     */
    public static function repairInstructorCourseLinks(array &$repaired = []): void
    {
        try {
            if (!Schema::hasTable('instructors') || !Schema::hasTable('users') || !Schema::hasTable('courses')) {
                return;
            }

            $instructors = \App\Models\Instructor::orderBy('order', 'asc')->get();
            foreach ($instructors as $inst) {
                $inst->save(); // Triggers saving/saved hooks to guarantee valid user_id and synced User profile
            }

            $tradeInst = \App\Models\Instructor::where('email', 'trade.faculty@cab-academy.com')->first()
                ?? $instructors->first();
            $scmInst = \App\Models\Instructor::where('email', 'scm.faculty@cab-academy.com')->first();

            if ($tradeInst && $tradeInst->user_id) {
                $tradeCourseCount = \App\Models\Course::where('instructor_id', $tradeInst->user_id)->count();
                if ($tradeCourseCount === 0) {
                    $updated = \App\Models\Course::where(function ($q) {
                        $q->where('category', 'like', '%Export%')
                          ->orWhere('title', 'like', '%L/C, Banking%')
                          ->orWhere('title', 'like', '%Customs, C&F%');
                    })->update(['instructor_id' => $tradeInst->user_id]);
                    if ($updated > 0) {
                        $repaired[] = 'courses.instructor_id:trade';
                    }
                }
            }

            if ($scmInst && $scmInst->user_id) {
                $scmCourseCount = \App\Models\Course::where('instructor_id', $scmInst->user_id)->count();
                if ($scmCourseCount === 0) {
                    $updated = \App\Models\Course::where(function ($q) {
                        $q->whereIn('category', ['Supply Chain Management', 'Civil Construction Project', 'Fire Safety'])
                          ->orWhere('title', 'like', '%Supply Chain%');
                    })->update(['instructor_id' => $scmInst->user_id]);
                    if ($updated > 0) {
                        $repaired[] = 'courses.instructor_id:scm';
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('DatabaseSchemaRepair::repairInstructorCourseLinks warning: ' . $e->getMessage());
        }
    }

    /**
     * Ensure essential Admin account and default payment methods exist even when migrating WITHOUT demo seed data.
     */
    public static function ensureEssentialAdminAndPaymentMethods(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        // Normalize any legacy "CAB Academy" / "Commercial Academy Bangladesh" branding in settings table to "ACS Academy"
        try {
            if (Schema::hasTable('settings')) {
                $legacySettings = \App\Models\Setting::where('value', 'like', '%CAB Academy%')
                    ->orWhere('value', 'like', '%Commercial Academy Bangladesh%')
                    ->orWhere('value', 'like', '%INV-CAB%')
                    ->get();
                foreach ($legacySettings as $s) {
                    $newVal = str_replace(
                        ['Commercial Academy Bangladesh (CAB Academy)', 'Commercial Academy Bangladesh', 'CAB Academy', 'INV-CAB'],
                        ['ACS Academy', 'ACS Academy', 'ACS Academy', 'INV-ACS'],
                        (string) $s->value
                    );
                    if ($newVal !== $s->value) {
                        $s->value = $newVal;
                        $s->saveQuietly();
                    }
                }
                \Illuminate\Support\Facades\Cache::forget('global_settings');
            }
        } catch (\Throwable $e) {
            // Ignore during initial migration
        }

        try {
            if (Schema::hasTable('users')) {
                $hasAdmin = \App\Models\User::where('role', 'admin')->exists();
                if (!$hasAdmin) {
                    \App\Models\User::firstOrCreate(
                        ['email' => 'admin@cab-academy.com'],
                        [
                            'name' => 'Academy Administrator',
                            'password' => bcrypt('password'),
                            'role' => 'admin',
                            'email_verified_at' => now(),
                        ]
                    );
                }
            }

            if (Schema::hasTable('payment_methods') && \App\Models\PaymentMethod::count() === 0) {
                $hotline = Schema::hasTable('settings')
                    ? \App\Models\Setting::getVal('contact_hotline', '01917-054664')
                    : '01917-054664';

                \App\Models\PaymentMethod::firstOrCreate(
                    ['provider_name' => 'bKash Merchant'],
                    [
                        'account_number' => $hotline,
                        'account_type' => 'Merchant',
                        'instructions' => "Send the exact Course Fee via bKash and enter your Sender Mobile Number & TrxID.",
                        'is_active' => true,
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning('DatabaseSchemaRepair::ensureEssentialAdminAndPaymentMethods warning: ' . $e->getMessage());
        }
    }

    /**
     * Safely purge seeded demo courses, demo faculty chairs, demo job posts, demo blogs, and demo community posts
     * while preserving all real admin-created courses, real instructors, real users, payments, and settings.
     */
    public static function purgeDemoData(): array
    {
        $summary = [
            'restored_real_courses' => 0,
            'deleted_demo_courses' => 0,
            'deleted_demo_instructors' => 0,
            'deleted_demo_jobs' => 0,
            'deleted_demo_blogs' => 0,
            'deleted_demo_posts' => 0,
        ];

        $demoCourseTitles = [
            'Certificate Course in L/C, Banking, Customs & Shipping for Import – Export',
            'Certificate Course in Supply Chain Management & Strategic Sourcing',
            'Certificate Course in Financial Accounting, VAT & Corporate Tax Management',
            'Certificate Course in Corporate Governance, Company Law & Secretarial Practice',
            'Commercial Guideline with Customs, C&F Agent & Shipping Procedures (Day-Long)',
            'Labor Law, HR Compliance & Industrial Disputes Masterclass (Day-Long)',
            'Civil Construction Project Planning, BOQ & Cost Estimation (Day-Long)',
            'Fire Safety, Disaster Management & Factory Compliance Masterclass (Day-Long)',
            'PGD in Supply Chain Management (PGDSCM)',
        ];

        $demoFacultyEmails = [
            'trade.faculty@cab-academy.com',
            'scm.faculty@cab-academy.com',
            'tax.faculty@cab-academy.com',
            'law.faculty@cab-academy.com',
        ];

        $demoJobTitles = [
            'Commercial & Shipping Operations Specialist',
            'Supply Chain & Sourcing Analyst',
            'Corporate VAT & Tax Compliance Specialist',
        ];

        $demoBlogSlugs = [
            'complete-roadmap-to-lc-customs-clearance',
            'strategic-sourcing-warehouse-safety-stock-modeling',
            'corporate-vat-rebates-withholding-tax-deductions',
        ];

        $demoDiscussionTitles = [
            'Updated HS Code Mandatory Guidelines for L/C Applications',
            'Excited to complete my first Bill of Entry simulation!',
            'Corporate VAT Rebate Checklist: Month-End Guidelines',
            'Complete Roadmap to Letter of Credit (L/C) & Customs Clearance Under Regulatory Directives',
            'Strategic Sourcing & Warehouse Safety Stock Modeling for Industrial Enterprises',
            'Corporate VAT Rebates & Withholding Tax Deductions: Finance Act Best Practices',
        ];

        // 1. Restore any real (non-demo) courses that were soft-deleted by an earlier seeder run
        if (Schema::hasTable('courses')) {
            $restored = \App\Models\Course::onlyTrashed()
                ->whereNotIn('title', $demoCourseTitles)
                ->restore();
            $summary['restored_real_courses'] = (int) $restored;

            // Delete the 8 demo courses
            $demoCourses = \App\Models\Course::withTrashed()
                ->whereIn('title', $demoCourseTitles)
                ->get();

            foreach ($demoCourses as $dc) {
                foreach ($dc->sections as $sec) {
                    $sec->lessons()->delete();
                    $sec->delete();
                }
                foreach ($dc->quizzes as $quiz) {
                    foreach ($quiz->questions as $q) {
                        $q->options()->delete();
                        $q->delete();
                    }
                    $quiz->delete();
                }
                $dc->forceDelete();
                $summary['deleted_demo_courses']++;
            }
        }

        // 2. Delete the 4 demo Faculty Chair instructors & re-link real courses to active real instructor
        if (Schema::hasTable('instructors') && Schema::hasTable('users')) {
            $demoInstructors = \App\Models\Instructor::withTrashed()
                ->where(function ($q) use ($demoFacultyEmails) {
                    $q->whereIn('email', $demoFacultyEmails)
                      ->orWhere('name', 'like', 'Faculty Chair%');
                })
                ->get();

            $demoUserIds = \App\Models\User::withTrashed()
                ->where(function ($q) use ($demoFacultyEmails) {
                    $q->whereIn('email', $demoFacultyEmails)
                      ->orWhere('name', 'like', 'Faculty Chair%');
                })
                ->pluck('id')
                ->toArray();

            foreach ($demoInstructors as $di) {
                if ($di->user_id) {
                    $demoUserIds[] = $di->user_id;
                }
                $di->forceDelete();
                $summary['deleted_demo_instructors']++;
            }

            $demoUserIds = array_unique(array_filter($demoUserIds));

            // Find a real remaining instructor or admin to own any real courses
            $realInstructor = \App\Models\Instructor::where('is_active', true)->orderBy('order', 'asc')->first();
            $fallbackUserId = $realInstructor?->user_id
                ?? \App\Models\User::where('role', 'admin')->value('id');

            if (!empty($demoUserIds)) {
                if (Schema::hasTable('courses') && $fallbackUserId) {
                    \App\Models\Course::withTrashed()
                        ->whereIn('instructor_id', $demoUserIds)
                        ->update(['instructor_id' => $fallbackUserId]);
                }
                \App\Models\User::withTrashed()->whereIn('id', $demoUserIds)->forceDelete();
            }

            // Also remove demo student account if it has no real payments
            $demoStudent = \App\Models\User::withTrashed()->where('email', 'student@cab-academy.com')->first();
            if ($demoStudent && (!$demoStudent->payments()->exists())) {
                $demoStudent->forceDelete();
            }
        }

        // 3. Delete demo JobPosts
        if (Schema::hasTable('job_posts')) {
            $summary['deleted_demo_jobs'] = (int) \App\Models\JobPost::withTrashed()
                ->whereIn('title', $demoJobTitles)
                ->forceDelete();
        }

        // 4. Delete demo BlogPosts & CommunityPosts
        if (Schema::hasTable('blog_posts')) {
            $demoBlogIds = \App\Models\BlogPost::withTrashed()
                ->whereIn('slug', $demoBlogSlugs)
                ->pluck('id')
                ->toArray();

            if (!empty($demoBlogIds) && Schema::hasTable('community_posts')) {
                $postIds = \App\Models\CommunityPost::withTrashed()
                    ->whereIn('blog_post_id', $demoBlogIds)
                    ->pluck('id')
                    ->toArray();
                if (!empty($postIds) && Schema::hasTable('community_comments')) {
                    \App\Models\CommunityComment::whereIn('community_post_id', $postIds)->delete();
                }
                $summary['deleted_demo_posts'] += (int) \App\Models\CommunityPost::withTrashed()
                    ->whereIn('blog_post_id', $demoBlogIds)
                    ->forceDelete();
            }

            $summary['deleted_demo_blogs'] = (int) \App\Models\BlogPost::withTrashed()
                ->whereIn('slug', $demoBlogSlugs)
                ->forceDelete();
        }

        if (Schema::hasTable('community_posts')) {
            $discPostIds = \App\Models\CommunityPost::withTrashed()
                ->whereIn('title', $demoDiscussionTitles)
                ->pluck('id')
                ->toArray();
            if (!empty($discPostIds) && Schema::hasTable('community_comments')) {
                \App\Models\CommunityComment::whereIn('community_post_id', $discPostIds)->delete();
            }
            $summary['deleted_demo_posts'] += (int) \App\Models\CommunityPost::withTrashed()
                ->whereIn('title', $demoDiscussionTitles)
                ->forceDelete();
        }

        // 5. Normalize any legacy "CAB Academy" / "Commercial Academy Bangladesh" in settings to "ACS Academy"
        $rebrandedSettings = 0;
        if (Schema::hasTable('settings')) {
            $legacySettings = \App\Models\Setting::where('value', 'like', '%CAB Academy%')
                ->orWhere('value', 'like', '%Commercial Academy Bangladesh%')
                ->orWhere('value', 'like', '%INV-CAB%')
                ->get();
            foreach ($legacySettings as $s) {
                $newVal = str_replace(
                    ['Commercial Academy Bangladesh (CAB Academy)', 'Commercial Academy Bangladesh', 'CAB Academy', 'INV-CAB'],
                    ['ACS Academy', 'ACS Academy', 'ACS Academy', 'INV-ACS'],
                    (string) $s->value
                );
                if ($newVal !== $s->value) {
                    $s->value = $newVal;
                    $s->saveQuietly();
                    $rebrandedSettings++;
                }
            }
            \Illuminate\Support\Facades\Cache::forget('global_settings');
        }

        $purgedLines = [];
        if ($summary['restored_real_courses'] > 0) {
            $purgedLines[] = "Restored {$summary['restored_real_courses']} real course(s) that were soft-deleted.";
        }
        if ($summary['deleted_demo_courses'] > 0) {
            $purgedLines[] = "Removed {$summary['deleted_demo_courses']} seeded demo course(s).";
        }
        if ($summary['deleted_demo_instructors'] > 0) {
            $purgedLines[] = "Removed {$summary['deleted_demo_instructors']} seeded demo faculty chair(s).";
        }
        if ($summary['deleted_demo_jobs'] > 0) {
            $purgedLines[] = "Removed {$summary['deleted_demo_jobs']} seeded demo job listing(s).";
        }
        if ($summary['deleted_demo_blogs'] > 0) {
            $purgedLines[] = "Removed {$summary['deleted_demo_blogs']} seeded demo blog post(s).";
        }
        if ($summary['deleted_demo_posts'] > 0) {
            $purgedLines[] = "Removed {$summary['deleted_demo_posts']} seeded demo community post(s).";
        }
        if ($rebrandedSettings > 0) {
            $purgedLines[] = "Updated {$rebrandedSettings} setting(s) from legacy CAB branding to ACS Academy.";
        }

        $summary['status'] = 'ok';
        $summary['purged'] = $purgedLines;

        return $summary;
    }
}
