<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_TTL    = 300; // 5 minutes
    private const CACHE_PREFIX = 'setting_';

    /**
     * Retrieve a setting value with a 5-minute cache.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX . $key,
            self::CACHE_TTL,
            fn () => Setting::getValue($key, $default)
        );
    }

    /**
     * Persist a setting value and invalidate its cache entry.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_PREFIX . $key);
    }
}
