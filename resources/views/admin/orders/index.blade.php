@extends('layouts.app')

@section('page_title', 'Orders')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Orders'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage and track all restaurant orders.</p>
        @can('create', \App\Models\Order::class)
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Order
        </a>
        @endcan
    </div>
</div>

@if ($orders->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Orders Yet</h4>
            <p class="text-muted">There are no orders in the system. Create your first order to get started.</p>
            @can('create', \App\Models\Order::class)
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Order
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-receipt mr-1"></i> All Orders</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Order #</th>
                    <th scope="col">Date / Time</th>
                    <th scope="col">Table / Res.</th>
                    <th scope="col">Waiter</th>
                    <th scope="col">Status</th>
                    <th scope="col">Total</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong><br><small class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</small></td>
                    <td>{{ $order->created_at->format('d M Y') }}<br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small></td>
                    <td>
                        {{ $order->table->table_number ?? 'Walk-in' }}
                        @if($order->reservation)
                            <br><small class="text-info"><i class="fas fa-calendar-alt mr-1"></i>Res #{{ $order->reservation_id }}</small>
                        @endif
                    </td>
                    <td>{{ $order->waiter->name ?? 'System' }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'pending'   => 'warning',
                                'confirmed' => 'primary',
                                'preparing' => 'info',
                                'ready'     => 'success',
                                'served'    => 'dark',
                                'completed' => 'secondary',
                                'cancelled' => 'danger',
                            ];
                            $color = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        @can('update', $order)
                        <div class="dropdown">
                            <button class="btn btn-{{ $color }} btn-sm dropdown-toggle" type="button"
                                id="statusMenu{{ $order->id }}" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="statusMenu{{ $order->id }}">
                                @foreach($statusColors as $status => $c)
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <button type="submit" class="dropdown-item {{ $order->status === $status ? 'active' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        @endcan
                    </td>
                    <td><strong>{{ setting('billing.currency_symbol', '$') }}{{ number_format($order->total_amount, 2) }}</strong></td>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-info" title="View Order"><i class="fas fa-eye"></i></a>
                        @can('update', $order)
                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-xs btn-warning" title="Edit Order"><i class="fas fa-edit"></i></a>
                        @endcan
                        @can('delete', $order)
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-xs btn-danger" title="Delete Order"
                                data-confirm="Delete order {{ $order->order_number }}? This cannot be undone."
                                data-confirm-title="Delete Order"
                                data-confirm-btn="Yes, delete it">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer pb-0">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endif
@stop
