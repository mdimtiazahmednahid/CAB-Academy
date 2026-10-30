<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!\App\Models\Setting::isPageEnabled('jobs') && (!auth()->check() || !auth()->user()->isAdmin()), 404);

        $baseActiveQuery = JobPost::where('is_active', true);
        $totalActiveJobs = (clone $baseActiveQuery)->count();
        $distinctCompaniesCount = (clone $baseActiveQuery)->distinct('company')->count('company');

        $allActiveJobsForCounts = (clone $baseActiveQuery)->select('job_type')->get();
        $typeCounts = [
            'all' => $totalActiveJobs,
            'Full-time' => $allActiveJobsForCounts->filter(fn($j) => stripos($j->job_type ?? 'Full-time', 'Full-time') !== false)->count(),
            'Remote' => $allActiveJobsForCounts->filter(fn($j) => stripos($j->job_type ?? '', 'Remote') !== false)->count(),
            'Contract' => $allActiveJobsForCounts->filter(fn($j) => stripos($j->job_type ?? '', 'Contract') !== false)->count(),
            'Part-time' => $allActiveJobsForCounts->filter(fn($j) => stripos($j->job_type ?? '', 'Part-time') !== false)->count(),
            'Internship' => $allActiveJobsForCounts->filter(fn($j) => stripos($j->job_type ?? '', 'Internship') !== false)->count(),
        ];

        $query = JobPost::where('is_active', true);

        if ($request->filled('type') && $request->type !== 'all') {
            $type = $request->type;
            $query->where(function ($q) use ($type) {
                $q->where('job_type', $type)
                  ->orWhere('job_type', 'like', "%{$type}%");
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->input('sort') === 'company') {
            $query->orderBy('company')->latest();
        } else {
            $query->latest();
        }

        $jobs = $query->get();

        $appliedJobIds = auth()->check()
            ? JobApplication::where('user_id', auth()->id())->pluck('job_post_id')->toArray()
            : [];

        return view('jobs.index', compact(
            'jobs',
            'totalActiveJobs',
            'distinctCompaniesCount',
            'appliedJobIds',
            'typeCounts'
        ));
    }
}
