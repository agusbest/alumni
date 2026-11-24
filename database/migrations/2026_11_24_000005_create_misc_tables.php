<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guestbook_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->unsignedSmallInteger('angkatan')->nullable();
            $table->string('body', 200);
            $table->timestamps();
            $table->index('angkatan');
        });

        Schema::create('playlist_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 60);
            $table->string('artist', 60);
            $table->string('url', 200)->nullable();
            $table->unsignedSmallInteger('angkatan');
            $table->timestamps();
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type', 3);                  // in | out
            $table->string('name', 60);
            $table->unsignedBigInteger('amount');
            $table->string('note', 80)->nullable();
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 10);                 // profile | post
            $table->unsignedBigInteger('target_id');
            $table->string('label', 120);
            $table->string('reason', 120);
            $table->boolean('is_done')->default(false);
            $table->timestamps();
            $table->index('is_done');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 60)->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('playlist_tracks');
        Schema::dropIfExists('guestbook_entries');
    }
};
