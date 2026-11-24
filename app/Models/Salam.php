<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salam extends Model
{
    protected $fillable = ['from_profile_id', 'to_profile_id'];
}
