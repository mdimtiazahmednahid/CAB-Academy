<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'provider_name',
        'account_number',
        'account_type',
        'instructions',
        'logo',
        'is_active',
    ];
}
