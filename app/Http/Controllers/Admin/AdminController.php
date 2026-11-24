<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlbumPhoto;
use App\Models\AlumniProfile;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $tab = in_array($request->query('tab'), ['verifikasi', 'laporan', 'moderasi', 'data'], true)
            ? $request->query('tab') : 'verifikasi';

        $pending = User::where('status', 'pending')->where('role', 'alumni')->oldest()->get();
        $claims = AlumniProfile::whereIn('id', $pending->pluck('claim_profile_id')->filter())->get()->keyBy('id');
        $reports = Report::with('reporter')->latest()->get();
        $pendingPhotos = AlbumPhoto::with('user')->where('status', 'pending')->latest()->get();
        $moderated = Setting::get('moderate_album') === '1';
        $pages = AlumniProfile::query()->select('angkatan', 'kelas')->distinct()->orderBy('angkatan')->orderBy('kelas')->get();

        $classList = null;
        if ($request->filled('angkatan') && $request->filled('kelas')) {
            $names = AlumniProfile::where('angkatan', (int) $request->query('angkatan'))
                ->where('kelas', $request->query('kelas'))->orderBy('name')->pluck('name');
            $classList = 'Angkatan '.(int) $request->query('angkatan').' - '.$request->query('kelas')."\n"
                .$names->map(fn ($n, $i) => ($i + 1).'. '.$n)->implode("\n");
        }

        return view('admin.index', compact('tab', 'pending', 'claims', 'reports', 'pendingPhotos', 'moderated', 'pages', 'classList'));
    }

    public function approve(User $user): RedirectResponse
    {
        abort_unless($user->status === 'pending', 404);

        DB::transaction(function () use ($user) {
            $claim = $user->claim_profile_id ? AlumniProfile::lockForUpdate()->find($user->claim_profile_id) : null;

            if ($claim && ! $claim->is_claimed && ! $claim->user_id) {
                $claim->forceFill(['user_id' => $user->id, 'is_claimed' => true])->save();
            } else {
                $profile = new AlumniProfile([
                    'name' => $user->name,
                    'angkatan' => $user->reg_angkatan,
                    'kelas' => $user->reg_kelas,
                    'visibility' => AlumniProfile::defaultVisibility(),
                ]);
                $profile->forceFill(['user_id' => $user->id, 'is_claimed' => true])->save();
            }
            $user->update(['status' => 'active']);
        });

        return back()->with('ok', $user->name.' disetujui.');
    }

    public function reject(User $user): RedirectResponse
    {
        abort_unless($user->status === 'pending', 404);
        $user->update(['status' => 'rejected']);

        return back()->with('ok', 'Pendaftaran ditolak.');
    }

    public function reportDone(Report $report): RedirectResponse
    {
        $report->update(['is_done' => true]);

        return back();
    }

    public function toggleModeration(): RedirectResponse
    {
        $now = Setting::get('moderate_album') === '1';
        Setting::put('moderate_album', $now ? '0' : '1');

        return back()->with('ok', 'Moderasi foto '.($now ? 'nonaktif' : 'aktif').'.');
    }

    public function addOld(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'angkatan' => ['required', 'integer', Rule::in(config('reuni.years'))],
            'kelas' => ['required', Rule::in(config('reuni.classes'))],
        ]);

        // Profil lama: belum diklaim, semua kolom privasi disembunyikan.
        $profile = new AlumniProfile($data + ['visibility' => array_fill_keys(AlumniProfile::VIS_FIELDS, 'hidden')]);
        $profile->save();

        return back()->with('ok', 'Alumni lama ditambahkan. Ia bisa mengklaim profil saat mendaftar.');
    }
}
