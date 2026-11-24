<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\Event;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        if (! $request->session()->has('counted')) {
            Setting::bump('visits');
            $request->session()->put('counted', true);
        }
        $visits = (int) Setting::get('visits', 0);
        $viewer = $request->user();

        $birthdays = AlumniProfile::query()
            ->where('is_claimed', true)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->whereCanSee('birth', $viewer)
            ->get()
            ->sortBy(fn ($p) => $p->birth_date->day);

        $pins = Post::where('pinned', true)->pluck('title');
        $next = Event::where('starts_at', '>', now())->orderBy('starts_at')->first();
        $marquee = collect(['Selamat datang di '.config('reuni.name')])
            ->merge($pins)
            ->when($next, fn ($c) => $c->push('Acara berikutnya: '.$next->title.' ('.$next->starts_at->translatedFormat('j F Y').')'))
            ->push('Jangan lupa lengkapi slam book kamu!')
            ->implode('  ★  ');

        return view('home', compact('visits', 'birthdays', 'marquee'));
    }
}
