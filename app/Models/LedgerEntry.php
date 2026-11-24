<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = ['type', 'name', 'amount', 'note', 'date'];

    protected function casts(): array
    {
        return ['date' => 'date', 'amount' => 'integer'];
    }
}
