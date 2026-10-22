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

