@extends('layouts.app')

@section('page_title', 'Edit Reservation')

@section('main_content')
<form action="{{ route('admin.reservations.update', $reservation) }}" method="POST">
@csrf @method('PUT')

<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Reservation Details</h3>
                <span class="badge badge-info">ID: #{{ $reservation->id }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name"
                                   class="form-control @error('customer_name') is-invalid @enderror"
                                   value="{{ old('customer_name', $reservation->customer_name) }}" required>
                            @error('customer_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="party_size">Party Size <span class="text-danger">*</span></label>
                            <input type="number" name="party_size" id="party_size" min="1" max="50"
                                   class="form-control @error('party_size') is-invalid @enderror"
                                   value="{{ old('party_size', $reservation->party_size) }}" required>
                            @error('party_size')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_phone">Phone</label>
                            <input type="text" name="customer_phone" id="customer_phone"
                                   class="form-control @error('customer_phone') is-invalid @enderror"
                                   value="{{ old('customer_phone', $reservation->customer_phone) }}">
                            @error('customer_phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <input type="email" name="customer_email" id="customer_email"
                                   class="form-control @error('customer_email') is-invalid @enderror"
                                   value="{{ old('customer_email', $reservation->customer_email) }}">
                            @error('customer_email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reserved_at">Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="reserved_at" id="reserved_at"
                                   class="form-control @error('reserved_at') is-invalid @enderror"
                                   value="{{ old('reserved_at', $reservation->reserved_at->format('Y-m-d\TH:i')) }}" required>
                            @error('reserved_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration_minutes">Duration (Minutes) <span class="text-danger">*</span></label>
                            <select name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" required>
                                <option value="30" {{ old('duration_minutes', $reservation->duration_minutes) == 30 ? 'selected' : '' }}>30 Minutes</option>
                                <option value="60" {{ old('duration_minutes', $reservation->duration_minutes) == 60 ? 'selected' : '' }}>1 Hour</option>
                                <option value="90" {{ old('duration_minutes', $reservation->duration_minutes) == 90 ? 'selected' : '' }}>1.5 Hours</option>
                                <option value="120" {{ old('duration_minutes', $reservation->duration_minutes) == 120 ? 'selected' : '' }}>2 Hours</option>
                                <option value="180" {{ old('duration_minutes', $reservation->duration_minutes) == 180 ? 'selected' : '' }}>3 Hours</option>
                            </select>
                            @error('duration_minutes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Special Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $reservation->notes) }}</textarea>
                    @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Table & Status</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="table_id">Select Table <span class="text-danger">*</span></label>
                    <select name="table_id" id="table_id" class="form-control @error('table_id') is-invalid @enderror" required>
                        <option value="">-- Choose Table --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('table_id', $reservation->table_id) == $table->id ? 'selected' : '' }}>
                                Table {{ $table->table_number }} (Cap: {{ $table->capacity }}) - {{ ucfirst($table->location ?? 'Main') }}
                            </option>
                        @endforeach
                    </select>
                    @error('table_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="type">Reservation Type <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="scheduled" {{ old('type', $reservation->type) == 'scheduled' ? 'selected' : '' }}>Scheduled Booking</option>
                        <option value="immediate" {{ old('type', $reservation->type) == 'immediate' ? 'selected' : '' }}>Immediate (Walk-in)</option>
                    </select>
                    @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="seated" {{ old('status', $reservation->status) == 'seated' ? 'selected' : '' }}>Seated</option>
                        <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ old('status', $reservation->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button type="submit" class="btn btn-info btn-block">
                    <i class="fas fa-save mr-1"></i> Update Reservation
                </button>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-default btn-block">Cancel</a>
            </div>
        </div>
    </div>
</div>
</form>
@stop
