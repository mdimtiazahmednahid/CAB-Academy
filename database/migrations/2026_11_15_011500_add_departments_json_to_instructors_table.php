<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('instructors') && !Schema::hasColumn('instructors', 'departments')) {
            Schema::table('instructors', function (Blueprint $table) {
                $table->json('departments')->nullable()->after('department');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('instructors') && Schema::hasColumn('instructors', 'departments')) {
            Schema::table('instructors', function (Blueprint $table) {
                $table->dropColumn('departments');
            });
        }
    }
};
