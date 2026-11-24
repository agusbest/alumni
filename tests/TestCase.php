<?php

namespace Tests;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static int $seq = 0;

    /** Buat alumni aktif lengkap dengan profil yang sudah diklaim. */
    protected function alumnus(array $profile = [], array $vis = [], array $user = []): User
    {
        $n = ++self::$seq;
        $u = User::create(array_merge([
            'name' => "Alumni $n", 'email' => "alumni$n@test.test", 'password' => 'password',
            'role' => 'alumni', 'status' => 'active',
        ], $user));

        $p = new AlumniProfile(array_merge([
            'name' => $u->name, 'angkatan' => 2004, 'kelas' => '3 IPA 1',
            'visibility' => array_merge(AlumniProfile::defaultVisibility(), $vis),
        ], $profile));
        $p->forceFill(['user_id' => $u->id, 'is_claimed' => true])->save();

        return $u->fresh();
    }

    protected function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin'.(++self::$seq).'@test.test', 'password' => 'password',
            'role' => 'admin', 'status' => 'active',
        ]);
    }
}
