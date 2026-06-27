<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface KitchenServiceInterface
{
    /**
     * Get all active orders for the kitchen (e.g., pending, confirmed, preparing).
     */
    public function getActiveOrders(): Collection;

    /**
     * Update the kitchen status of a specific order item.
     */
    public function updateItemStatus(int $itemId, string $status): void;

    /**
     * Update the overall status of an order.
     */
    public function updateOrderStatus(int $orderId, string $status): void;
}
