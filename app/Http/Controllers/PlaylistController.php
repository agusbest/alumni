<?php

namespace App\Http\Controllers;

use App\Models\PlaylistTrack;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaylistController extends Controller
{
    public function index(Request $request): View
    {
        $ang = (string) $request->query('ang', '');
        $tracks = PlaylistTrack::with('user')
            ->when($ang !== '', fn ($q) => $q->where('angkatan', (int) $ang))
            ->latest()->get();
        $angkatans = PlaylistTrack::distinct()->orderBy('angkatan')->pluck('angkatan');

        return view('playlist.index', compact('tracks', 'ang', 'angkatans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'artist' => ['required', 'string', 'max:60'],
            'url' => ['nullable', 'url:http,https', 'max:200'],
        ]);
        $profile = $request->user()->profile;

        PlaylistTrack::create($data + [
            'user_id' => $request->user()->id,
            'angkatan' => $profile?->angkatan ?? (int) ($request->user()->reg_angkatan ?? 2005),
        ]);

        return back()->with('ok', 'Kaset baru masuk!');
    }

    public function destroy(Request $request, PlaylistTrack $track): RedirectResponse
    {
        abort_unless($track->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        $track->delete();

        return back();
    }
}
