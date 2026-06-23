<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    // Assume waiter/admin can manage orders
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'waiter', 'cashier']);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'waiter', 'cashier']);
    }

    public function create(User $user): bool
    {
        \Log::info('User roles: ' . json_encode($user->roles->pluck('name')));
        return $user->hasRole('admin') || $user->hasRole('waiter');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'waiter']);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }
}
