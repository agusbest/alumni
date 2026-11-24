<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Support\Avatar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Foto profil disimpan di disk privat dan hanya dilayani lewat controller ini,
 * sehingga aturan privasi (canSee) ditegakkan di server, bukan hanya di tampilan.
 */
class PhotoController extends Controller
{
    public function profile(Request $request, AlumniProfile $profile, string $kind): Response|StreamedResponse
    {
        $viewer = $request->user();

        if (! $profile->is_claimed) {
            return $this->svg(Avatar::svg($profile->id, 'anon'));
        }
        if (! $profile->canSee('photo', $viewer)) {
            return $this->svg(Avatar::svg($profile->id, 'lock'));
        }

        $path = $kind === 'then' ? $profile->photo_then_path : $profile->photo_now_path;
        if ($path && Storage::disk(config('reuni.disk'))->exists($path)) {
            return Storage::disk(config('reuni.disk'))->response($path, null, ['Cache-Control' => 'private, max-age=300']);
        }

        return $this->svg(Avatar::svg($profile->id, $kind, (int) $profile->angkatan));
    }

    private function svg(string $svg): Response
    {
        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
