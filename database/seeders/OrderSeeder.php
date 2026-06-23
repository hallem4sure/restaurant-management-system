<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $waiter = User::whereHas('roles', function($q) {
            $q->where('name', 'waiter');
        })->first() ?? User::first();

        $table = RestaurantTable::first();
        $reservation = Reservation::first();
        $menuItems = MenuItem::take(3)->get();

        if ($menuItems->isEmpty()) {
            return;
        }

        // Create 2 Demo Orders
        $orderData = [
            [
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'table_id' => $table->id ?? null,
                'reservation_id' => $reservation->id ?? null,
                'waiter_id' => $waiter->id,
                'type' => 'reservation',
                'status' => 'served',
                'subtotal' => 0,
                'tax_rate' => 5.00,
                'tax_amount' => 0,
                'service_charge_rate' => 10.00,
                'service_charge_amount' => 0,
                'total_amount' => 0,
                'special_instructions' => 'Extra spicy please.',
            ],
            [
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'table_id' => $table->id ?? null,
                'reservation_id' => null,
                'waiter_id' => $waiter->id,
                'type' => 'walk_in',
                'status' => 'preparing',
                'subtotal' => 0,
                'tax_rate' => 5.00,
                'tax_amount' => 0,
                'service_charge_rate' => 10.00,
                'service_charge_amount' => 0,
                'total_amount' => 0,
                'special_instructions' => null,
            ]
        ];

        foreach ($orderData as $data) {
            $order = Order::create($data);
            
            $subtotal = 0;
            // Add items
            foreach ($menuItems as $index => $menuItem) {
                $qty = rand(1, 3);
                $itemSubtotal = $menuItem->price * $qty;
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $qty,
                    'unit_price' => $menuItem->price,
                    'subtotal' => $itemSubtotal,
                    'kitchen_status' => 'pending'
                ]);
                $subtotal += $itemSubtotal;
            }

            // Update order totals
            $tax = ($subtotal * $order->tax_rate) / 100;
            $service = ($subtotal * $order->service_charge_rate) / 100;
            
            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'service_charge_amount' => $service,
                'total_amount' => $subtotal + $tax + $service,
            ]);
        }
    }
}
