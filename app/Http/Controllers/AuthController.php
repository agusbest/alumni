<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $cred = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $cred['email'])->first();
        if ($user && $user->status === 'pending') {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Akunmu masih menunggu persetujuan admin angkatan.']);
        }
        if ($user && $user->status === 'rejected') {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Pendaftaran ditolak. Hubungi admin angkatan.']);
        }

        if (! Auth::attempt(['email' => $cred['email'], 'password' => $cred['password'], 'status' => 'active'], $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('ok', 'Halo, '.Auth::user()->name.'! Selamat datang kembali.');
    }

    public function showRegister(): View
    {
        $claimable = AlumniProfile::where('is_claimed', false)->whereNull('user_id')
            ->orderBy('name')->get(['id', 'name', 'angkatan', 'kelas']);

        return view('auth.register', compact('claimable'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'angkatan' => ['required', 'integer', Rule::in(config('reuni.years'))],
            'kelas' => ['required', Rule::in(config('reuni.classes'))],
            'claim_profile_id' => ['nullable', 'integer', 'exists:alumni_profiles,id'],
        ]);

        $claimId = null;
        if (! empty($data['claim_profile_id'])) {
            $profile = AlumniProfile::find($data['claim_profile_id']);
            if (! $profile || $profile->is_claimed || $profile->user_id) {
                return back()->withInput()->withErrors(['claim_profile_id' => 'Profil ini sudah diklaim.']);
            }
            $claimId = $profile->id;
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'alumni',
            'status' => 'pending',
            'reg_angkatan' => $data['angkatan'],
            'reg_kelas' => $data['kelas'],
            'claim_profile_id' => $claimId,
        ]);

        return redirect()->route('login')->with('ok', 'Pendaftaran terkirim. Akunmu aktif setelah disetujui admin angkatan.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('ok', 'Kamu sudah keluar.');
    }
}
