<?php

namespace App\Http\Controllers;

use App\Models\AlbumPhoto;
use App\Models\AlumniProfile;
use App\Models\Setting;
use App\Support\Avatar;
use App\Support\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class AlbumController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $photos = AlbumPhoto::with(['user', 'tags'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where(fn ($w) => $w->where('status', 'ok')->orWhere('user_id', $user->id)))
            ->latest()->paginate(12);
        $people = AlumniProfile::where('is_claimed', true)->orderBy('name')->get(['id', 'name']);
        $moderated = Setting::get('moderate_album') === '1';

        return view('album.index', compact('photos', 'people', 'moderated'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:'.config('reuni.photo.max_kb')],
            'caption' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'between:1995,2015'],
            'tags' => ['nullable', 'array', 'max:30'],
            'tags.*' => ['integer', 'exists:alumni_profiles,id'],
        ]);

        try {
            $path = ImageProcessor::store($request->file('photo'), 'album', config('reuni.photo.album_w'), config('reuni.photo.album_h'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['photo' => $e->getMessage()])->withInput();
        }

        $user = $request->user();
        $pending = Setting::get('moderate_album') === '1' && ! $user->isAdmin();

        $photo = AlbumPhoto::create([
            'user_id' => $user->id,
            'path' => $path,
            'caption' => $data['caption'],
            'year' => $data['year'],
            'status' => $pending ? 'pending' : 'ok',
        ]);
        $photo->tags()->sync($data['tags'] ?? []);

        return redirect()->route('album.index')->with('ok', $pending ? 'Terunggah, menunggu persetujuan admin.' : 'Foto masuk album!');
    }

    public function image(Request $request, AlbumPhoto $photo): Response
    {
        $user = $request->user();
        abort_unless($photo->status === 'ok' || $photo->user_id === $user->id || $user->isAdmin(), 404);

        if ($photo->path && Storage::disk(config('reuni.disk'))->exists($photo->path)) {
            return Storage::disk(config('reuni.disk'))->response($photo->path, null, ['Cache-Control' => 'private, max-age=300']);
        }

        return response(Avatar::scene($photo->caption, (int) ($photo->hue ?? 200)), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function destroy(Request $request, AlbumPhoto $photo): RedirectResponse
    {
        abort_unless($photo->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        ImageProcessor::delete($photo->path);
        $photo->delete();

        return back()->with('ok', 'Foto dihapus.');
    }

    public function approve(AlbumPhoto $photo): RedirectResponse
    {
        $photo->update(['status' => 'ok']);

        return back()->with('ok', 'Foto disetujui.');
    }
}
