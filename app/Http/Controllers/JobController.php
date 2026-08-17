<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobPost::where('is_active', true)->latest()->get();
        return view('jobs.index', compact('jobs'));
    }
}
