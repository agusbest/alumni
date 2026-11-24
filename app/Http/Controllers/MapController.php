<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(Request $request): View
    {
        // Hanya kota dari profil yang mengizinkan kolom "kota" dilihat oleh si pengunjung.
        $cities = AlumniProfile::query()
            ->whereCanSee('city', $request->user())
            ->whereNotNull('city')->where('city', '!=', '')
            ->select('city', DB::raw('count(*) as total'))
            ->groupBy('city')->orderByDesc('total')->get();

        return view('map.index', ['cities' => $cities, 'coords' => config('reuni.cities')]);
    }
}
