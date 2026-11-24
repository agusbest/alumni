<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slam_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->foreignId('to_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->string('body', 400);
            $table->timestamps();
            $table->index('to_profile_id');
        });

        Schema::create('salams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->foreignId('to_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['from_profile_id', 'to_profile_id']);
        });

        Schema::create('yearbook_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->unsignedSmallInteger('angkatan');
            $table->string('kelas', 20);
            $table->string('body', 300);
            $table->timestamps();
            $table->index(['angkatan', 'kelas']);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 80);
            $table->text('body');
            $table->boolean('pinned')->default(false);
            $table->timestamps();
            $table->index(['pinned', 'created_at']);
        });

        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('body', 200);
            $table->timestamps();
        });

        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 8);
            $table->unique(['post_id', 'user_id', 'emoji']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('yearbook_messages');
        Schema::dropIfExists('salams');
        Schema::dropIfExists('slam_messages');
    }
};
