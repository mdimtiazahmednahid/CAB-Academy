<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::where('is_active', true)->latest();

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

        $jobs = $query->get();

        return view('jobs.index', compact('jobs'));
    }
}
