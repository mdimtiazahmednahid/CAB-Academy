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
        'thumbnail',
        'is_published',
        'cover_image',
        'duration',
        'price',
        'level',
        'category',
        'instructor_id',
        'views'
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
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
