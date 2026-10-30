<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (auth()->user()->preferences !== null) {
            return redirect()->route('dashboard');
        }
        return view('onboarding');
    }

    public function store(Request $request)
    {
        if ($request->boolean('skip')) {
            $user = auth()->user();
            $user->preferences = [
                'level' => 'Professional',
                'subjects' => ['Export-Import & L/C Operations', 'Supply Chain & Logistics'],
                'goals' => ['Advance Corporate Career'],
            ];
            $user->save();

            return response()->json(['redirect' => route('dashboard')]);
        }

        $validated = $request->validate([
            'level' => 'required|string',
            'subjects' => 'required|array',
            'goals' => 'required|array',
        ]);

        $user = auth()->user();
        $user->preferences = $validated;
        $user->save();

        return response()->json(['redirect' => route('dashboard')]);
    }
}
