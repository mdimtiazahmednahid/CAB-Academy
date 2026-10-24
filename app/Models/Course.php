<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'thumbnail',
        'is_published',
        'cover_image',
        'duration',
        'start_date',
        'venue',
        'batch',
        'show_batch',
        'target_audience',
        'price',
        'level',
        'category',
        'instructor_id',
        'certificate_enabled',
        'views'
    ];

    protected $casts = [
        'start_date' => 'date',
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'certificate_enabled' => 'boolean',
        'show_batch' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
        $this->attributes['cover_image'] = $value;
    }

    public function setCoverImageAttribute($value)
    {
        $this->attributes['cover_image'] = $value;
        $this->attributes['thumbnail'] = $value;
    }

    public function getThumbnailAttribute($value)
    {
        return $value ?: ($this->attributes['cover_image'] ?? null);
    }

    public function getCoverImageAttribute($value)
    {
        return $value ?: ($this->attributes['thumbnail'] ?? null);
    }

    protected static function booted(): void
    {
        static::saving(function ($course) {
            // Guarantee single source of truth between cover_image and thumbnail
            $img = $course->cover_image ?: $course->thumbnail;
            if (!empty($img)) {
                $course->attributes['cover_image'] = $img;
                $course->attributes['thumbnail'] = $img;
            }

            // Ensure instructor_id points to users.id even if an instructors.id was passed
            if (!empty($course->instructor_id)) {
                $inst = Instructor::find($course->instructor_id);
                if ($inst && $inst->user_id) {
                    $course->instructor_id = $inst->user_id;
                }
            }
        });
    }

    /**
     * Get fully-qualified cover image URL with reliable storage prefixing and fallback.
     */
    public function getCoverImageUrlAttribute(): string
    {
        $img = $this->cover_image ?: $this->thumbnail;
        $default = 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';
        if (!$img) {
            return $default;
        }
        return storage_asset($img, $default);
    }

    /**
     * Alias for cover_image_url ensuring 100% parity across all views.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->cover_image_url;
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function instructorProfile()
    {
        return $this->hasOne(Instructor::class, 'user_id', 'instructor_id');
    }

    /**
     * Bulletproof Lead Faculty resolver handling both User ID and Instructor ID foreign keys.
     */
    public function getLeadFacultyAttribute()
    {
        if (!$this->instructor_id) {
            return null;
        }
        if ($this->instructor && $this->instructor->instructorProfile) {
            return $this->instructor->instructorProfile;
        }
        if ($inst = Instructor::find($this->instructor_id)) {
            return $inst;
        }
        return $this->instructor;
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Determine if a certificate is available/accessible for the given user.
     * Certificates are only accessible if explicitly released by admin or individually issued.
     */
    public function isCertificateAvailableFor(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Admin can always preview/view certificates
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        // Check if an individual certificate was already generated/issued by admin
        if ($this->certificates()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Course-wide certificate release: requires certificate_enabled = true AND active enrollment/payment
        if ($this->certificate_enabled) {
            $isEnrolled = $user->enrolledCourses()->where('course_id', $this->id)->exists();
            $isVerifiedPaid = $user->payments()->where('course_id', $this->id)->where('status', 'verified')->exists();
            return $isEnrolled || $isVerifiedPaid;
        }

        return false;
    }
}
