<?php

use App\Contracts\Services\SettingServiceInterface;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return app(SettingServiceInterface::class)->get($key, $default);
    }
}
