<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage settings');
    }

    public function create(User $user): bool
    {
        return $user->can('manage settings');
    }
}
