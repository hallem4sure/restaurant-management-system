<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\KitchenServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KitchenController extends Controller
{
    use AuthorizesRequests;

    protected $kitchenService;

    public function __construct(KitchenServiceInterface $kitchenService)
    {
        $this->kitchenService = $kitchenService;
    }

    public function index()
    {
        $this->authorize('view kitchen');
        
        $activeOrders = $this->kitchenService->getActiveOrders();
        
        return view('admin.kitchen.index', compact('activeOrders'));
    }

    public function updateItemStatus(Request $request, $itemId)
    {
        $this->authorize('update kitchen status');
        
        $request->validate([
            'status' => 'required|in:pending,preparing,ready'
        ]);

        $this->kitchenService->updateItemStatus($itemId, $request->status);

        return redirect()->back()->with('success', 'Item status updated.');
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $this->authorize('update kitchen status');
        
        $request->validate([
            'status' => 'required|in:pending,preparing,ready'
        ]);

        $this->kitchenService->updateOrderStatus($orderId, $request->status);

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
