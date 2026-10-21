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
        // 1. Contact Inquiries Table
        if (!Schema::hasTable('contact_inquiries')) {
            Schema::create('contact_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('inquiry_type')->default('admission'); // admission, corporate, general
                $table->string('organization')->nullable();
                $table->string('subject')->nullable();
                $table->text('message');
                $table->string('status')->default('new'); // new, in_progress, resolved
                $table->timestamps();
            });
        }

        // 2. Course Wishlists Table
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
            });
        }

        // 3. Certificates Table
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->string('certificate_number')->unique();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->date('issue_date');
                $table->string('verification_hash')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('contact_inquiries');
    }
};
