<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Post;
use App\Models\SlamMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_only_pages_redirect_guests(): void
    {
        foreach (['slam.index', 'yearbook.show', 'album.index', 'playlist.index', 'ledger.index', 'map.index', 'profile.edit'] as $name) {
            $this->get(route($name))->assertRedirect(route('login'));
        }
        foreach (['home', 'mading.index', 'events.index', 'guestbook.index', 'kuis', 'snake', 'galeri.index'] as $name) {
            $this->get(route($name))->assertOk();
        }
    }

    public function test_slam_message_is_stored_and_shown(): void
    {
        $a = $this->alumnus();
        $b = $this->alumnus();

        $this->actingAs($a)->post(route('slam.store'), ['to_profile_id' => $b->profile->id, 'body' => 'Halo sahabat lama!'])->assertRedirect();
        $this->assertDatabaseHas('slam_messages', ['from_profile_id' => $a->profile->id, 'body' => 'Halo sahabat lama!']);

        $this->actingAs($b)->get(route('slam.index'))->assertSee('Halo sahabat lama!');
    }

    public function test_cannot_write_in_own_or_unclaimed_slam_book(): void
    {
        $a = $this->alumnus();
        $this->actingAs($a)->post(route('slam.store'), ['to_profile_id' => $a->profile->id, 'body' => 'Aku sendiri'])->assertSessionHasErrors('to_profile_id');
        $this->assertSame(0, SlamMessage::count());
    }

    public function test_profile_update_only_changes_known_visibility_fields(): void
    {
        $a = $this->alumnus();
        $this->actingAs($a)->put(route('profile.update'), [
            'name' => 'Nama Baru', 'city' => 'Bandung',
            'vis' => ['city' => 'public', 'bogus' => 'public', 'wa' => 'alumni'],
        ])->assertRedirect(route('profile.edit'));

        $p = $a->profile->fresh();
        $this->assertSame('Bandung', $p->city);
        $this->assertSame('public', $p->visibility['city']);
        $this->assertSame('alumni', $p->visibility['wa']);
        $this->assertArrayNotHasKey('bogus', $p->visibility);
    }

    public function test_profile_update_rejects_invalid_visibility_level(): void
    {
        $this->actingAs($this->alumnus())->put(route('profile.update'), ['name' => 'X', 'vis' => ['city' => 'semua-orang']])
            ->assertSessionHasErrors('vis.city');
    }

    public function test_account_deletion_requires_confirmation_and_removes_data(): void
    {
        $a = $this->alumnus();
        $b = $this->alumnus();
        SlamMessage::create(['from_profile_id' => $a->profile->id, 'to_profile_id' => $b->profile->id, 'body' => 'pesan']);
        Post::create(['user_id' => $a->id, 'title' => 'Judul', 'body' => 'Isi']);

        $this->actingAs($a)->delete(route('profile.destroy'), ['confirm' => 'hapus'])->assertSessionHasErrors('confirm');
        $this->assertNotNull(User::find($a->id));

        $this->actingAs($a)->delete(route('profile.destroy'), ['confirm' => 'HAPUS'])->assertRedirect(route('home'));
        $this->assertNull(User::find($a->id));
        $this->assertDatabaseMissing('alumni_profiles', ['user_id' => $a->id]);
        $this->assertSame(0, SlamMessage::count());
        $this->assertSame(0, Post::count());
    }

    public function test_rsvp_toggles_attendance(): void
    {
        $a = $this->alumnus();
        $event = Event::create(['title' => 'Reuni', 'starts_at' => now()->addDays(10), 'place' => 'Aula']);

        $this->actingAs($a)->post(route('events.rsvp', $event));
        $this->assertSame(1, $event->attendees()->count());
        $this->actingAs($a)->post(route('events.rsvp', $event));
        $this->assertSame(0, $event->attendees()->count());
    }

    public function test_only_admin_can_pin_posts_and_add_events(): void
    {
        $a = $this->alumnus();
        $post = Post::create(['user_id' => $a->id, 'title' => 'T', 'body' => 'B']);

        $this->actingAs($a)->post(route('mading.pin', $post))->assertForbidden();
        $this->actingAs($this->admin())->post(route('mading.pin', $post))->assertRedirect();
        $this->assertTrue($post->fresh()->pinned);

        $this->actingAs($a)->post(route('events.store'), ['title' => 'x', 'starts_at' => now()->addDay()->toDateTimeString(), 'place' => 'y'])->assertForbidden();
    }

    public function test_guestbook_honeypot_blocks_bots_and_html_is_escaped(): void
    {
        $this->post(route('guestbook.store'), ['name' => 'Bot', 'body' => 'spam', 'website' => 'http://spam.test']);
        $this->assertDatabaseCount('guestbook_entries', 0);

        $this->post(route('guestbook.store'), ['name' => 'Tamu', 'body' => '<script>alert(1)</script>']);
        $this->get(route('guestbook.index'))->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    }
}
