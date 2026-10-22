<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalize any uppercase or variation admin roles to clean lowercase 'admin'
        DB::table('users')
            ->whereIn(DB::raw('LOWER(role)'), ['admin', 'superadmin', 'super_admin', 'super admin', 'manager'])
            ->update(['role' => 'admin']);

        // 2. Ensure official CAB Academy admin and owner emails have role = 'admin'
        DB::table('users')
            ->whereIn(DB::raw('LOWER(email)'), [
                'admin@cab-academy.com',
                'commercialacademy868@gmail.com'
            ])
            ->update(['role' => 'admin']);

        // 3. If there is a sole user in the database or user #1, ensure they have role = 'admin'
        $firstUser = DB::table('users')->orderBy('id', 'asc')->first();
        if ($firstUser && in_array(strtolower($firstUser->role), ['student', 'user', ''])) {
            DB::table('users')->where('id', $firstUser->id)->update(['role' => 'admin']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal necessary
    }
};
