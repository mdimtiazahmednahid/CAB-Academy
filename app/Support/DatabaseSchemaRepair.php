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
        $lockFile = storage_path('framework/schema_verified_v4.lock');
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
                    $table->string('author_name')->default('CAB Academy Faculty');
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
}
