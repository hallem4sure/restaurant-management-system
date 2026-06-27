<?php

namespace App\Services;

use App\Contracts\Services\KitchenServiceInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class KitchenService implements KitchenServiceInterface
{
    public function getActiveOrders(): Collection
    {
        return Order::with(['items.menuItem', 'waiter', 'table'])
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        $item = OrderItem::findOrFail($itemId);
        $item->update(['kitchen_status' => $status]);

        $this->syncOrderStatus($item->order_id);
    }

    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);

        // If order is marked as ready or completed, maybe update all items to ready?
        if (in_array($status, ['ready', 'served', 'completed'])) {
            $order->items()->update(['kitchen_status' => 'ready']);
        }
    }

    /**
     * Automatically update the parent order status based on its items.
     */
    protected function syncOrderStatus(int $orderId): void
    {
        $order = Order::with('items')->findOrFail($orderId);
        
        $totalItems = $order->items->count();
        if ($totalItems === 0) return;

        $readyItems = $order->items->where('kitchen_status', 'ready')->count();
        
        if ($readyItems === $totalItems) {
            $order->update(['status' => 'ready']);
        } elseif ($readyItems > 0 || $order->items->where('kitchen_status', 'preparing')->count() > 0) {
            // If some items are ready or preparing, the order is being prepared
            if ($order->status !== 'preparing') {
                $order->update(['status' => 'preparing']);
            }
        }
    }
}
