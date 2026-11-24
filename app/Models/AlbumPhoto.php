<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AlbumPhoto extends Model
{
    protected $fillable = ['user_id', 'path', 'hue', 'caption', 'year', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(AlumniProfile::class, 'album_photo_profile', 'album_photo_id', 'alumni_profile_id');
    }
}
