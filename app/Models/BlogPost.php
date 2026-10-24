<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'meta_description',
        'content',
        'featured_image',
        'author_name',
        'author_title',
        'author_avatar',
        'read_time',
        'is_published',
        'is_featured',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_published', true)->where('is_featured', true);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get fully-qualified featured image URL with storage prefixing and fallback.
     */
    public function getFeaturedImageUrlAttribute(): string
    {
        $default = 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80';
        if (!$this->featured_image) {
            return $default;
        }
        return storage_asset($this->featured_image, $default);
    }
}
