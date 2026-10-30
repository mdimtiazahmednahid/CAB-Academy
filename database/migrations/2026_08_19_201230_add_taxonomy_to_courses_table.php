<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'level')) {
                    $table->string('level')->nullable();
                }
                if (!Schema::hasColumn('courses', 'category')) {
                    $table->string('category')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'category']);
        });
    }
};
