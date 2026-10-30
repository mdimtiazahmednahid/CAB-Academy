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

    public function getAuthorAvatarUrlAttribute(): ?string
    {
        if ($this->user && $this->user->hasCustomProfilePicture()) {
            return $this->user->profile_picture_url;
        }
        if (!empty($this->author_avatar)) {
            return storage_asset($this->author_avatar);
        }
        return null;
    }

    public function getSharedUrlAttribute(): ?string
    {
        return \App\Support\LinkPreviewResolver::extractFirstUrl($this->content);
    }

    public function getLinkPreviewAttribute(): ?array
    {
        $url = $this->shared_url;
        if (!$url) {
            return null;
        }
        return \App\Support\LinkPreviewResolver::resolve($url);
    }

    public function getFormattedContentHtmlAttribute(): string
    {
        return \App\Support\LinkPreviewResolver::formatTextWithClickableLinks($this->content);
    }

    /**
     * Auto-generated or converted Uppercase Bold Serif header for Facebook-style feed posts.
     * Example: "𝐏𝐆𝐃 𝐈𝐍 𝐒𝐔𝐏𝐏𝐋𝐘 𝐂𝐇𝐀𝐈𝐍 𝐌𝐀𝐍𝐀𝐆𝐄𝐌𝐄𝐍𝐓"
     */
    public function getStylishTitleAttribute(): ?string
    {
        if (!empty($this->title)) {
            return \App\Support\StylishTextFormatter::toUpperBoldSerif($this->title);
        }

        if ($this->blogPost) {
            return $this->blogPost->stylish_post_header;
        }

        $preview = $this->link_preview;
        if (is_array($preview) && !empty($preview['post_header'])) {
            return $preview['post_header'];
        }

        return null;
    }
}
