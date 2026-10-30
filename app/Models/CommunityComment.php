<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'community_post_id',
        'user_id',
        'author_name',
        'author_avatar',
        'content',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'community_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getFormattedContentHtmlAttribute(): string
    {
        return \App\Support\LinkPreviewResolver::formatTextWithClickableLinks($this->content);
    }
}
