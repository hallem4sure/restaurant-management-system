<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\DashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DashboardServiceInterface $dashboardService
    ) {}

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Kitchen staff should only access the Kitchen Dashboard.
        // All other authenticated roles (admin, waiter, cashier) have 'view reservations'.
        if (!auth()->user()->can('view reservations')) {
            return redirect()->route('admin.kitchen.index');
        }

        $stats = $this->dashboardService->getStats();
        $revenueChart = $this->dashboardService->getRevenueChart();
        $ordersStatusChart = $this->dashboardService->getOrdersByStatusChart();
        $topMenuItems = $this->dashboardService->getTopMenuItems();
        $recentOrders = $this->dashboardService->getRecentOrders();
        $recentReservations = $this->dashboardService->getRecentReservations();

        return view('admin.dashboard', compact(
            'stats',
            'revenueChart',
            'ordersStatusChart',
            'topMenuItems',
            'recentOrders',
            'recentReservations'
        ));
    }
}
