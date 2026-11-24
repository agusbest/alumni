<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\Salam;
use App\Models\SlamMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlamController extends Controller
{
    public function index(Request $request): View
    {
        $me = $request->user()->profile;
        $mine = $me ? $me->slamMessages()->with('from')->latest()->get() : collect();
        $friends = AlumniProfile::where('is_claimed', true)
            ->when($me, fn ($q) => $q->where('id', '!=', $me->id))
            ->orderBy('name')->get(['id', 'name', 'angkatan']);

        return view('slam.index', compact('mine', 'friends', 'me'));
    }

    public function store(Request $request): RedirectResponse
    {
        $me = $request->user()->profile;
        abort_unless($me, 403, 'Hanya alumni yang bisa menulis di slam book.');

        $data = $request->validate([
            'to_profile_id' => ['required', 'integer', 'exists:alumni_profiles,id'],
            'body' => ['required', 'string', 'max:400'],
        ]);

        $target = AlumniProfile::findOrFail($data['to_profile_id']);
        if (! $target->is_claimed || $target->id === $me->id) {
            return back()->withErrors(['to_profile_id' => 'Tujuan tidak valid.']);
        }

        SlamMessage::create([
            'from_profile_id' => $me->id,
            'to_profile_id' => $target->id,
            'body' => $data['body'],
        ]);

        return back()->with('ok', 'Tulisan masuk ke slam book '.$target->name.'!');
    }

    public function salam(Request $request, AlumniProfile $profile): RedirectResponse
    {
        $me = $request->user()->profile;
        abort_unless($me, 403);

        if ($me->id === $profile->id) {
            return back()->with('info', 'Salam ke diri sendiri? Cukup senyum aja :)');
        }
        $already = Salam::where('from_profile_id', $me->id)->where('to_profile_id', $profile->id)
            ->where('created_at', '>', now()->subDay())->exists();
        if ($already) {
            return back()->with('info', 'Salam hari ini sudah terkirim.');
        }

        Salam::create(['from_profile_id' => $me->id, 'to_profile_id' => $profile->id]);

        return back()->with('ok', 'Salam terkirim ke '.$profile->name.'!');
    }
}
