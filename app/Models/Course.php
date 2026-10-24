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
        'target_audience',
        'price',
        'level',
        'category',
        'instructor_id',
        'views'
    ];

    protected $casts = [
        'start_date' => 'date',
        'price' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get fully-qualified cover image URL with reliable storage prefixing and fallback.
     */
    public function getCoverImageUrlAttribute(): string
    {
        $img = $this->cover_image ?: $this->thumbnail;
        if (!$img) {
            return 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';
        }
        return storage_asset($img, 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80');
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
}
