<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobPost::latest()->get();
        return view('admin.jobs.index', compact('jobs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'apply_link' => 'nullable|url',
            'is_active' => 'boolean',
            'company_logo' => 'nullable|image|max:2048'
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        JobPost::create($data);
        return back()->with('success', 'Job posted successfully!');
    }

    public function update(Request $request, JobPost $job)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'apply_link' => 'nullable|url',
            'is_active' => 'boolean',
            'company_logo' => 'nullable|image|max:2048'
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        $job->update($data);
        return back()->with('success', 'Job updated successfully!');
    }

    public function show(JobPost $job)
    {
        $job->load(['applications.user']);
        return view('admin.jobs.show', compact('job'));
    }

    public function destroy(JobPost $job)
    {
        $job->delete();
        return back()->with('success', 'Job deleted successfully!');
    }
}
