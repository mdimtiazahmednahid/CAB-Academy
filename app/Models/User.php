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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function instructedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
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
        ];
    }
}
