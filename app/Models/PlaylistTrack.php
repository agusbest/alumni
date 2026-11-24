<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistTrack extends Model
{
    protected $fillable = ['user_id', 'title', 'artist', 'url', 'angkatan'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
