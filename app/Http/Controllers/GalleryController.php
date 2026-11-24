<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $viewer = $request->user();
        $f = [
            'q' => trim((string) $request->query('q', '')),
            'ang' => (string) $request->query('ang', ''),
            'kelas' => (string) $request->query('kelas', ''),
            'kota' => trim((string) $request->query('kota', '')),
            'prof' => trim((string) $request->query('prof', '')),
            'sort' => in_array($request->query('sort'), ['az', 'ang', 'new'], true) ? $request->query('sort') : 'az',
            'view' => in_array($request->query('view'), ['grid', 'list', 'kelas'], true) ? $request->query('view') : 'grid',
            'foto' => $request->query('foto') === 'now' ? 'now' : 'then',
        ];

        // PostgreSQL: LIKE peka huruf besar/kecil, pakai ILIKE agar pencarian tetap tidak peduli kapital.
        $op = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

        $query = AlumniProfile::query()->galleryVisible($viewer);

        if ($f['q'] !== '') {
            $like = '%'.addcslashes($f['q'], '%_\\').'%';
            $query->where(function ($w) use ($like, $viewer, $op) {
                $w->where('alumni_profiles.name', $op, $like)
                    ->orWhere(function ($n) use ($like, $viewer, $op) {
                        $n->whereCanSee('memories', $viewer)->where('alumni_profiles.nickname', $op, $like);
                    });
            });
        }
        if ($f['ang'] !== '') {
            $query->where('alumni_profiles.angkatan', (int) $f['ang']);
        }
        if ($f['kelas'] !== '') {
            $query->where('alumni_profiles.kelas', $f['kelas']);
        }
        // Filter kota/profesi hanya mengenai profil yang mengizinkan kolom itu dilihat pengunjung.
        if ($f['kota'] !== '') {
            $query->whereCanSee('city', $viewer)->where('alumni_profiles.city', $f['kota']);
        }
        if ($f['prof'] !== '') {
            $like = '%'.addcslashes($f['prof'], '%_\\').'%';
            $query->whereCanSee('job', $viewer)->where(function ($w) use ($like, $op) {
                $w->where('alumni_profiles.occupation', $op, $like)->orWhere('alumni_profiles.company', $op, $like);
            });
        }

        match ($f['sort']) {
            'ang' => $query->orderBy('angkatan')->orderBy('kelas')->orderBy('name'),
            'new' => $query->orderByDesc('alumni_profiles.id'),
            default => $query->orderBy('name'),
        };

        $per = match ($f['view']) { 'list' => 20, 'kelas' => 48, default => 12 };
        $profiles = $query->paginate($per)->withQueryString();

        $base = AlumniProfile::query()->galleryVisible($viewer);
        $angkatans = (clone $base)->distinct()->orderBy('angkatan')->pluck('angkatan');
        $kelass = (clone $base)->distinct()->orderBy('kelas')->pluck('kelas');
        $kotas = (clone $base)->whereCanSee('city', $viewer)->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->orderBy('city')->pluck('city');

        return view('gallery.index', compact('profiles', 'f', 'angkatans', 'kelass', 'kotas', 'viewer'));
    }

    public function show(Request $request, AlumniProfile $profile): View|RedirectResponse
    {
        $viewer = $request->user();

        if (! $viewer && ! ($profile->is_claimed && $profile->level('photo') === 'public')) {
            return redirect()->route('login')->with('info', 'Masuk dulu untuk melihat biodata ini.');
        }

        $tab = in_array($request->query('tab'), ['profil', 'sekarang', 'kenangan', 'kontak', 'slam'], true)
            ? $request->query('tab') : 'profil';

        $messages = collect();
        if ($tab === 'slam' && $viewer && $profile->is_claimed) {
            $messages = $profile->slamMessages()->with('from')->latest()->get();
        }
        $salamCount = $profile->salams()->count();

        return view('gallery.show', compact('profile', 'tab', 'messages', 'salamCount', 'viewer'));
    }
}
