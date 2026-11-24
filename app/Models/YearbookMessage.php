<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YearbookMessage extends Model
{
    protected $fillable = ['profile_id', 'angkatan', 'kelas', 'body'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'profile_id');
    }
}
