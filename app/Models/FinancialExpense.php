<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'paid_to',
        'payment_method',
        'reference_no',
        'receipt_file',
        'notes',
        'created_by',
    ];

    protected $attributes = [
        'paid_to' => 'General Vendor',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
