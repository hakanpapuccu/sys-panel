<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static ?array $cachedSettings = null;

    protected static array $resolvedValues = [];

    protected static array $encryptedKeys = [
        'zoom_client_secret',
        'teams_client_secret',
    ];

    protected static function allCached(): array
    {
        if (self::$cachedSettings !== null) {
            return self::$cachedSettings;
        }

        try {
            self::$cachedSettings = Cache::rememberForever('settings.all', static function (): array {
                return self::query()->pluck('value', 'key')->all();
            });
        } catch (Throwable $e) {
            // During initial install/migrate the settings table may not exist yet.
            self::$cachedSettings = [];
        }

        return self::$cachedSettings;
    }

    public static function forgetCachedSettings(): void
    {
        self::$cachedSettings = null;
        self::$resolvedValues = [];
        Cache::forget('settings.all');
    }

    public static function get($key, $default = null)
    {
        if (array_key_exists($key, self::$resolvedValues)) {
            return self::$resolvedValues[$key];
        }

        $settings = self::allCached();
        if (! array_key_exists($key, $settings)) {
            return $default;
        }

        $value = $settings[$key];
        if (! in_array($key, self::$encryptedKeys, true) || $value === null || $value === '') {
            self::$resolvedValues[$key] = $value;

            return $value;
        }

        try {
            $decrypted = Crypt::decryptString($value);
            self::$resolvedValues[$key] = $decrypted;

            return $decrypted;
        } catch (DecryptException $e) {
            // Backward compatibility for pre-encryption plain values.
            self::$resolvedValues[$key] = $value;

            return $value;
        }
    }

    public static function set($key, $value)
    {
        if (in_array($key, self::$encryptedKeys, true) && $value !== null && $value !== '') {
            $value = Crypt::encryptString((string) $value);
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::forgetCachedSettings();

        return $setting;
    }
}
