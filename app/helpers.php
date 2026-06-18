<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $value = Setting::where('key', $key)->value('value');
            return $value ?? $default;
        });
    }
}

if (!function_exists('settingRefresh')) {
    function settingRefresh(string $key): void
    {
        Cache::forget('setting_' . $key);
    }
}
