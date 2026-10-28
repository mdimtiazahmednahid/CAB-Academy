<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:promote {email}', function ($email) {
    $user = \App\Models\User::withTrashed()->where('email', $email)->first();
    if (!$user) {
        $this->error("User with email [{$email}] was not found.");
        return 1;
    }
    if ($user->trashed()) {
        $user->restore();
    }
    $user->role = 'admin';
    $user->email_verified_at = now();
    $user->save();
    $this->info("User [{$user->name}] ({$user->email}) is now an Administrator with full permissions!");
    return 0;
})->purpose('Promote a user account to full Administrator privileges');

Artisan::command('admin:fix-roles', function () {
    // 1. Normalize roles
    \Illuminate\Support\Facades\DB::table('users')
        ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(role)'), ['admin', 'superadmin', 'super_admin', 'super admin', 'manager'])
        ->update(['role' => 'admin']);

    // 2. Official emails
    \Illuminate\Support\Facades\DB::table('users')
        ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(email)'), [
            'admin@cab-academy.com',
            'commercialacademy868@gmail.com'
        ])
        ->update(['role' => 'admin']);

    $admins = \App\Models\User::where('role', 'admin')->get(['id', 'name', 'email', 'role']);
    $this->info("Admin role normalization complete. Verified administrators:");
    foreach ($admins as $a) {
        $this->line(" • #{$a->id} {$a->name} ({$a->email}) -> role: {$a->role}");
    }
    return 0;
})->purpose('Normalize administrator accounts and repair any 403 authorization discrepancies');

Artisan::command('instructors:fix', function () {
    $this->info("Checking and resolving instructor-user account associations...");
    $instructors = \App\Models\Instructor::all();
    $repaired = 0;

    foreach ($instructors as $inst) {
        if (!empty($inst->email)) {
            $matchingUser = \App\Models\User::where('email', $inst->email)->first();
            if ($matchingUser && $inst->user_id !== $matchingUser->id) {
                $oldId = $inst->user_id;
                $inst->user_id = $matchingUser->id;
                $inst->saveQuietly();
                $this->line(" • Relinked '{$inst->name}' ({$inst->email}) from user #{$oldId} to user #{$matchingUser->id} ({$matchingUser->name})");
                $repaired++;
            }
        }
    }

    if ($repaired === 0) {
        $this->info("All instructor profiles and user accounts are cleanly mapped with zero collisions.");
    } else {
        $this->info("Successfully resolved {$repaired} instructor-user account conflict(s)!");
    }
    return 0;
})->purpose('Resolve duplicate email collisions and synchronize instructor profile user IDs');


