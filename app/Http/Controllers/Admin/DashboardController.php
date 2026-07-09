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
        // Kitchen staff have their own dedicated dashboard.
        if (auth()->user()->hasRole('kitchen_staff')) {
            return redirect()->route('admin.kitchen.index');
        }

        // All other roles (admin, waiter, cashier) reach the dashboard.
        // Per-widget @can directives in the view control what each role actually sees.
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
