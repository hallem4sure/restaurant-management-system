@extends('layouts.app')

@section('page_title', 'Order Details')

@section('main_content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Order #{{ $order->order_number }}</h3>
                <div>
                    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->menuItem->name ?? 'Unknown Item' }}</strong>
                                @if($item->special_instructions)
                                    <br><small class="text-muted"><i class="fas fa-info-circle"></i> {{ $item->special_instructions }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <div class="row justify-content-end">
                    <div class="col-sm-6 col-md-5">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Subtotal:</th>
                                <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Tax ({{ $order->tax_rate }}%):</th>
                                <td class="text-right">{{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Svc Charge ({{ $order->service_charge_rate }}%):</th>
                                <td class="text-right">{{ number_format($order->service_charge_amount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <th><h4>Total:</h4></th>
                                <td class="text-right"><h4>{{ number_format($order->total_amount, 2) }}</h4></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Order Information</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7">
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
                        <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                    </dd>
                    
                    <dt class="col-sm-5">Type</dt>
                    <dd class="col-sm-7">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</dd>

                    <dt class="col-sm-5">Table</dt>
                    <dd class="col-sm-7">{{ $order->table ? 'Table ' . $order->table->table_number : 'N/A' }}</dd>

                    <dt class="col-sm-5">Reservation</dt>
                    <dd class="col-sm-7">
                        @if($order->reservation)
                            <a href="{{ route('admin.reservations.show', $order->reservation_id) }}">#{{ $order->reservation_id }} ({{ $order->reservation->customer_name }})</a>
                        @else
                            N/A
                        @endif
                    </dd>

                    <dt class="col-sm-5">Waiter</dt>
                    <dd class="col-sm-7">{{ $order->waiter->name ?? 'System' }}</dd>

                    <dt class="col-sm-5">Date</dt>
                    <dd class="col-sm-7">{{ $order->created_at->format('d M Y, H:i') }}</dd>
                </dl>

                @if($order->special_instructions)
                    <hr>
                    <strong>Special Instructions:</strong>
                    <p class="text-muted">{{ $order->special_instructions }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
