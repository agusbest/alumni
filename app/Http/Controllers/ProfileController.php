<?php

namespace App\Http\Controllers;

use App\Models\AlbumPhoto;
use App\Models\AlumniProfile;
use App\Support\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class ProfileController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $profile = $request->user()->profile;
        if (! $profile) {
            return redirect()->route('home')->with('info', 'Akun admin tidak punya biodata alumni.');
        }

        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->profile;
        abort_unless($profile, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'nickname' => ['nullable', 'string', 'max:40'],
            'absen' => ['nullable', 'string', 'max:3'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'city' => ['nullable', 'string', 'max:80'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'status_text' => ['nullable', 'string', 'max:120'],
            'social' => ['nullable', 'string', 'max:120'],
            'ekskul' => ['nullable', 'string', 'max:120'],
            'geng' => ['nullable', 'string', 'max:120'],
            'favorite_teacher' => ['nullable', 'string', 'max:120'],
            'favorite_subject' => ['nullable', 'string', 'max:120'],
            'memory' => ['nullable', 'string', 'max:400'],
            'dream_then' => ['nullable', 'string', 'max:120'],
            'dream_now' => ['nullable', 'string', 'max:120'],
            'favorite_song' => ['nullable', 'string', 'max:120'],
            'wa' => ['nullable', 'regex:/^[0-9+\-\s]{6,20}$/'],
            'email_public' => ['nullable', 'email', 'max:120'],
            'vis' => ['nullable', 'array'],
            'vis.*' => ['in:public,alumni,angkatan,hidden'],
        ]);

        // Hanya kolom privasi yang dikenal yang boleh diubah.
        $vis = array_merge(
            AlumniProfile::defaultVisibility(),
            $profile->visibility ?? [],
            Arr::only($data['vis'] ?? [], AlumniProfile::VIS_FIELDS)
        );

        $profile->fill(Arr::except($data, ['vis']));
        $profile->visibility = $vis;
        $profile->save();
        $request->user()->update(['name' => $data['name']]);

        return redirect()->route('profile.edit')->with('ok', 'Profil tersimpan!');
    }

    public function photo(Request $request, string $kind): RedirectResponse
    {
        $profile = $request->user()->profile;
        abort_unless($profile && in_array($kind, ['then', 'now'], true), 404);

        $request->validate([
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:'.config('reuni.photo.max_kb')],
        ]);

        try {
            $path = ImageProcessor::store($request->file('photo'), 'profiles', config('reuni.photo.w'), config('reuni.photo.h'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['photo' => $e->getMessage()]);
        }

        $column = $kind === 'then' ? 'photo_then_path' : 'photo_now_path';
        ImageProcessor::delete($profile->{$column});
        $profile->forceFill([$column => $path])->save();

        return redirect()->route('profile.edit')->with('ok', 'Foto tersimpan.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['required', 'in:HAPUS']], [
            'confirm.in' => 'Ketik HAPUS (huruf besar) untuk konfirmasi.',
        ]);

        $user = $request->user();
        $profile = $user->profile;

        // Hapus berkas foto (profil dan album) sebelum baris database dihapus.
        if ($profile) {
            ImageProcessor::delete($profile->photo_then_path);
            ImageProcessor::delete($profile->photo_now_path);
        }
        AlbumPhoto::where('user_id', $user->id)->pluck('path')->each(fn ($p) => ImageProcessor::delete($p));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Cascade: profil, slam, pesan buku tahunan, mading, album, RSVP ikut terhapus.
        $user->delete();

        return redirect()->route('home')->with('ok', 'Akun dan semua datamu sudah dihapus.');
    }
}
