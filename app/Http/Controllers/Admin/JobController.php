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
        $jobs = JobPost::withCount('applications')->latest()->get();
        return view('admin.jobs.index', compact('jobs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'job_type' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'apply_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'company_logo' => 'nullable|image|max:2048',
            'company_logo_base64' => 'nullable|string',
        ]);
        
        $data['job_type'] = $request->input('job_type', 'Full-time') ?: 'Full-time';
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
                    if (is_dir(public_path('storage/company_logos'))) {
                        @file_put_contents(public_path('storage/company_logos/' . $filename), $decoded);
                    }
                    $data['company_logo'] = 'company_logos/' . $filename;
                }
            }
        }
        unset($data['company_logo_base64']);

        $job = JobPost::create($data);

        if ($job->is_active) {
            try {
                $users = User::all()->filter(function ($u) {
                    $prefs = is_array($u->notification_preferences) ? $u->notification_preferences : null;
                    return $prefs === null || ($prefs['new_jobs'] ?? true) === true;
                });
                if ($users->isNotEmpty()) {
                    Notification::send($users, new NewJobNotification($job));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Job publish notification skipped: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Job posted successfully!');
    }

    public function update(Request $request, JobPost $job)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'job_type' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'apply_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'company_logo' => 'nullable|image|max:2048',
            'company_logo_base64' => 'nullable|string',
        ]);
        
        $data['job_type'] = $request->input('job_type', $job->job_type ?? 'Full-time') ?: 'Full-time';
        $data['is_active'] = $request->has('is_active');

        if ($request->boolean('remove_company_logo')) {
            $data['company_logo'] = null;
        } elseif ($request->hasFile('company_logo')) {
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
                    if (is_dir(public_path('storage/company_logos'))) {
                        @file_put_contents(public_path('storage/company_logos/' . $filename), $decoded);
                    }
                    $data['company_logo'] = 'company_logos/' . $filename;
                }
            }
        }
        unset($data['company_logo_base64']);

        $wasActive = $job->is_active;

        $job->update($data);

        if (!$wasActive && $job->is_active) {
            try {
                $users = User::all()->filter(function ($u) {
                    $prefs = is_array($u->notification_preferences) ? $u->notification_preferences : null;
                    return $prefs === null || ($prefs['new_jobs'] ?? true) === true;
                });
                if ($users->isNotEmpty()) {
                    Notification::send($users, new NewJobNotification($job));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Job update notification skipped: ' . $e->getMessage());
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

    public function updateApplicationStatus(Request $request, \App\Models\JobApplication $application)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,reviewed,accepted,rejected',
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('success', 'Applicant status updated successfully.');
    }

    public function destroyApplication(\App\Models\JobApplication $application)
    {
        $application->delete();
        return back()->with('success', 'Candidate application removed.');
    }
}
