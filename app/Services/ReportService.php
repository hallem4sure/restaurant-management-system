<?php

namespace App\Services;

use App\Contracts\Services\ReportServiceInterface;
use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService implements ReportServiceInterface
{
    public function getSummaryKPIs(string $startDate, string $endDate): array
    {
        $cacheKey = "reports.summary.{$startDate}.{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // Previous period for comparison
            $diffInDays = $start->diffInDays($end) + 1;
            $prevStart = $start->copy()->subDays($diffInDays)->startOfDay();
            $prevEnd = $end->copy()->subDays($diffInDays)->endOfDay();

            $currentRevenue = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end])
                ->sum('total_amount');
                
            $prevRevenue = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$prevStart, $prevEnd])
                ->sum('total_amount');

            $currentOrders = Order::whereBetween('created_at', [$start, $end])->count();
            $prevOrders = Order::whereBetween('created_at', [$prevStart, $prevEnd])->count();

            return [
                'revenue' => [
                    'current' => (float) $currentRevenue,
                    'previous' => (float) $prevRevenue,
                    'trend' => $this->calculateTrend($currentRevenue, $prevRevenue),
                ],
                'orders' => [
                    'current' => $currentOrders,
                    'previous' => $prevOrders,
                    'trend' => $this->calculateTrend($currentOrders, $prevOrders),
                ],
            ];
        });
    }

    public function getSalesData(string $startDate, string $endDate, string $groupBy = 'date'): array
    {
        $cacheKey = "reports.sales.{$startDate}.{$endDate}.{$groupBy}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate, $groupBy) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // Grouping format — MySQL uses DATE_FORMAT(), SQLite uses strftime()
            $mysqlFormat = match ($groupBy) {
                'month' => '%Y-%m',
                'year'  => '%Y',
                default => '%Y-%m-%d',
            };
            $sqliteFormat = $mysqlFormat; // same tokens happen to match

            $dateExpr = $this->dateFormat('paid_at', $mysqlFormat, $sqliteFormat);

            $sales = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end])
                ->select(
                    DB::raw("{$dateExpr} as label"),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(id) as count')
                )
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            return [
                'labels' => $sales->pluck('label')->toArray(),
                'totals' => $sales->pluck('total')->map(fn($v) => (float)$v)->toArray(),
                'counts' => $sales->pluck('count')->toArray(),
                'raw'    => $sales->toArray(),
            ];
        });
    }

    public function getRevenueData(string $startDate, string $endDate): array
    {
        $cacheKey = "reports.revenue.{$startDate}.{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $totals = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end])
                ->select(
                    DB::raw('SUM(subtotal) as gross_revenue'),
                    DB::raw('SUM(discount_amount) as total_discount'),
                    DB::raw('SUM(tax_amount) as tax_collected'),
                    DB::raw('SUM(service_charge_amount) as service_charges'),
                    DB::raw('SUM(total_amount) as net_revenue')
                )
                ->first();

            return [
                'gross_revenue' => (float) ($totals->gross_revenue ?? 0),
                'total_discount' => (float) ($totals->total_discount ?? 0),
                'tax_collected' => (float) ($totals->tax_collected ?? 0),
                'service_charges' => (float) ($totals->service_charges ?? 0),
                'net_revenue' => (float) ($totals->net_revenue ?? 0),
            ];
        });
    }

    public function getOrdersData(string $startDate, string $endDate): array
    {
        $cacheKey = "reports.orders.{$startDate}.{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $byStatus = Order::whereBetween('created_at', [$start, $end])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $byType = Order::whereBetween('created_at', [$start, $end])
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();

            $avgValue = Order::whereBetween('created_at', [$start, $end])->avg('total_amount') ?? 0;
            $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();

            return [
                'by_status' => $byStatus,
                'by_type' => $byType,
                'avg_value' => (float) $avgValue,
                'total_orders' => $totalOrders,
            ];
        });
    }

    public function getMenuAnalytics(string $startDate, string $endDate): array
    {
        $cacheKey = "reports.menu.{$startDate}.{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $items = OrderItem::whereHas('order', function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                })
                ->select(
                    'menu_item_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(subtotal) as total_revenue')
                )
                ->with(['menuItem' => function ($q) {
                    $q->select('id', 'name', 'menu_category_id')->with('category:id,name');
                }])
                ->groupBy('menu_item_id')
                ->orderByDesc('total_quantity')
                ->get();

            $bestSelling = $items->take(5);
            $worstSelling = $items->sortBy('total_quantity')->take(5);

            $byCategory = [];
            foreach ($items as $item) {
                if (!$item->menuItem || !$item->menuItem->category) continue;
                
                $catName = $item->menuItem->category->name;
                if (!isset($byCategory[$catName])) {
                    $byCategory[$catName] = ['quantity' => 0, 'revenue' => 0];
                }
                $byCategory[$catName]['quantity'] += $item->total_quantity;
                $byCategory[$catName]['revenue'] += $item->total_revenue;
            }

            return [
                'best_selling' => $bestSelling,
                'worst_selling' => $worstSelling,
                'by_category' => collect($byCategory)->sortByDesc('revenue')->toArray(),
                'all_items' => $items,
            ];
        });
    }

    public function getReservationsData(string $startDate, string $endDate): array
    {
        $cacheKey = "reports.reservations.{$startDate}.{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $byStatus = Reservation::whereBetween('reserved_at', [$start, $end])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $byDay = Reservation::whereBetween('reserved_at', [$start, $end])
                ->select(
                    DB::raw($this->dateFormat('reserved_at', '%Y-%m-%d', '%Y-%m-%d') . ' as label'),
                    DB::raw('COUNT(id) as count')
                )
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            return [
                'by_status' => $byStatus,
                'by_day' => [
                    'labels' => $byDay->pluck('label')->toArray(),
                    'counts' => $byDay->pluck('count')->toArray(),
                ],
            ];
        });
    }

    public function getStaffPerformance(string $startDate, string $endDate): array
    {
        $cacheKey = "reports.staff.{$startDate}.{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $waiters = Order::whereBetween('created_at', [$start, $end])
                ->select('waiter_id', DB::raw('count(*) as total_orders'), DB::raw('SUM(total_amount) as total_revenue'))
                ->with('waiter:id,name')
                ->groupBy('waiter_id')
                ->orderByDesc('total_orders')
                ->get();

            $cashiers = Bill::where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end])
                ->select('cashier_id', DB::raw('count(*) as total_bills'), DB::raw('SUM(total_amount) as total_revenue'))
                ->with('cashier:id,name')
                ->groupBy('cashier_id')
                ->orderByDesc('total_bills')
                ->get();

            return [
                'waiters' => $waiters,
                'cashiers' => $cashiers,
            ];
        });
    }

    public function exportCsv(string $type, string $startDate, string $endDate): StreamedResponse
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=report_{$type}_{$startDate}_{$endDate}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'sales':
                    fputcsv($file, ['Date', 'Total Orders', 'Revenue']);
                    $data = $this->getSalesData($startDate, $endDate, 'date');
                    foreach ($data['raw'] as $row) {
                        fputcsv($file, [$row['label'], $row['count'], $row['total']]);
                    }
                    break;
                case 'revenue':
                    fputcsv($file, ['Metric', 'Amount']);
                    $data = $this->getRevenueData($startDate, $endDate);
                    foreach ($data as $key => $val) {
                        fputcsv($file, [ucfirst(str_replace('_', ' ', $key)), $val]);
                    }
                    break;
                case 'orders':
                    fputcsv($file, ['Status', 'Count']);
                    $data = $this->getOrdersData($startDate, $endDate);
                    foreach ($data['by_status'] as $status => $count) {
                        fputcsv($file, [ucfirst($status), $count]);
                    }
                    break;
                case 'menu':
                    fputcsv($file, ['Menu Item', 'Category', 'Quantity Sold', 'Revenue']);
                    $data = $this->getMenuAnalytics($startDate, $endDate);
                    foreach ($data['all_items'] as $item) {
                        $name = $item->menuItem->name ?? 'Unknown';
                        $cat = $item->menuItem->category->name ?? 'Unknown';
                        fputcsv($file, [$name, $cat, $item->total_quantity, $item->total_revenue]);
                    }
                    break;
                case 'reservations':
                    fputcsv($file, ['Date', 'Count']);
                    $data = $this->getReservationsData($startDate, $endDate);
                    $labels = $data['by_day']['labels'];
                    $counts = $data['by_day']['counts'];
                    for ($i = 0; $i < count($labels); $i++) {
                        fputcsv($file, [$labels[$i], $counts[$i]]);
                    }
                    break;
                case 'staff':
                    fputcsv($file, ['Waiter', 'Orders Handled', 'Revenue']);
                    $data = $this->getStaffPerformance($startDate, $endDate);
                    foreach ($data['waiters'] as $w) {
                        $name = $w->waiter->name ?? 'System';
                        fputcsv($file, [$name, $w->total_orders, $w->total_revenue]);
                    }
                    break;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateTrend($current, $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100.0 : 0.0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Return a DB-driver-aware date-format SQL expression.
     *
     * MySQL  : DATE_FORMAT(column, '%Y-%m-%d')
     * SQLite : strftime('%Y-%m-%d', column)
     *
     * Both accept the same %-tokens so a single format string works for both.
     */
    private function dateFormat(string $column, string $mysqlFormat, string $sqliteFormat): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'mysql', 'mariadb' => "DATE_FORMAT({$column}, '{$mysqlFormat}')",
            default            => "strftime('{$sqliteFormat}', {$column})",
        };
    }
}
