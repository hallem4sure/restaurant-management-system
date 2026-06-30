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
        $waiter = User::firstOrCreate(
            ['email' => 'waiter@restaurant.com'],
            [
                'name' => 'John Waiter',
                'password' => Hash::make('password123'),
                'phone' => '1112223333',
                'is_active' => true,
            ]
        );
        $waiter->assignRole('waiter');

        // Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@restaurant.com'],
            [
                'name' => 'Sarah Cashier',
                'password' => Hash::make('password123'),
                'phone' => '4445556666',
                'is_active' => true,
            ]
        );
        $cashier->assignRole('cashier');

        // Kitchen Staff
        $kitchen = User::firstOrCreate(
            ['email' => 'kitchen@restaurant.com'],
            [
                'name' => 'Mike Chef',
                'password' => Hash::make('password123'),
                'phone' => '7778889999',
                'is_active' => true,
            ]
        );
        $kitchen->assignRole('kitchen_staff');
    }
}
