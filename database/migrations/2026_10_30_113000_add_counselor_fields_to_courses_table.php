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
                if (!Schema::hasColumn('courses', 'counselor_phone')) {
                    $table->string('counselor_phone', 50)->nullable()->after('enrollment_fields');
                }
                if (!Schema::hasColumn('courses', 'whatsapp_message')) {
                    $table->text('whatsapp_message')->nullable()->after('counselor_phone');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'whatsapp_message')) {
                    $table->dropColumn('whatsapp_message');
                }
                if (Schema::hasColumn('courses', 'counselor_phone')) {
                    $table->dropColumn('counselor_phone');
                }
            });
        }
    }
};

