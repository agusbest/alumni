<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = ['title', 'starts_at', 'place', 'description'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime'];
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user');
    }
}
