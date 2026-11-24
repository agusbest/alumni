<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('album_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path')->nullable();          // null = gambar contoh (seed)
            $table->unsignedSmallInteger('hue')->nullable(); // untuk gambar contoh
            $table->string('caption', 100);
            $table->unsignedSmallInteger('year');
            $table->string('status', 10)->default('ok'); // ok | pending
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('album_photo_profile', function (Blueprint $table) {
            $table->foreignId('album_photo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alumni_profile_id')->constrained('alumni_profiles')->cascadeOnDelete();
            $table->primary(['album_photo_id', 'alumni_profile_id']);
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80);
            $table->dateTime('starts_at');
            $table->string('place', 100);
            $table->string('description', 300)->nullable();
            $table->timestamps();
            $table->index('starts_at');
        });

        Schema::create('event_user', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_user');
        Schema::dropIfExists('events');
        Schema::dropIfExists('album_photo_profile');
        Schema::dropIfExists('album_photos');
    }
};
