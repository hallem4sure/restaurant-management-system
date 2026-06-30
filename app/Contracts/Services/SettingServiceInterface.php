<?php

namespace App\Contracts\Services;

interface SettingServiceInterface
{
    /**
     * Get all settings grouped by their 'group' field.
     */
    public function allGrouped(): array;

    /**
     * Get a specific setting value by key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Update multiple settings.
     * 
     * @param array $data key-value pairs of settings
     * @return void
     */
    public function updateMultiple(array $data): void;
}
