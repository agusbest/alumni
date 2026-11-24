<?php

namespace App\Http\Controllers;

use App\Models\GuestbookEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuestbookController extends Controller
{
    public function index(Request $request): View
    {
        $ang = (string) $request->query('ang', '');
        $entries = GuestbookEntry::query()
            ->when($ang !== '', fn ($q) => $q->where('angkatan', (int) $ang))
            ->latest()->paginate(15)->withQueryString();

        return view('guestbook.index', compact('entries', 'ang'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Honeypot sederhana: bot biasanya mengisi kolom tersembunyi ini.
        if ($request->filled('website')) {
            return redirect()->route('guestbook.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:40'],
            'angkatan' => ['nullable', 'integer', Rule::in(config('reuni.years'))],
            'body' => ['required', 'string', 'max:200'],
        ]);
        GuestbookEntry::create($data);

        return redirect()->route('guestbook.index')->with('ok', 'Tanda tangan tersimpan!');
    }

    public function destroy(GuestbookEntry $entry): RedirectResponse
    {
        $entry->delete();

        return back();
    }
}
