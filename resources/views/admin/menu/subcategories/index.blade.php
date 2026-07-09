@extends('layouts.app')

@section('page_title', 'Menu Subcategories')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Management', 'url' => '#'],
        ['label' => 'Menu Subcategories'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage subcategories (e.g. Vegetarian, Gluten-Free under Appetizers).</p>
        @can('manage menu')
        <a href="{{ route('admin.menu-subcategories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Subcategory
        </a>
        @endcan
    </div>
</div>

@if ($subcategories->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Menu Subcategories Yet</h4>
            <p class="text-muted">Create your first subcategory to further organize your menu items.</p>
            @can('manage menu')
            <a href="{{ route('admin.menu-subcategories.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Subcategory
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list-alt mr-1"></i> All Subcategories</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Category</th>
                    <th scope="col">Name</th>
                    <th scope="col">Sort Order</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subcategories as $subcategory)
                    <tr>
                        <td>{{ $subcategory->id }}</td>
                        <td>
                            @if($subcategory->category)
                                <a href="{{ route('admin.menu-categories.show', $subcategory->category) }}">{{ $subcategory->category->name }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td><strong>{{ $subcategory->name }}</strong></td>
                        <td>{{ $subcategory->sort_order }}</td>
                        <td>
                            @if($subcategory->is_active)
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i>Inactive</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="{{ route('admin.menu-subcategories.show', $subcategory) }}" class="btn btn-xs btn-info" title="View Subcategory"><i class="fas fa-eye"></i></a>
                            @can('manage menu')
                            <a href="{{ route('admin.menu-subcategories.edit', $subcategory) }}" class="btn btn-xs btn-warning" title="Edit Subcategory"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-subcategories.destroy', $subcategory) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-xs btn-danger" title="Delete Subcategory"
                                    data-confirm="Delete subcategory '{{ $subcategory->name }}'? This cannot be undone."
                                    data-confirm-title="Delete Subcategory"
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
    @if($subcategories->hasPages())
    <div class="card-footer pb-0">
        {{ $subcategories->links() }}
    </div>
    @endif
</div>
@endif
@stop
