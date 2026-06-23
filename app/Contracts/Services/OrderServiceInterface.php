<?php

namespace App\Contracts\Services;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function getAllOrders(int $perPage = 15): LengthAwarePaginator;

    public function findOrder(int $id): Order;

    public function createOrder(array $data): Order;

    public function updateOrder(int $id, array $data): Order;

    public function deleteOrder(int $id): void;

    public function updateStatus(int $id, string $status): Order;
}
