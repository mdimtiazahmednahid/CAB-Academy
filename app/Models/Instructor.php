<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'expertise',
        'avatar',
        'bio',
        'email',
        'phone',
        'order',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id', 'user_id');
    }

    /**
     * Get fully-qualified avatar image URL with reliable storage prefixing and fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        $default = 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=051c45&color=fff';
        if (!$this->avatar) {
            return $default;
        }
        return storage_asset($this->avatar, $default);
    }
}
