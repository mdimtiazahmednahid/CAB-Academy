<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    /**
     * Display the public directory of instructors and fellows.
     */
    public function index()
    {
        abort_if(!\App\Models\Setting::isPageEnabled('instructors') && (!auth()->check() || !auth()->user()->isAdmin()), 404);
        \App\Support\DatabaseSchemaRepair::ensureSchema();

        $instructors = Instructor::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        $departments = \App\Models\Category::active()
            ->ordered()
            ->with('headInstructor')
            ->get();

        $courses = Course::where('is_published', true)->get();

        return view('faculty.index', compact('instructors', 'departments', 'courses'));
    }
}
