<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlumniProfile extends Model
{
    /** Kolom privasi yang bisa diatur pemilik profil. */
    public const VIS_FIELDS = ['photo', 'birth', 'city', 'job', 'social', 'memories', 'wa', 'email'];

    /** Kolom yang dihitung untuk indikator kelengkapan profil. */
    public const COMPLETE_FIELDS = [
        'nickname', 'birth_date', 'city', 'occupation', 'company', 'social', 'ekskul', 'geng',
        'favorite_teacher', 'favorite_subject', 'memory', 'dream_then', 'dream_now', 'favorite_song',
        'photo_then_path', 'photo_now_path', 'wa', 'email_public',
    ];

    protected $guarded = ['id', 'user_id', 'is_claimed', 'photo_then_path', 'photo_now_path'];

    protected function casts(): array
    {
        return [
            'visibility' => 'array',
            'birth_date' => 'date',
            'is_claimed' => 'boolean',
        ];
    }

    public static function defaultVisibility(): array
    {
        return [
            'photo' => 'alumni', 'birth' => 'hidden', 'city' => 'alumni', 'job' => 'alumni',
            'social' => 'hidden', 'memories' => 'alumni', 'wa' => 'hidden', 'email' => 'hidden',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function slamMessages(): HasMany
    {
        return $this->hasMany(SlamMessage::class, 'to_profile_id');
    }

    public function salams(): HasMany
    {
        return $this->hasMany(Salam::class, 'to_profile_id');
    }

    public function albumPhotos(): BelongsToMany
    {
        return $this->belongsToMany(AlbumPhoto::class, 'album_photo_profile', 'alumni_profile_id', 'album_photo_id');
    }

    public function level(string $field): string
    {
        return ($this->visibility ?? [])[$field] ?? 'hidden';
    }

    /**
     * Aturan privasi tunggal: apakah $viewer boleh melihat kolom $field milik profil ini?
     * Dipakai oleh semua view dan endpoint, sehingga data yang disembunyikan tidak bocor.
     */
    public function canSee(string $field, ?User $viewer): bool
    {
        if ($viewer && $this->user_id && $viewer->id === $this->user_id) {
            return true; // pemilik selalu bisa melihat datanya sendiri
        }
        if (! $this->is_claimed) {
            return false; // profil belum diklaim: hanya data minimal
        }

        return match ($this->level($field)) {
            'public' => true,
            'alumni' => $viewer !== null,
            'angkatan' => $viewer !== null
                && $viewer->profile !== null
                && (int) $viewer->profile->angkatan === (int) $this->angkatan,
            default => false,
        };
    }

    /** Versi SQL dari canSee() untuk filter/pencarian, agar filter tidak membocorkan data. */
    public function scopeWhereCanSee(Builder $query, string $field, ?User $viewer): Builder
    {
        abort_unless(in_array($field, self::VIS_FIELDS, true), 500);
        $path = 'visibility->'.$field;

        return $query->where(function (Builder $w) use ($path, $viewer) {
            $w->where(function (Builder $c) use ($path, $viewer) {
                $c->where('alumni_profiles.is_claimed', true)->where(function (Builder $v) use ($path, $viewer) {
                    $v->where($path, 'public');
                    if ($viewer) {
                        $v->orWhere($path, 'alumni');
                        if ($viewer->profile) {
                            $ang = (int) $viewer->profile->angkatan;
                            $v->orWhere(fn (Builder $a) => $a->where($path, 'angkatan')->where('alumni_profiles.angkatan', $ang));
                        }
                    }
                });
            });
            if ($viewer) {
                $w->orWhere('alumni_profiles.user_id', $viewer->id);
            }
        });
    }

    /** Siapa saja yang tampil di galeri: tamu hanya alumni yang memilih foto Publik. */
    public function scopeGalleryVisible(Builder $query, ?User $viewer): Builder
    {
        if ($viewer) {
            return $query;
        }

        return $query->where('alumni_profiles.is_claimed', true)->where('visibility->photo', 'public');
    }

    public function completeness(): int
    {
        $filled = collect(self::COMPLETE_FIELDS)->filter(fn ($f) => filled($this->{$f}))->count();

        return (int) round($filled / count(self::COMPLETE_FIELDS) * 100);
    }

    public function photoUrl(string $kind): string
    {
        return route('photo', [$this->id, $kind]);
    }

    /** Tanggal lahir untuk tampilan: orang lain hanya melihat tanggal dan bulan. */
    public function birthText(?User $viewer): ?string
    {
        if (! $this->birth_date) {
            return null;
        }
        $own = $viewer && $this->user_id === $viewer->id;

        return $this->birth_date->translatedFormat($own ? 'j F Y' : 'j F');
    }
}
