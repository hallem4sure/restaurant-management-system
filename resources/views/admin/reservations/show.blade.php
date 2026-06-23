@extends('layouts.app')

@section('page_title', 'Reservation Details')

@section('main_content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Reservation #{{ $reservation->id }}</h3>
                <div>
                    <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Customer Name</dt>
                    <dd class="col-sm-8">{{ $reservation->customer_name }}</dd>

                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $reservation->customer_phone ?? '—' }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $reservation->customer_email ?? '—' }}</dd>

                    <dt class="col-sm-4">Party Size</dt>
                    <dd class="col-sm-8">{{ $reservation->party_size }} Persons</dd>

                    <dt class="col-sm-4">Date & Time</dt>
                    <dd class="col-sm-8">{{ $reservation->reserved_at->format('d M Y, H:i') }}</dd>

                    <dt class="col-sm-4">Duration</dt>
                    <dd class="col-sm-8">{{ $reservation->duration_minutes }} Minutes</dd>

                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8">{{ ucfirst($reservation->type) }}</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'primary',
                                'seated' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'no_show' => 'dark',
                            ];
                            $color = $statusColors[$reservation->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    </dd>

                    <dt class="col-sm-4">Created By</dt>
                    <dd class="col-sm-8">{{ $reservation->creator->name ?? 'System/Online' }}</dd>

                    @if ($reservation->notes)
                    <dt class="col-sm-4">Notes</dt>
                    <dd class="col-sm-8">{{ $reservation->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Table Details</h3>
            </div>
            <div class="card-body">
                @if($reservation->table)
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Table No.</dt>
                        <dd class="col-sm-7">{{ $reservation->table->table_number }}</dd>
                        
                        <dt class="col-sm-5">Capacity</dt>
                        <dd class="col-sm-7">{{ $reservation->table->capacity }} Persons</dd>
                        
                        <dt class="col-sm-5">Location</dt>
                        <dd class="col-sm-7">{{ ucfirst($reservation->table->location ?? 'Main Area') }}</dd>
                    </dl>
                @else
                    <div class="text-muted">No table assigned.</div>
                @endif
            </div>
        </div>
        
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">Orders</h3>
            </div>
            <div class="card-body p-0">
                @if($reservation->orders->isEmpty())
                    <p class="text-muted p-3 mb-0">No orders associated with this reservation yet.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($reservation->orders as $order)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Order #{{ $order->order_number }}</span>
                                <span class="badge badge-primary">{{ number_format($order->total_amount, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
