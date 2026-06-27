@extends('layouts.app')

@section('page_title', 'Kitchen Dashboard')

@section('main_content')
<div class="row">
    @forelse($activeOrders as $order)
        <div class="col-md-4">
            <div class="card card-{{ $order->status === 'preparing' ? 'warning' : 'info' }}">
                <div class="card-header">
                    <h3 class="card-title">
                        #{{ $order->order_number }}
                        @if($order->table)
                            - Table {{ $order->table->table_number }}
                        @endif
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $order->status === 'preparing' ? 'warning' : 'light' }}">{{ strtoupper($order->status) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Item</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->quantity }}x</td>
                                    <td>
                                        {{ $item->menuItem->name }}
                                        @if($item->special_instructions)
                                            <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $item->special_instructions }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->kitchen_status === 'ready' ? 'success' : ($item->kitchen_status === 'preparing' ? 'warning' : 'secondary') }}">
                                            {{ $item->kitchen_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.kitchen.item.update-status', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                <option value="pending" {{ $item->kitchen_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="preparing" {{ $item->kitchen_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                                <option value="ready" {{ $item->kitchen_status === 'ready' ? 'selected' : '' }}>Ready</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.kitchen.order.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="input-group input-group-sm">
                            <select name="status" class="form-control">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                            </select>
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary btn-flat">Update Order</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No active orders in the kitchen.
            </div>
        </div>
    @endforelse
</div>
@endsection
