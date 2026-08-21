<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
