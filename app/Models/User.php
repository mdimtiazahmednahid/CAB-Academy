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

#[Fillable(['name', 'email', 'password', 'role', 'preferences', 'profile_picture', 'cover_photo'])]
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

    public function instructedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function instructorProfile()
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }

    /**
     * Get fully-qualified profile picture URL with storage prefixing and fallback.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        $default = 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=051c45&color=fff';
        if (!$this->profile_picture) {
            return $default;
        }
        return storage_asset($this->profile_picture, $default);
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
