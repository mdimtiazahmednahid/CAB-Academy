<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'title',
        'company',
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
}
