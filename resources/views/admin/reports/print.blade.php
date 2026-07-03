<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ setting('general.restaurant_name', 'Restaurant') }} - Report ({{ ucfirst($activeTab) }})</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .logo { max-height: 80px; margin-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; margin: 0 0 10px; }
        .meta { color: #555; font-size: 12px; }
        .meta div { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 20px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="header">
        @if(setting('general.restaurant_logo'))
            <img src="{{ asset(setting('general.restaurant_logo')) }}" class="logo" alt="Logo">
        @endif
        <div class="title">{{ setting('general.restaurant_name', 'Restaurant') }}</div>
        <h2>{{ ucfirst($activeTab) }} Report</h2>
        
        <div class="meta">
            <div><strong>Date Range:</strong> {{ $startDate }} to {{ $endDate }}</div>
            <div><strong>Printed On:</strong> {{ now()->format('M d, Y H:i:s') }}</div>
            <div><strong>Printed By:</strong> {{ auth()->user()->name }}</div>
        </div>
    </div>

    @php
        $sym = setting('billing.currency_symbol', '$');
        $pos = setting('billing.currency_position', 'before');
        $formatCurrency = function($amount) use ($sym, $pos) {
            return $pos === 'before' ? $sym . number_format($amount, 2) : number_format($amount, 2) . $sym;
        };
    @endphp

    <div class="content">
        @if($activeTab == 'sales')
            <table>
                <thead>
                    <tr><th>Date</th><th>Total Orders</th><th>Revenue</th></tr>
                </thead>
                <tbody>
                    @forelse($data['raw'] as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $formatCurrency($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($activeTab == 'revenue')
            <table>
                <tbody>
                    <tr><th>Gross Revenue</th><td>{{ $formatCurrency($data['gross_revenue']) }}</td></tr>
                    <tr><th>Total Discounts</th><td>- {{ $formatCurrency($data['total_discount']) }}</td></tr>
                    <tr><th>Tax Collected</th><td>{{ $formatCurrency($data['tax_collected']) }}</td></tr>
                    <tr><th>Service Charges</th><td>{{ $formatCurrency($data['service_charges']) }}</td></tr>
                    <tr><th>Net Revenue</th><td><strong>{{ $formatCurrency($data['net_revenue']) }}</strong></td></tr>
                </tbody>
            </table>
        @elseif($activeTab == 'orders')
            <h3>Orders by Status</h3>
            <table>
                <thead><tr><th>Status</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($data['by_status'] as $status => $count)
                        <tr><td>{{ ucfirst($status) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <h3>Orders by Type</h3>
            <table>
                <thead><tr><th>Type</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach($data['by_type'] as $type => $count)
                        <tr><td>{{ ucfirst($type) }}</td><td>{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <p><strong>Average Order Value:</strong> {{ $formatCurrency($data['avg_value']) }}</p>
            <p><strong>Total Orders:</strong> {{ $data['total_orders'] }}</p>
        @elseif($activeTab == 'menu')
            <h3>All Menu Items Sold</h3>
            <table>
                <thead><tr><th>Item Name</th><th>Category</th><th>Quantity Sold</th><th>Revenue</th></tr></thead>
                <tbody>
                    @forelse($data['all_items'] as $item)
                        <tr>
                            <td>{{ $item->menuItem->name ?? 'Unknown' }}</td>
                            <td>{{ $item->menuItem->category->name ?? 'Unknown' }}</td>
                            <td>{{ $item->total_quantity }}</td>
                            <td>{{ $formatCurrency($item->total_revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($activeTab == 'reservations')
            <h3>Reservations per Day</h3>
            <table>
                <thead><tr><th>Date</th><th>Count</th></tr></thead>
                <tbody>
                    @for($i=0; $i < count($data['by_day']['labels']); $i++)
                        <tr>
                            <td>{{ $data['by_day']['labels'][$i] }}</td>
                            <td>{{ $data['by_day']['counts'][$i] }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        @elseif($activeTab == 'staff')
            <h3>Waiter Performance</h3>
            <table>
                <thead><tr><th>Name</th><th>Orders Handled</th><th>Revenue Generated</th></tr></thead>
                <tbody>
                    @forelse($data['waiters'] as $w)
                        <tr>
                            <td>{{ $w->waiter->name ?? 'Unknown' }}</td>
                            <td>{{ $w->total_orders }}</td>
                            <td>{{ $formatCurrency($w->total_revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <h3>Cashier Performance</h3>
            <table>
                <thead><tr><th>Name</th><th>Bills Processed</th><th>Revenue Processed</th></tr></thead>
                <tbody>
                    @forelse($data['cashiers'] as $c)
                        <tr>
                            <td>{{ $c->cashier->name ?? 'Unknown' }}</td>
                            <td>{{ $c->total_bills }}</td>
                            <td>{{ $formatCurrency($c->total_revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer">
        Generated by {{ config('app.name') }} Reporting Module
    </div>

</body>
</html>
