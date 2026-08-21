<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, modifying ENUMs can be tricky. A safe way is to change the column to a string type, 
        // since ENUM is mostly emulated. 
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
        
        // Convert existing 'approved' to 'verified'
        DB::table('payments')->where('status', 'approved')->update(['status' => 'verified']);
    }

    public function down(): void
    {
        DB::table('payments')->where('status', 'verified')->update(['status' => 'approved']);
        
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};
