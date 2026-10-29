<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'company',
        'job_type',
        'location',
        'salary_range',
        'description',
        'apply_link',
        'is_active',
        'company_logo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get fully-qualified company logo URL with storage prefixing and fallback.
     */
    public function getCompanyLogoUrlAttribute(): ?string
    {
        if (!$this->company_logo) {
            return null;
        }
        return storage_asset($this->company_logo);
    }
}
