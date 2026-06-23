@extends('layouts.app')

@section('page_title', 'Reservations Management')

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Reservation
        </a>
    </div>
</div>

@if ($reservations->isEmpty())
    <div class="alert alert-info">No reservations yet.</div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> All Reservations</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Date & Time</th>
                    <th>Customer Name</th>
                    <th>Contact</th>
                    <th>Party Size</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
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
                        <span class="badge badge-info">{{ $reservation->party_size }} Persons</span>
                    </td>
                    <td>
                        <strong>{{ $reservation->table->table_number ?? 'Unknown' }}</strong>
                        <small class="text-muted">(Cap: {{ $reservation->table->capacity ?? '?' }})</small>
                    </td>
                    <td>
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
                        
                        <div class="dropdown">
                            <button class="btn btn-{{ $color }} btn-sm dropdown-toggle" type="button" id="statusMenu{{ $reservation->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this reservation?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
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
