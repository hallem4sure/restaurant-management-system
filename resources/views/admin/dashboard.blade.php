@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard'],
    ]])
@endsection

@section('custom_css')
<style>
    .small-box .icon { top: 0px; }
</style>
@stop

@section('main_content')
    <!-- Small boxes (Stat box) — Row 1 -->
    <div class="row">
        {{-- Today's Revenue — only roles with view bills (admin, cashier) --}}
        @can('view bills')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    @php $sym = setting('billing.currency_symbol', '$'); $pos = setting('billing.currency_position', 'before'); @endphp
                    <h3>{{ $pos === 'before' ? $sym . number_format($stats['todayRevenue'], 2) : number_format($stats['todayRevenue'], 2) . $sym }}</h3>
                    <p>Today's Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.bills.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan

        {{-- Orders Today — all order-viewing roles --}}
        @can('view orders')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['ordersToday'] }}</h3>
                    <p>Orders Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan

        {{-- Pending / Active Orders --}}
        @can('view orders')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pendingOrders'] }}</h3>
                    <p>Pending / Active Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan

        {{-- Active Reservations --}}
        @can('view reservations')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['activeReservations'] }}</h3>
                    <p>Active Reservations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('admin.reservations.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan
    </div>

    <!-- Small boxes — Row 2 -->
    <div class="row">
        {{-- Occupied Tables --}}
        @can('view tables')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['occupiedTables'] }}</h3>
                    <p>Occupied Tables</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chair"></i>
                </div>
                <a href="{{ route('admin.tables.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan

        {{-- Pending Bills — only roles with view bills (admin, cashier) --}}
        @can('view bills')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-maroon">
                <div class="inner">
                    <h3>{{ $stats['pendingBills'] }}</h3>
                    <p>Pending Bills</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('admin.bills.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan

        {{-- Kitchen Queue — only roles with view kitchen (admin, kitchen_staff) --}}
        @can('view kitchen')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>{{ $stats['kitchenQueue'] }}</h3>
                    <p>Items in Kitchen Queue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fire-alt"></i>
                </div>
                <a href="{{ route('admin.kitchen.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan

        {{-- Available Tables --}}
        @can('view tables')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['availableTables'] }}<sup style="font-size: 20px">/{{ $stats['totalTables'] }}</sup></h3>
                    <p>Available Tables</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <a href="{{ route('admin.tables.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan
    </div>

    <!-- Charts Row — revenue chart only for roles with view reports -->
    <div class="row">
        @can('view reports')
        {{-- Revenue Chart --}}
        <div class="col-lg-8">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Revenue (Last 7 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        {{-- Orders by Status --}}
        <div class="col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Orders by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="ordersStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        @else
        {{-- Waiter: no revenue chart — show a full-width orders status chart instead --}}
        @can('view orders')
        <div class="col-lg-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Orders by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="ordersStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        @endcan
        @endcan
    </div>

    <!-- Additional Sections Row -->
    <div class="row">
        {{-- Recent Orders --}}
        @can('view orders')
        <div class="col-lg-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Recent Orders</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-tool btn-sm"><i class="fas fa-bars"></i> View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Table</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                {{-- Use show (read-only) route — safe for waiter, cashier, admin --}}
                                <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                <td>{{ $order->table->table_number ?? 'Walk-in' }}</td>
                                <td>
                                    @if(in_array($order->status, ['completed']))
                                        <span class="badge badge-success">Completed</span>
                                    @elseif(in_array($order->status, ['cancelled']))
                                        <span class="badge badge-danger">Cancelled</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">No recent orders</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan

        {{-- Top Selling Menu Items --}}
        @can('view menu')
        <div class="col-lg-4">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Top 5 Best Selling Items</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @forelse($topMenuItems as $item)
                        <li class="item">
                            <div class="product-info ml-1">
                                <span class="product-title">{{ $item['name'] }}
                                    <span class="badge badge-info float-right">{{ $item['total'] }} Sold</span>
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="item text-center">No data available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @endcan

        {{-- Recent Reservations --}}
        @can('view reservations')
        <div class="col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Recent Reservations</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-tool btn-sm"><i class="fas fa-bars"></i> View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Table</th>
                                <th>Date &amp; Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReservations as $res)
                            <tr>
                                <td>{{ $res->customer_name }}</td>
                                <td>{{ $res->table->table_number ?? 'N/A' }}</td>
                                <td>{{ $res->reserved_at->format('M d, H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">No recent reservations</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Low Stock Placeholder --}}
            @can('manage settings')
            <div class="card bg-gradient-dark">
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Inventory Alert (Future)</h3>
                </div>
                <div class="card-body text-center">
                    <h5 class="text-muted"><i class="fas fa-info-circle"></i> Low Stock Alerts Coming Soon</h5>
                    <p class="text-muted text-sm mb-0">The inventory management module will populate this card.</p>
                </div>
            </div>
            @endcan
        </div>
        @endcan
    </div>
@stop

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    @can('view reports')
    // Revenue Chart — admin & cashier only
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueChart['labels']) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($revenueChart['data']) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    @endcan

    @can('view orders')
    // Orders Status Chart — all order-viewing roles
    const statusCtx = document.getElementById('ordersStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ordersStatusChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($ordersStatusChart['data']) !!},
                backgroundColor: ['#ffc107', '#17a2b8', '#007bff', '#20c997', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });
    @endcan
});
</script>
@stop
