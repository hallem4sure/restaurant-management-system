<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\OrderServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\Reservation;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected OrderServiceInterface $orderService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $orders = $this->orderService->getAllOrders();
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $this->authorize('create', Order::class);
        $tables = RestaurantTable::all();
        $reservations = Reservation::whereIn('status', ['confirmed', 'seated'])->get();
        $menuItems = MenuItem::where('is_available', true)->get();
        return view('admin.orders.create', compact('tables', 'reservations', 'menuItems'));
    }

    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);
        $data = $request->validated();
        
        $this->orderService->createOrder($data);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['table', 'reservation', 'waiter', 'items.menuItem']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        $order->load(['items.menuItem']);
        $tables = RestaurantTable::all();
        $reservations = Reservation::whereIn('status', ['confirmed', 'seated'])->get();
        $menuItems = MenuItem::where('is_available', true)->get();
        return view('admin.orders.edit', compact('order', 'tables', 'reservations', 'menuItems'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $this->authorize('update', $order);
        $data = $request->validated();

        $this->orderService->updateOrder($order->id, $data);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $this->orderService->deleteOrder($order->id);
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled']);
        
        $this->orderService->updateStatus($order->id, $request->status);
        
        return back()->with('success', 'Order status updated successfully.');
    }
}
