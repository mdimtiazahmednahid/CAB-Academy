<?php

use App\Support\DatabaseSchemaRepair;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DatabaseSchemaRepair::ensureSchema(true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Idempotent schema verification
    }
};
