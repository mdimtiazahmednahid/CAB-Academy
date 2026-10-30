<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'preferences', 'notification_preferences', 'profile_picture', 'cover_photo', 'xp', 'current_streak', 'last_activity_date', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \Laravel\Cashier\Billable, SoftDeletes;
    /**
     * Check if user has administrator privileges.
     */
    public function isAdmin(): bool
    {
        $role = strtolower(trim($this->role ?? ''));
        $adminRoles = ['admin', 'superadmin', 'super_admin', 'super admin', 'manager'];
        
        if (in_array($role, $adminRoles)) {
            return true;
        }

        // Official CAB Academy owner & admin emails
        $adminEmails = [
            'admin@cab-academy.com',
            'commercialacademy868@gmail.com',
            'delwar@cabacademy.com',
        ];

        return in_array(strtolower(trim($this->email ?? '')), $adminEmails);
    }

    /**
     * Check if user is an instructor or elevated educator.
     */
    public function isInstructor(): bool
    {
        $role = strtolower(trim($this->role ?? ''));
        return $role === 'instructor' || $this->isAdmin();
    }

    protected static function booted(): void
    {
        static::saved(function ($user) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('instructors') || !\Illuminate\Support\Facades\Schema::hasColumn('instructors', 'user_id')) {
                    return;
                }

                if ($user->role === 'instructor' || ($user->isAdmin() && Instructor::where('user_id', $user->id)->exists())) {
                    Instructor::withoutEvents(function () use ($user) {
                        $instructor = Instructor::where('user_id', $user->id)->first();
                        if (!$instructor && !empty($user->email) && \Illuminate\Support\Facades\Schema::hasColumn('instructors', 'email')) {
                            $instructor = Instructor::where('email', $user->email)->first();
                        }
                        if (!$instructor) {
                            $instructor = new Instructor();
                            $instructor->user_id = $user->id;
                            $instructor->name = $user->name;
                            $instructor->email = $user->email;
                            $instructor->title = 'Corporate Faculty Fellow';
                            $instructor->expertise = 'International Trade & Industry Masterclasses';
                            $instructor->phone = $user->phone ?? null;
                            $instructor->is_active = true;
                            $instructor->order = 99;
                        } else {
                            $instructor->user_id = $user->id;
                            $instructor->name = $user->name;
                            if (!empty($user->email)) {
                                $instructor->email = $user->email;
                            }
                            if (!empty($user->phone)) {
                                $instructor->phone = $user->phone;
                            }
                            $instructor->is_active = true;
                        }
                        if ($user->profile_picture) {
                            $instructor->avatar = $user->profile_picture;
                        } elseif ($instructor->avatar && !$user->profile_picture) {
                            $user->profile_picture = $instructor->avatar;
                        }
                        $instructor->saveQuietly();
                    });
                } elseif (!$user->isAdmin()) {
                    // If user role was changed away from instructor/admin, cleanly deactivate linked instructor profile
                    Instructor::withoutEvents(function () use ($user) {
                        $instructor = Instructor::where('user_id', $user->id)->first();
                        if ($instructor) {
                            $instructor->is_active = false;
                            $instructor->saveQuietly();
                        }
                    });
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('User::saved instructor sync skipped: ' . $e->getMessage());
            }

            // Also sync author_avatar and author_name across Community Feed posts & comments
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('community_posts')) {
                    $effectiveAvatar = $user->profile_picture ?: ($user->instructorProfile?->avatar ?? null);
                    CommunityPost::where('user_id', $user->id)->update([
                        'author_name' => $user->name,
                        'author_avatar' => $effectiveAvatar,
                    ]);
                }
                if (\Illuminate\Support\Facades\Schema::hasTable('community_comments')) {
                    $effectiveAvatar = $user->profile_picture ?: ($user->instructorProfile?->avatar ?? null);
                    CommunityComment::where('user_id', $user->id)->update([
                        'author_name' => $user->name,
                        'author_avatar' => $effectiveAvatar,
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('User::saved community avatar sync skipped: ' . $e->getMessage());
            }
        });
    }

    public function instructedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function instructorProfile()
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }

    /**
     * Check whether the user has an uploaded profile picture or linked instructor avatar.
     */
    public function hasCustomProfilePicture(): bool
    {
        return !empty($this->profile_picture) || !empty($this->instructorProfile?->avatar);
    }

    /**
     * Get fully-qualified profile picture URL with fallback to instructor avatar and UI Avatars.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        $pic = $this->profile_picture ?: ($this->instructorProfile?->avatar ?? null);
        $default = 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?: 'User') . '&background=051c45&color=fff&bold=true';
        if (!$pic) {
            return $default;
        }
        return storage_asset($pic, $default);
    }

    /**
     * Get fully-qualified cover photo URL with storage prefixing.
     */
    public function getCoverPhotoUrlAttribute(): ?string
    {
        if (!$this->cover_photo) {
            return null;
        }
        return storage_asset($this->cover_photo);
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class)->withTimestamps();
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Gamification Logic
     */
    public function awardXp($amount)
    {
        $this->xp += $amount;
        
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        
        if ($this->last_activity_date === $yesterday) {
            $this->current_streak += 1;
        } elseif ($this->last_activity_date !== $today) {
            $this->current_streak = 1;
        }
        
        $this->last_activity_date = $today;
        $this->save();
    }

    public function getLevelAttribute()
    {
        // Simple scaling: Level 1 = 0 XP, Level 2 = 100 XP, Level 3 = 300 XP, Level 4 = 600 XP
        // Formula: xp = (level * (level - 1) / 2) * 100
        $level = 1;
        while (($level * ($level + 1) / 2) * 100 <= $this->xp) {
            $level++;
        }
        return $level;
    }

    public function getNextLevelXpAttribute()
    {
        $level = $this->level;
        return ($level * ($level + 1) / 2) * 100;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'notification_preferences' => 'array',
        ];
    }
}
