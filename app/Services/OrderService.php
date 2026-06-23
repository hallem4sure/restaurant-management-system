<?php

namespace App\Services;

use App\Contracts\Services\OrderServiceInterface;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['table', 'reservation', 'waiter'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findOrder(int $id): Order
    {
        return Order::with(['items.menuItem', 'table', 'waiter'])->findOrFail($id);
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $orderNumber = $this->generateOrderNumber();
            
            $subtotal = 0;
            $items = [];
            
            foreach ($data['items'] ?? [] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $quantity = (int)$itemData['quantity'];
                $itemSubtotal = $menuItem->price * $quantity;
                
                $items[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $menuItem->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $itemData['special_instructions'] ?? null,
                    'kitchen_status' => 'pending',
                ];
                
                $subtotal += $itemSubtotal;
            }

            // Defaults (Tax & Service Charge could be fetched from DB Settings here)
            $taxRate = 0;
            $serviceChargeRate = 0;
            
            $taxAmount = ($subtotal * $taxRate) / 100;
            $serviceChargeAmount = ($subtotal * $serviceChargeRate) / 100;
            $totalAmount = $subtotal + $taxAmount + $serviceChargeAmount; // omitting discount logic for brevity

            $order = Order::create([
                'order_number' => $orderNumber,
                'table_id' => $data['table_id'] ?? null,
                'reservation_id' => $data['reservation_id'] ?? null,
                'waiter_id' => auth()->id(),
                'type' => $data['type'] ?? 'walk_in',
                'status' => $data['status'] ?? 'pending',
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'service_charge_rate' => $serviceChargeRate,
                'service_charge_amount' => $serviceChargeAmount,
                'total_amount' => $totalAmount,
                'special_instructions' => $data['special_instructions'] ?? null,
            ]);

            if (!empty($items)) {
                $order->items()->createMany($items);
            }

            return $order;
        });
    }

    public function updateOrder(int $id, array $data): Order
    {
        return DB::transaction(function () use ($id, $data) {
            $order = Order::findOrFail($id);
            
            // Delete old items
            $order->items()->delete();
            
            $subtotal = 0;
            $items = [];
            
            foreach ($data['items'] ?? [] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $quantity = (int)$itemData['quantity'];
                $itemSubtotal = $menuItem->price * $quantity;
                
                $items[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $menuItem->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $itemData['special_instructions'] ?? null,
                    'kitchen_status' => 'pending',
                ];
                
                $subtotal += $itemSubtotal;
            }

            $taxRate = $order->tax_rate;
            $serviceChargeRate = $order->service_charge_rate;
            
            $taxAmount = ($subtotal * $taxRate) / 100;
            $serviceChargeAmount = ($subtotal * $serviceChargeRate) / 100;
            $totalAmount = $subtotal + $taxAmount + $serviceChargeAmount;

            $order->update([
                'table_id' => $data['table_id'] ?? $order->table_id,
                'reservation_id' => $data['reservation_id'] ?? $order->reservation_id,
                'type' => $data['type'] ?? $order->type,
                'status' => $data['status'] ?? $order->status,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_charge_amount' => $serviceChargeAmount,
                'total_amount' => $totalAmount,
                'special_instructions' => $data['special_instructions'] ?? $order->special_instructions,
            ]);

            if (!empty($items)) {
                $order->items()->createMany($items);
            }

            return $order;
        });
    }

    public function deleteOrder(int $id): void
    {
        DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);
            $order->items()->delete();
            $order->delete();
        });
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);
        return $order;
    }
    
    protected function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $timestamp = date('Ymd');
        $random = strtoupper(Str::random(5));
        
        $number = $prefix . $timestamp . '-' . $random;
        
        // Ensure uniqueness
        while (Order::where('order_number', $number)->exists()) {
            $random = strtoupper(Str::random(5));
            $number = $prefix . $timestamp . '-' . $random;
        }
        
        return $number;
    }
}
