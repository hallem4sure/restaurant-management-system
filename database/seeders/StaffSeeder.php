<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // Waiter
        $waiter = User::updateOrCreate(
            ['email' => 'waiter@restaurant.com'],
            [
                'name'      => 'John Waiter',
                'password'  => Hash::make('12345678'),
                'phone'     => '1112223333',
                'is_active' => true,
            ]
        );
        if (!$waiter->hasRole('waiter')) {
            $waiter->assignRole('waiter');
        }

        // Cashier
        $cashier = User::updateOrCreate(
            ['email' => 'cashier@restaurant.com'],
            [
                'name'      => 'Sarah Cashier',
                'password'  => Hash::make('12345678'),
                'phone'     => '4445556666',
                'is_active' => true,
            ]
        );
        if (!$cashier->hasRole('cashier')) {
            $cashier->assignRole('cashier');
        }

        // Kitchen Staff
        $kitchen = User::updateOrCreate(
            ['email' => 'kitchen@restaurant.com'],
            [
                'name'      => 'Mike Chef',
                'password'  => Hash::make('12345678'),
                'phone'     => '7778889999',
                'is_active' => true,
            ]
        );
        if (!$kitchen->hasRole('kitchen_staff')) {
            $kitchen->assignRole('kitchen_staff');
        }
    }
}
