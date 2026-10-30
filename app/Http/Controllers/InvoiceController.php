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

        if ($payment->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access to this payment receipt.');
        }

        if (!$user->isAdmin() && $payment->status !== 'verified') {
            return redirect()->route('dashboard')
                ->with('error', 'Official tuition invoice will be available for download once your payment is verified and approved by an administrator.');
        }

        $payment->load(['course', 'user', 'paymentMethod']);

        return view('payments.invoice', compact('payment'));
    }
}
