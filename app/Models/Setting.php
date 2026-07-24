<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Retrieve the value for a given settings key.
     *
     * @param  string  $key
     * @param  mixed   $default  Returned when no record exists or the value is null.
     * @return mixed
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $value = static::where('key', $key)->value('value');

        return $value ?? $default;
    }
}
