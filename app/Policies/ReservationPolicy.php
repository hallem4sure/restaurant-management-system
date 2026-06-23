<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    // Assuming 'manage tables' covers reservations, or if there is a 'manage reservations' we can use that.
    // To be safe, we will just use 'manage tables' since reservations are tightly coupled.
    
    public function viewAny(User $user): bool
    {
        return $user->can('manage tables');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->can('manage tables');
    }

    public function create(User $user): bool
    {
        return $user->can('manage tables');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->can('manage tables');
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->can('manage tables');
    }
}
