<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add dynamic enrollment fields to courses table
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'enrollment_fields')) {
                    $table->json('enrollment_fields')->nullable();
                }
            });
        }

        // Add dynamic form data storage to payments table
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'form_data')) {
                    $table->json('form_data')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'enrollment_fields')) {
                    $table->dropColumn('enrollment_fields');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'form_data')) {
                    $table->dropColumn('form_data');
                }
            });
        }
    }
};
