<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display the authenticated student's saved courses wishlist.
     */
    public function index()
    {
        $user = auth()->user();
        $wishlists = $user ? $user->wishlists()->with('course')->latest()->get() : collect();
        $courses = $wishlists->pluck('course')->filter();

        return view('wishlist.index', compact('courses'));
    }

    /**
     * Toggle a course in the student's wishlist.
     */
    public function toggle(Request $request, Course $course)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Please sign in to save courses to your wishlist.',
                    'redirect' => route('login'),
                ], 401);
            }
            return redirect()->route('login')->with('info', 'Please sign in to save courses to your wishlist.');
        }

        $user = auth()->user();
        $existing = Wishlist::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Course removed from your wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $status = 'added';
            $message = 'Course added to your wishlist!';
        }

        $count = $user->wishlists()->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
