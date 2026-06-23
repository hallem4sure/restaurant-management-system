@extends('layouts.app')

@section('page_title', 'Orders Management')

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Order
        </a>
    </div>
</div>

@if ($orders->isEmpty())
    <div class="alert alert-info">No orders yet.</div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-receipt mr-1"></i> All Orders</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Order #</th>
                    <th>Date / Time</th>
                    <th>Table / Res.</th>
                    <th>Waiter</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong><br><small class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</small></td>
                    <td>{{ $order->created_at->format('d M Y') }}<br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small></td>
                    <td>
                        T{{ $order->table->table_number ?? '?' }}
                        @if($order->reservation)
                            <br><small class="text-info">Res #{{ $order->reservation_id }}</small>
                        @endif
                    </td>
                    <td>{{ $order->waiter->name ?? 'System' }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'primary',
                                'preparing' => 'info',
                                'ready' => 'success',
                                'served' => 'dark',
                                'completed' => 'secondary',
                                'cancelled' => 'danger',
                            ];
                            $color = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        <div class="dropdown">
                            <button class="btn btn-{{ $color }} btn-sm dropdown-toggle" type="button" id="statusMenu{{ $order->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    </td>
                    <td><strong>{{ number_format($order->total_amount, 2) }}</strong></td>
                    <td class="text-center">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        @can('delete', $order)
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this order?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
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
