<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        abort_if(!\App\Models\Setting::isPageEnabled('contact') && (!auth()->check() || !auth()->user()->isAdmin()), 404);
        return view('contact.index');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'inquiry_type' => 'required|string|in:general,corporate,course_admission,consulting',
            'organization' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        $validated['status'] = 'pending';

        ContactInquiry::create($validated);

        return redirect()->back()->with('success', 'Thank you! Your message has been received. Our executive advisory team will contact you shortly.');
    }
}
