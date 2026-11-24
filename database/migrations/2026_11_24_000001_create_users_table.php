<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role', 10)->default('alumni');      // alumni | admin
            $table->string('status', 10)->default('pending');   // pending | active | rejected
            // Data pendaftaran (dipakai admin saat verifikasi)
            $table->unsignedSmallInteger('reg_angkatan')->nullable();
            $table->string('reg_kelas', 20)->nullable();
            $table->unsignedBigInteger('claim_profile_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
