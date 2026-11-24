<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'key';
    protected $keyType = 'string';
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::query()->whereKey($key)->value('value') ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrInsert(['key' => $key], ['value' => (string) $value]);
    }

    public static function bump(string $key): int
    {
        $next = (int) static::get($key, 0) + 1;
        static::put($key, $next);

        return $next;
    }
}
