<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * Display a listing of customer & corporate contact inquiries.
     */
    public function index(Request $request)
    {
        $query = ContactInquiry::latest();

        if ($request->filled('type')) {
            $query->where('inquiry_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inquiries = $query->paginate(15);
        $pendingCount = ContactInquiry::where('status', 'pending')->count();

        return view('admin.inquiries.index', compact('inquiries', 'pendingCount'));
    }

    /**
     * Display the specified inquiry details.
     */
    public function show(ContactInquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    /**
     * Update the status of an inquiry.
     */
    public function updateStatus(Request $request, ContactInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,contacted,resolved,archived',
        ]);

        $inquiry->update($validated);

        return redirect()->back()->with('success', 'Inquiry status updated to ' . ucfirst($validated['status']));
    }

    /**
     * Remove the inquiry from storage.
     */
    public function destroy(ContactInquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry record deleted successfully.');
    }
}
