<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::withCount('attendees')
            ->when($request->user(), fn ($q) => $q->with('attendees'))
            ->orderBy('starts_at')->get();
        $going = $request->user()
            ? $request->user()->id : null;

        return view('events.index', compact('events', 'going'));
    }

    public function rsvp(Request $request, Event $event): RedirectResponse
    {
        $result = $event->attendees()->toggle($request->user()->id);
        $joined = count($result['attached']) > 0;

        return back()->with('ok', $joined ? 'Sip! Kamu tercatat hadir.' : 'RSVP dibatalkan.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'starts_at' => ['required', 'date'],
            'place' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:300'],
        ]);
        Event::create($data);

        return back()->with('ok', 'Acara ditambahkan.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return back()->with('ok', 'Acara dihapus.');
    }
}
