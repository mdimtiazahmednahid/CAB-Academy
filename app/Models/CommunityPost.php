<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'author_name',
        'author_title',
        'author_avatar',
        'category',
        'title',
        'content',
        'image_url',
        'blog_post_id',
        'likes_count',
        'comments_count',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class)->latest();
    }

    public function likes()
    {
        return $this->hasMany(CommunityPostLike::class);
    }

    public function isLikedBy($user, $ip = null)
    {
        if ($user) {
            return $this->likes()->where('user_id', $user->id)->exists();
        }

        if ($ip) {
            return $this->likes()->where('ip_address', $ip)->exists();
        }

        return false;
    }
}
