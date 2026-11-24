<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pencatatan iuran reuni secara transparan. Aplikasi ini TIDAK memproses pembayaran;
 * admin mencatat pemasukan dan pengeluaran secara manual.
 */
class LedgerController extends Controller
{
    public function index(): View
    {
        $entries = LedgerEntry::orderByDesc('date')->orderByDesc('id')->get();
        $in = (int) $entries->where('type', 'in')->sum('amount');
        $out = (int) $entries->where('type', 'out')->sum('amount');
        $target = config('reuni.ledger_target');

        return view('ledger.index', compact('entries', 'in', 'out', 'target'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out'],
            'name' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'note' => ['nullable', 'string', 'max:80'],
        ]);
        LedgerEntry::create($data + ['date' => now()->toDateString()]);

        return back()->with('ok', 'Transaksi dicatat.');
    }

    public function destroy(LedgerEntry $entry): RedirectResponse
    {
        $entry->delete();

        return back();
    }
}
