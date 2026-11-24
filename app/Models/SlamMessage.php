<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlamMessage extends Model
{
    protected $fillable = ['from_profile_id', 'to_profile_id', 'body'];

    public function from(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'from_profile_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'to_profile_id');
    }
}
