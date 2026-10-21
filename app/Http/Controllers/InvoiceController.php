<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Show official printable student tuition receipt & tax invoice.
     */
    public function show(Payment $payment)
    {
        $user = auth()->user();

        if ($payment->user_id !== $user->id && !in_array($user->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized access to this payment receipt.');
        }

        $payment->load(['course', 'user', 'paymentMethod']);

        return view('payments.invoice', compact('payment'));
    }
}
