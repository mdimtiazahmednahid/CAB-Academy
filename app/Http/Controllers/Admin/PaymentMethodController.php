<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::all();
        return view('admin.payment_methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        PaymentMethod::create($validated);

        return back()->with('success', 'Payment method added successfully.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'provider_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $paymentMethod->update($validated);

        return back()->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return back()->with('success', 'Payment method deleted successfully.');
    }
}
