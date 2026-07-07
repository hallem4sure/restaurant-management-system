<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage offers');
    }

    public function view(User $user, Offer $offer): bool
    {
        return $user->can('manage offers');
    }

    public function create(User $user): bool
    {
        return $user->can('manage offers');
    }

    public function update(User $user, Offer $offer): bool
    {
        return $user->can('manage offers');
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $user->can('manage offers');
    }
}
