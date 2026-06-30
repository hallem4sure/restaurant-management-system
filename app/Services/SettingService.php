<?php

namespace App\Services;

use App\Contracts\Services\SettingServiceInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService implements SettingServiceInterface
{
    private const CACHE_KEY = 'app_settings';

    public function allGrouped(): array
    {
        $settings = $this->getAllSettings();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->group][] = $setting;
        }

        return $grouped;
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->getAllSettings();
        
        $setting = $settings->firstWhere('key', $key);

        return $setting ? $setting->value : $default;
    }

    public function updateMultiple(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        $this->clearCache();
    }

    private function getAllSettings()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::all();
        });
    }

    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
