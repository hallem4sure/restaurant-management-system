@extends('layouts.app')

@section('page_title', 'Menu Sections')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Management', 'url' => '#'],
        ['label' => 'Menu Sections'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage top-level menu sections (e.g. Food, Drinks).</p>
        @can('manage menu')
        <a href="{{ route('admin.menu-sections.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Section
        </a>
        @endcan
    </div>
</div>

@if ($sections->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Menu Sections Yet</h4>
            <p class="text-muted">Create your first menu section to organize your menu categories.</p>
            @can('manage menu')
            <a href="{{ route('admin.menu-sections.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Section
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-layer-group mr-1"></i> All Sections</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Sort Order</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sections as $section)
                    <tr>
                        <td>{{ $section->id }}</td>
                        <td><strong>{{ $section->name }}</strong></td>
                        <td>{{ $section->sort_order }}</td>
                        <td>
                            @if($section->is_active)
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i>Inactive</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="{{ route('admin.menu-sections.show', $section) }}" class="btn btn-xs btn-info" title="View Section"><i class="fas fa-eye"></i></a>
                            @can('manage menu')
                            <a href="{{ route('admin.menu-sections.edit', $section) }}" class="btn btn-xs btn-warning" title="Edit Section"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-sections.destroy', $section) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-xs btn-danger" title="Delete Section"
                                    data-confirm="Delete section '{{ $section->name }}'? This cannot be undone."
                                    data-confirm-title="Delete Section"
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
    @if($sections->hasPages())
    <div class="card-footer pb-0">
        {{ $sections->links() }}
    </div>
    @endif
</div>
@endif
@stop
