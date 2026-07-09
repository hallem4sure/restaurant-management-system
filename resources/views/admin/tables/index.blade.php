@extends('layouts.app')

@section('page_title', 'Tables')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Tables'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage restaurant seating and table status.</p>
        @can('manage tables')
        <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add New Table
        </a>
        @endcan
    </div>
</div>

<div class="row">
    @forelse ($tables as $table)
        <div class="col-lg-3 col-6">
            @php
                $bgClass = 'bg-info';
                switch($table->status) {
                    case 'available': $bgClass = 'bg-success'; break;
                    case 'occupied': $bgClass = 'bg-danger'; break;
                    case 'reserved': $bgClass = 'bg-warning'; break;
                    case 'maintenance': $bgClass = 'bg-secondary'; break;
                }
            @endphp
            <div class="small-box {{ $bgClass }}">
                <div class="inner">
                    <h3>{{ $table->table_number }}</h3>
                    <p>
                        Capacity: {{ $table->capacity }}<br>
                        Type: {{ $table->is_private ? 'Private' : 'Public' }}<br>
                        Status: {{ ucfirst($table->status) }}
                    </p>
                </div>
                <div class="icon">
                    <i class="fas {{ $table->is_private ? 'fa-user-secret' : 'fa-users' }}"></i>
                </div>
                <div class="small-box-footer">
                    <a href="{{ route('admin.tables.show', $table) }}" class="text-white mr-2" title="View"><i class="fas fa-eye"></i></a>
                    @can('manage tables')
                    <a href="{{ route('admin.tables.edit', $table) }}" class="text-white mr-2" title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-link p-0 text-white" title="Delete Table"
                            data-confirm="Delete table {{ $table->table_number }}? This cannot be undone."
                            data-confirm-title="Delete Table"
                            data-confirm-btn="Yes, delete it"><i class="fas fa-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Tables Added Yet</h4>
                    <p class="text-muted">Add your restaurant tables to start managing seating and reservations.</p>
                    @can('manage tables')
                    <a href="{{ route('admin.tables.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus mr-1"></i> Add First Table
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    @endforelse
</div>
@stop
