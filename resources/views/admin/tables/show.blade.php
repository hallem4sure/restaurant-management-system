@extends('layouts.app')

@section('page_title', 'Table Details')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Tables', 'url' => route('admin.tables.index')],
        ['label' => 'Table ' . $table->table_number],
    ]])
@endsection

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Table: {{ $table->table_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.tables.edit', $table) }}" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.tables.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Table Identifier</dt>
            <dd class="col-sm-9">{{ $table->table_number }}</dd>

            <dt class="col-sm-3">Capacity</dt>
            <dd class="col-sm-9">{{ $table->capacity }} Persons</dd>

            <dt class="col-sm-3">Type</dt>
            <dd class="col-sm-9">{{ $table->is_private ? 'Private / VIP' : 'Public' }}</dd>

            <dt class="col-sm-3">Current Status</dt>
            <dd class="col-sm-9">
                @php
                    $badgeClass = 'badge-info';
                    switch($table->status) {
                        case 'available': $badgeClass = 'badge-success'; break;
                        case 'occupied': $badgeClass = 'badge-danger'; break;
                        case 'reserved': $badgeClass = 'badge-warning'; break;
                        case 'maintenance': $badgeClass = 'badge-secondary'; break;
                    }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($table->status) }}</span>
            </dd>
        </dl>
    </div>
</div>
@stop
