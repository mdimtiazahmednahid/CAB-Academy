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
        if (!Schema::hasColumn('job_posts', 'job_type')) {
            Schema::table('job_posts', function (Blueprint $table) {
                $table->string('job_type')->default('Full-time')->nullable()->after('company');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('job_posts', 'job_type')) {
            Schema::table('job_posts', function (Blueprint $table) {
                $table->dropColumn('job_type');
            });
        }
    }
};
