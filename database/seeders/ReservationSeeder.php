<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $tables = RestaurantTable::take(5)->get();
        
        if ($tables->isEmpty()) {
            return;
        }

        $reservations = [
            [
                'table_id'         => $tables[0]->id,
                'created_by'       => $admin->id,
                'customer_name'    => 'John Doe',
                'customer_phone'   => '+1234567890',
                'customer_email'   => 'john@example.com',
                'party_size'       => 2,
                'type'             => 'scheduled',
                'reserved_at'      => Carbon::now()->addDays(1)->setHour(19)->setMinute(0),
                'duration_minutes' => 90,
                'status'           => 'confirmed',
                'notes'            => 'Window seat preferred',
            ],
            [
                'table_id'         => $tables[1]->id,
                'created_by'       => $admin->id,
                'customer_name'    => 'Jane Smith',
                'customer_phone'   => '+1987654321',
                'party_size'       => 4,
                'type'             => 'scheduled',
                'reserved_at'      => Carbon::now()->addHours(2),
                'duration_minutes' => 120,
                'status'           => 'pending',
                'notes'            => 'Anniversary dinner',
            ],
            [
                'table_id'         => $tables[2]->id,
                'created_by'       => $admin->id,
                'customer_name'    => 'Walk-in Guest',
                'party_size'       => 2,
                'type'             => 'immediate',
                'reserved_at'      => Carbon::now()->subMinutes(15),
                'duration_minutes' => 60,
                'status'           => 'seated',
            ],
        ];

        foreach ($reservations as $res) {
            Reservation::create($res);
        }
    }
}
