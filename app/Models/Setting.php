<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static array $encryptedKeys = [
        'zoom_client_secret',
        'teams_client_secret',
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $value = $setting->value;
        if (! in_array($key, self::$encryptedKeys, true) || $value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Backward compatibility for pre-encryption plain values.
            return $value;
        }
    }

    public static function set($key, $value)
    {
        if (in_array($key, self::$encryptedKeys, true) && $value !== null && $value !== '') {
            $value = Crypt::encryptString((string) $value);
        }

        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
