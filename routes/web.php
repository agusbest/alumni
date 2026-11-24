<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SlamController;
use App\Http\Controllers\YearbookController;
use Illuminate\Support\Facades\Route;

/* ---------- Publik (info umum) ---------- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/tentang', 'about')->name('about');
Route::get('/foto/{profile}/{kind}', [PhotoController::class, 'profile'])->where('kind', 'then|now')->name('photo');

Route::get('/galeri', [GalleryController::class, 'index'])->name('galeri.index');
Route::get('/galeri/{profile}', [GalleryController::class, 'show'])->whereNumber('profile')->name('galeri.show');
Route::get('/mading', [PostController::class, 'index'])->name('mading.index');
Route::get('/reuni', [EventController::class, 'index'])->name('events.index');
Route::get('/guestbook', [GuestbookController::class, 'index'])->name('guestbook.index');
Route::post('/guestbook', [GuestbookController::class, 'store'])->middleware('throttle:4,1')->name('guestbook.store');
Route::view('/kuis', 'fun.kuis')->name('kuis');
Route::view('/snake', 'fun.snake')->name('snake');

/* ---------- Tamu ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
});

/* ---------- Alumni terverifikasi (login) ---------- */
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profil-saya', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil-saya', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil-saya/foto/{kind}', [ProfileController::class, 'photo'])->where('kind', 'then|now')->name('profile.photo');
    Route::delete('/profil-saya', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/slam-book', [SlamController::class, 'index'])->name('slam.index');
    Route::post('/slam-book', [SlamController::class, 'store'])->middleware('throttle:10,1')->name('slam.store');
    Route::post('/salam/{profile}', [SlamController::class, 'salam'])->whereNumber('profile')->name('salam.store');
    Route::post('/lapor/profil/{profile}', [ReportController::class, 'profile'])->whereNumber('profile')->name('report.profile');

    Route::get('/buku-tahunan', [YearbookController::class, 'show'])->name('yearbook.show');
    Route::post('/buku-tahunan', [YearbookController::class, 'store'])->middleware('throttle:10,1')->name('yearbook.store');

    Route::post('/mading', [PostController::class, 'store'])->middleware('throttle:10,1')->name('mading.store');
    Route::post('/mading/{post}/komentar', [PostController::class, 'comment'])->middleware('throttle:20,1')->name('mading.comment');
    Route::post('/mading/{post}/reaksi', [PostController::class, 'react'])->name('mading.react');
    Route::post('/mading/{post}/lapor', [PostController::class, 'report'])->name('mading.report');
    Route::delete('/mading/{post}', [PostController::class, 'destroy'])->name('mading.destroy');
    Route::delete('/mading/komentar/{comment}', [PostController::class, 'destroyComment'])->name('mading.comment.destroy');

    Route::get('/album', [AlbumController::class, 'index'])->name('album.index');
    Route::post('/album', [AlbumController::class, 'store'])->middleware('throttle:10,1')->name('album.store');
    Route::get('/album/{photo}/gambar', [AlbumController::class, 'image'])->name('album.image');
    Route::delete('/album/{photo}', [AlbumController::class, 'destroy'])->name('album.destroy');

    Route::post('/reuni/{event}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');

    Route::get('/playlist', [PlaylistController::class, 'index'])->name('playlist.index');
    Route::post('/playlist', [PlaylistController::class, 'store'])->middleware('throttle:10,1')->name('playlist.store');
    Route::delete('/playlist/{track}', [PlaylistController::class, 'destroy'])->name('playlist.destroy');

    Route::get('/iuran', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/peta', [MapController::class, 'index'])->name('map.index');
});

/* ---------- Admin ---------- */
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/pendaftaran/{user}/setujui', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/pendaftaran/{user}/tolak', [AdminController::class, 'reject'])->name('admin.reject');
    Route::post('/admin/laporan/{report}/selesai', [AdminController::class, 'reportDone'])->name('admin.report.done');
    Route::post('/admin/moderasi', [AdminController::class, 'toggleModeration'])->name('admin.moderation');
    Route::post('/admin/alumni-lama', [AdminController::class, 'addOld'])->name('admin.old');

    Route::post('/mading/{post}/pin', [PostController::class, 'pin'])->name('mading.pin');
    Route::post('/album/{photo}/setujui', [AlbumController::class, 'approve'])->name('album.approve');
    Route::post('/reuni', [EventController::class, 'store'])->name('events.store');
    Route::delete('/reuni/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::delete('/guestbook/{entry}', [GuestbookController::class, 'destroy'])->name('guestbook.destroy');
    Route::post('/iuran', [LedgerController::class, 'store'])->name('ledger.store');
    Route::delete('/iuran/{entry}', [LedgerController::class, 'destroy'])->name('ledger.destroy');
});
