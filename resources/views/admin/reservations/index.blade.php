@extends('layouts.app')

@section('page_title', 'Reservations')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Reservations'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage table reservations and bookings.</p>
        @can('create reservations')
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Reservation
        </a>
        @endcan
    </div>
</div>

@if ($reservations->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Reservations Yet</h4>
            <p class="text-muted">There are currently no reservations. Create one to get started.</p>
            @can('create reservations')
            <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Reservation
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> All Reservations</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Date &amp; Time</th>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Party Size</th>
                    <th scope="col">Table</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                <tr>
                    <td>
                        <strong>{{ $reservation->reserved_at->format('d M Y') }}</strong><br>
                        <span class="text-muted">{{ $reservation->reserved_at->format('H:i') }} ({{ $reservation->duration_minutes }}m)</span>
                    </td>
                    <td>
                        <strong>{{ $reservation->customer_name }}</strong><br>
                        <small class="badge badge-light">{{ ucfirst($reservation->type) }}</small>
                    </td>
                    <td>
                        {{ $reservation->customer_phone ?? '—' }}<br>
                        <small class="text-muted">{{ $reservation->customer_email }}</small>
                    </td>
                    <td>
                        <span class="badge badge-info"><i class="fas fa-users mr-1"></i>{{ $reservation->party_size }}</span>
                    </td>
                    <td>
                        <strong>{{ $reservation->table->table_number ?? 'Unknown' }}</strong>
                        <small class="text-muted">(Cap: {{ $reservation->table->capacity ?? '?' }})</small>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'pending'   => 'warning',
                                'confirmed' => 'primary',
                                'seated'    => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                'no_show'   => 'dark',
                            ];
                            $color = $statusColors[$reservation->status] ?? 'secondary';
                        @endphp
                        @can('update reservation status')
                        <div class="dropdown">
                            <button class="btn btn-{{ $color }} btn-sm dropdown-toggle" type="button"
                                id="statusMenu{{ $reservation->id }}" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="statusMenu{{ $reservation->id }}">
                                @foreach($statusColors as $status => $c)
                                    <form action="{{ route('admin.reservations.update-status', $reservation) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <button type="submit" class="dropdown-item {{ $reservation->status === $status ? 'active' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                        @endcan
                    </td>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-xs btn-info" title="View Reservation"><i class="fas fa-eye"></i></a>
                        @can('update', $reservation)
                        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-xs btn-warning" title="Edit Reservation"><i class="fas fa-edit"></i></a>
                        @endcan
                        @can('delete', $reservation)
                        <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-xs btn-danger" title="Delete Reservation"
                                data-confirm="Delete reservation for {{ $reservation->customer_name }}? This cannot be undone."
                                data-confirm-title="Delete Reservation"
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
    @if($reservations->hasPages())
    <div class="card-footer pb-0">
        {{ $reservations->links() }}
    </div>
    @endif
</div>
@endif
@stop
