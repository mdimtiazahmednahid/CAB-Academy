<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'provider_name',
        'account_number',
        'account_type',
        'instructions',
        'logo',
        'is_active',
    ];
}
