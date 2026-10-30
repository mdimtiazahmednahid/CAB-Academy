<?php

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Ensure users and instructors tables have required unified columns
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 50)->nullable();
            });
        }

        if (Schema::hasTable('instructors')) {
            Schema::table('instructors', function (Blueprint $table) {
                if (!Schema::hasColumn('instructors', 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn('instructors', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn('instructors', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('instructors', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // 1. Ensure all existing instructors have an active User account and are linked
        $instructors = Instructor::all();
        foreach ($instructors as $instructor) {
            $user = null;
            if ($instructor->user_id) {
                $user = User::find($instructor->user_id);
            }

            if (!$user && !empty($instructor->email)) {
                $user = User::where('email', $instructor->email)->first();
            }

            if (!$user) {
                $email = !empty($instructor->email) ? $instructor->email : 'faculty_' . $instructor->id . '@cab-academy.com';
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $instructor->name,
                        'password' => bcrypt('CabAcademy@2026'),
                        'role' => 'instructor',
                        'phone' => $instructor->phone,
                        'profile_picture' => $instructor->avatar,
                        'email_verified_at' => now(),
                    ]
                );
            }

            // Ensure role is instructor
            if ($user->role !== 'admin') {
                $user->role = 'instructor';
            }
            if ($instructor->avatar && !$user->profile_picture) {
                $user->profile_picture = $instructor->avatar;
            }
            $user->save();

            // Link instructor to user
            $instructor->user_id = $user->id;
            if (empty($instructor->email)) {
                $instructor->email = $user->email;
            }
            $instructor->saveQuietly();
        }

        // 2. Ensure all existing users with role 'instructor' have an Instructor profile
        $instructorUsers = User::where('role', 'instructor')->get();
        foreach ($instructorUsers as $instUser) {
            $profile = Instructor::where('user_id', $instUser->id)->first();
            if (!$profile && !empty($instUser->email)) {
                $profile = Instructor::where('email', $instUser->email)->first();
                if ($profile) {
                    $profile->user_id = $instUser->id;
                    $profile->saveQuietly();
                }
            }

            if (!$profile) {
                Instructor::create([
                    'user_id' => $instUser->id,
                    'name' => $instUser->name,
                    'email' => $instUser->email,
                    'title' => 'Corporate Faculty Fellow',
                    'expertise' => 'International Trade & Industry Masterclasses',
                    'avatar' => $instUser->profile_picture,
                    'phone' => $instUser->phone,
                    'is_active' => true,
                    'order' => 99,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};
