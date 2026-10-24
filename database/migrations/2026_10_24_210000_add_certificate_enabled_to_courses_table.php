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
        if (!Schema::hasColumn('courses', 'certificate_enabled')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->boolean('certificate_enabled')->default(false)->after('is_published');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'certificate_enabled')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('certificate_enabled');
            });
        }
    }
};
