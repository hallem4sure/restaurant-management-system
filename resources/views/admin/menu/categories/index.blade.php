@extends('layouts.app')

@section('page_title', 'Menu Categories')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Management', 'url' => '#'],
        ['label' => 'Menu Categories'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage menu categories (e.g. Appetizers, Main Course).</p>
        @can('manage menu')
        <a href="{{ route('admin.menu-categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Category
        </a>
        @endcan
    </div>
</div>

@if ($categories->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-list fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Menu Categories Yet</h4>
            <p class="text-muted">Create your first category to organize your menu items.</p>
            @can('manage menu')
            <a href="{{ route('admin.menu-categories.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Category
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-1"></i> All Categories</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Section</th>
                    <th scope="col">Name</th>
                    <th scope="col">Sort Order</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            @if($category->section)
                                <a href="{{ route('admin.menu-sections.show', $category->section) }}">{{ $category->section->name }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i>Inactive</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="{{ route('admin.menu-categories.show', $category) }}" class="btn btn-xs btn-info" title="View Category"><i class="fas fa-eye"></i></a>
                            @can('manage menu')
                            <a href="{{ route('admin.menu-categories.edit', $category) }}" class="btn btn-xs btn-warning" title="Edit Category"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-xs btn-danger" title="Delete Category"
                                    data-confirm="Delete category '{{ $category->name }}'? This cannot be undone."
                                    data-confirm-title="Delete Category"
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
    @if($categories->hasPages())
    <div class="card-footer pb-0">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endif
@stop
