<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            // Profil lama bisa belum punya akun (user_id null) sampai diklaim.
            $table->foreignId('user_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->string('nickname', 40)->nullable();
            $table->unsignedSmallInteger('angkatan');
            $table->string('kelas', 20);
            $table->string('absen', 4)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('occupation', 120)->nullable();
            $table->string('company', 120)->nullable();
            $table->string('status_text', 120)->nullable();
            $table->string('social', 120)->nullable();
            $table->string('ekskul', 120)->nullable();
            $table->string('geng', 120)->nullable();
            $table->string('favorite_teacher', 120)->nullable();
            $table->string('favorite_subject', 120)->nullable();
            $table->text('memory')->nullable();
            $table->string('dream_then', 120)->nullable();
            $table->string('dream_now', 120)->nullable();
            $table->string('favorite_song', 120)->nullable();
            $table->string('wa', 20)->nullable();
            $table->string('email_public', 120)->nullable();
            $table->string('photo_then_path')->nullable();
            $table->string('photo_now_path')->nullable();
            $table->json('visibility')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->timestamps();

            $table->index(['angkatan', 'kelas']);
            $table->index('city');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
