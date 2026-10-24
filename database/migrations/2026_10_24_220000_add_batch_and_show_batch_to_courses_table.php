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
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'batch')) {
                $table->string('batch')->nullable()->after('venue');
            }
            if (!Schema::hasColumn('courses', 'show_batch')) {
                $table->boolean('show_batch')->default(false)->after('batch');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'show_batch')) {
                $table->dropColumn('show_batch');
            }
            if (Schema::hasColumn('courses', 'batch')) {
                $table->dropColumn('batch');
            }
        });
    }
};
