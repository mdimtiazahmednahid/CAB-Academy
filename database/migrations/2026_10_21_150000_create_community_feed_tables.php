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
        if (!Schema::hasTable('community_posts')) {
            Schema::create('community_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('author_name');
                $table->string('author_title')->nullable();
                $table->string('author_avatar')->nullable();
                $table->string('category')->default('General');
                $table->string('title')->nullable();
                $table->text('content');
                $table->string('image_url')->nullable();
                $table->foreignId('blog_post_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedInteger('likes_count')->default(0);
                $table->unsignedInteger('comments_count')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('community_comments')) {
            Schema::create('community_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('author_name');
                $table->string('author_avatar')->nullable();
                $table->text('content');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('community_post_likes')) {
            Schema::create('community_post_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->unique(['community_post_id', 'user_id', 'ip_address'], 'post_user_ip_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_post_likes');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_posts');
    }
};
