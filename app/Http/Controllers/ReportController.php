<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function profile(Request $request, AlumniProfile $profile): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'in:Foto tidak pantas,Data pribadi disalahgunakan,Akun palsu,Lainnya'],
        ]);

        Report::create([
            'reporter_id' => $request->user()->id,
            'type' => 'profile',
            'target_id' => $profile->id,
            'label' => 'Profil '.$profile->name,
            'reason' => $data['reason'],
        ]);

        return back()->with('ok', 'Laporan terkirim ke admin. Terima kasih.');
    }
}
