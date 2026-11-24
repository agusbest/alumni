<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\YearbookMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class YearbookController extends Controller
{
    public function show(Request $request): View
    {
        $pages = AlumniProfile::query()->select('angkatan', 'kelas')->distinct()
            ->orderBy('angkatan')->orderBy('kelas')->get()->values();

        $i = max(0, min((int) $request->query('p', 0), max(0, $pages->count() - 1)));
        $cur = $pages->get($i);

        $members = collect();
        $messages = collect();
        if ($cur) {
            $members = AlumniProfile::where('angkatan', $cur->angkatan)->where('kelas', $cur->kelas)->orderBy('name')->get();
            $messages = YearbookMessage::with('profile')->where('angkatan', $cur->angkatan)
                ->where('kelas', $cur->kelas)->oldest()->get();
        }

        return view('yearbook.show', compact('pages', 'i', 'cur', 'members', 'messages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $me = $request->user()->profile;
        abort_unless($me, 403, 'Hanya alumni yang bisa menulis pesan.');

        $data = $request->validate([
            'angkatan' => ['required', 'integer', Rule::exists('alumni_profiles', 'angkatan')],
            'kelas' => ['required', 'string', 'max:20'],
            'body' => ['required', 'string', 'max:300'],
            'p' => ['nullable', 'integer'],
        ]);

        YearbookMessage::create([
            'profile_id' => $me->id,
            'angkatan' => $data['angkatan'],
            'kelas' => $data['kelas'],
            'body' => $data['body'],
        ]);

        return redirect()->route('yearbook.show', ['p' => $data['p'] ?? 0])->with('ok', 'Pesanmu masuk buku tahunan!');
    }
}
