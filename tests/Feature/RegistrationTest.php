<?php

namespace Tests\Feature;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $over = []): array
    {
        return array_merge([
            'name' => 'Calon Alumni', 'email' => 'calon@test.test', 'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123', 'angkatan' => 2004, 'kelas' => '3 IPA 1',
        ], $over);
    }

    public function test_new_registration_is_pending_and_cannot_login(): void
    {
        $this->post(route('register.store'), $this->payload())->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', ['email' => 'calon@test.test', 'status' => 'pending']);
        $this->post(route('login.attempt'), ['email' => 'calon@test.test', 'password' => 'rahasia123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_year_must_be_in_2000s(): void
    {
        $this->post(route('register.store'), $this->payload(['angkatan' => 1998]))->assertSessionHasErrors('angkatan');
    }

    public function test_admin_approval_creates_profile_and_allows_login(): void
    {
        $this->post(route('register.store'), $this->payload());
        $user = User::where('email', 'calon@test.test')->first();

        $this->actingAs($this->admin())->post(route('admin.approve', $user))->assertRedirect();
        $this->post('/keluar');

        $user->refresh();
        $this->assertSame('active', $user->status);
        $this->assertTrue($user->profile->is_claimed);
        $this->assertSame(2004, (int) $user->profile->angkatan);
        $this->assertSame('hidden', $user->profile->visibility['wa']);

        $this->post(route('login.attempt'), ['email' => 'calon@test.test', 'password' => 'rahasia123'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_claiming_an_old_profile_links_it_to_the_account(): void
    {
        $old = new AlumniProfile(['name' => 'Tono Lama', 'angkatan' => 2003, 'kelas' => '3 IPS 1', 'visibility' => AlumniProfile::defaultVisibility()]);
        $old->save();

        $this->post(route('register.store'), $this->payload(['name' => 'Tono', 'angkatan' => 2003, 'kelas' => '3 IPS 1', 'claim_profile_id' => $old->id]));
        $user = User::where('email', 'calon@test.test')->first();
        $this->actingAs($this->admin())->post(route('admin.approve', $user));

        $old->refresh();
        $this->assertTrue($old->is_claimed);
        $this->assertSame($user->id, $old->user_id);
        $this->assertSame(1, AlumniProfile::count());
    }

    public function test_already_claimed_profile_cannot_be_claimed_again(): void
    {
        $owner = $this->alumnus();

        $this->post(route('register.store'), $this->payload(['claim_profile_id' => $owner->profile->id]))
            ->assertSessionHasErrors('claim_profile_id');
    }

    public function test_admin_area_is_forbidden_for_regular_alumni_and_guests(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('login'));
        $this->actingAs($this->alumnus())->get(route('admin.index'))->assertForbidden();
        $this->actingAs($this->admin())->get(route('admin.index'))->assertOk();
    }
}
