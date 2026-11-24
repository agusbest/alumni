<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_hidden_contact_is_not_rendered_for_other_alumni(): void
    {
        $owner = $this->alumnus(['wa' => '0812-9999-0001', 'email_public' => 'rahasia@test.test'], ['wa' => 'hidden', 'email' => 'hidden']);
        $viewer = $this->alumnus();

        $res = $this->actingAs($viewer)->get(route('galeri.show', ['profile' => $owner->profile->id, 'tab' => 'kontak']));

        $res->assertOk()->assertSee('Disembunyikan')->assertDontSee('0812-9999-0001')->assertDontSee('rahasia@test.test');
    }

    public function test_owner_can_see_own_hidden_fields(): void
    {
        $owner = $this->alumnus(['wa' => '0812-9999-0002'], ['wa' => 'hidden']);

        $this->actingAs($owner)->get(route('galeri.show', ['profile' => $owner->profile->id, 'tab' => 'kontak']))
            ->assertOk()->assertSee('0812-9999-0002');
    }

    public function test_guest_is_redirected_for_non_public_profiles_but_sees_public_ones(): void
    {
        $private = $this->alumnus([], ['photo' => 'alumni']);
        $public = $this->alumnus(['city' => 'Bandung'], ['photo' => 'public', 'city' => 'alumni']);

        $this->get(route('galeri.show', $private->profile->id))->assertRedirect(route('login'));

        $this->get(route('galeri.show', ['profile' => $public->profile->id, 'tab' => 'sekarang']))
            ->assertOk()->assertDontSee('Bandung');
    }

    public function test_gallery_for_guests_lists_only_public_profiles(): void
    {
        $private = $this->alumnus(['name' => 'Tersembunyi Satu'], ['photo' => 'alumni']);
        $public = $this->alumnus(['name' => 'Terlihat Dua'], ['photo' => 'public']);

        $this->get(route('galeri.index'))->assertOk()->assertSee('Terlihat Dua')->assertDontSee('Tersembunyi Satu');
        $this->actingAs($this->alumnus())->get(route('galeri.index'))->assertSee('Tersembunyi Satu')->assertSee('Terlihat Dua');
    }

    public function test_city_filter_does_not_leak_hidden_city(): void
    {
        $this->alumnus(['name' => 'Orang Bandung', 'city' => 'Bandung'], ['city' => 'hidden']);
        $this->alumnus(['name' => 'Orang Bandung Terbuka', 'city' => 'Bandung'], ['city' => 'alumni']);

        $this->actingAs($this->alumnus())->get(route('galeri.index', ['kota' => 'Bandung']))
            ->assertOk()->assertSee('Orang Bandung Terbuka')->assertDontSee('Orang Bandung<');
    }

    public function test_angkatan_level_only_visible_to_same_class_year(): void
    {
        $owner = $this->alumnus(['city' => 'Medan', 'angkatan' => 2004], ['city' => 'angkatan']);
        $same = $this->alumnus(['angkatan' => 2004]);
        $other = $this->alumnus(['angkatan' => 2005]);
        $url = route('galeri.show', ['profile' => $owner->profile->id, 'tab' => 'sekarang']);

        $this->actingAs($same)->get($url)->assertSee('Medan');
        $this->actingAs($other)->get($url)->assertDontSee('Medan');
    }

    public function test_hidden_photo_is_served_as_lock_placeholder(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('profiles/x.jpg', 'RAHASIA-BINER');
        $owner = $this->alumnus([], ['photo' => 'hidden']);
        $owner->profile->forceFill(['photo_now_path' => 'profiles/x.jpg'])->save();

        $res = $this->actingAs($this->alumnus())->get(route('photo', [$owner->profile->id, 'now']));
        $res->assertOk();
        $this->assertStringContainsString('svg', (string) $res->headers->get('Content-Type'));
        $this->assertStringNotContainsString('RAHASIA-BINER', $res->getContent());
    }

    public function test_map_only_counts_cities_visible_to_viewer(): void
    {
        $this->alumnus(['city' => 'Kota Rahasia'], ['city' => 'hidden']);
        $this->alumnus(['city' => 'Kota Terbuka'], ['city' => 'alumni']);

        $this->actingAs($this->alumnus())->get(route('map.index'))
            ->assertOk()->assertSee('Kota Terbuka')->assertDontSee('Kota Rahasia');
    }
}
