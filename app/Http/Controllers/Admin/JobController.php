<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\User;
use App\Notifications\NewJobNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

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
            'apply_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'company_logo' => 'nullable|image|max:2048'
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        $job = JobPost::create($data);

        if ($job->is_active) {
            $users = User::where(function($q) {
                $q->whereNull('notification_preferences')
                  ->orWhereJsonContains('notification_preferences->new_jobs', true);
            })->get();
            if ($users->isNotEmpty()) {
                Notification::send($users, new NewJobNotification($job));
            }
        }

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
            'apply_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'company_logo' => 'nullable|image|max:2048',
            'company_logo_base64' => 'nullable|string',
        ]);
        
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        } elseif ($request->filled('company_logo_base64')) {
            $base64 = $request->input('company_logo_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);
                $decoded = base64_decode($base64);
                if ($decoded !== false) {
                    $filename = 'logo_' . time() . '_' . uniqid() . '.' . $type;
                    Storage::disk('public')->put('company_logos/' . $filename, $decoded);
                    $data['company_logo'] = 'company_logos/' . $filename;
                }
            }
        }
        unset($data['company_logo_base64']);

        $wasActive = $job->is_active;

        $job->update($data);

        if (!$wasActive && $job->is_active) {
            $users = User::where(function($q) {
                $q->whereNull('notification_preferences')
                  ->orWhereJsonContains('notification_preferences->new_jobs', true);
            })->get();
            if ($users->isNotEmpty()) {
                Notification::send($users, new NewJobNotification($job));
            }
        }

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
