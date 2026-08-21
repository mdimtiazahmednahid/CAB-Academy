<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function store(Request $request, JobPost $job)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
        ]);

        if (JobApplication::where('user_id', auth()->id())->where('job_post_id', $job->id)->exists()) {
            return back()->with('error', 'You have already applied for this job.');
        }

        $path = $request->file('resume')->store('resumes', 'public');

        JobApplication::create([
            'user_id' => auth()->id(),
            'job_post_id' => $job->id,
            'resume_path' => $path,
            'cover_letter' => $request->cover_letter,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your application has been submitted successfully.');
    }
}
