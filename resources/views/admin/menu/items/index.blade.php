@extends('layouts.app')

@section('page_title', 'Menu Items')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Management', 'url' => '#'],
        ['label' => 'Menu Items'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage individual menu items, their prices, and availability.</p>
        @can('manage menu')
        <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Menu Item
        </a>
        @endcan
    </div>
</div>

@if ($items->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-hamburger fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Menu Items Yet</h4>
            <p class="text-muted">Start building your menu by adding your first food or drink item.</p>
            @can('manage menu')
            <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Menu Item
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-hamburger mr-1"></i> All Menu Items</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Image</th>
                    <th scope="col">Category</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="align-middle">{{ $item->id }}</td>
                        <td class="align-middle">
                            @if($item->images->count() > 0)
                                <img src="{{ asset('storage/' . $item->images->first()->path) }}" alt="{{ $item->name }}" width="50" height="50" class="img-thumbnail" style="object-fit:cover;">
                            @else
                                <div class="bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 0.8rem;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($item->category)
                                <a href="{{ route('admin.menu-categories.show', $item->category) }}">{{ $item->category->name }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="align-middle"><strong>{{ $item->name }}</strong></td>
                        <td class="align-middle"><strong>{{ setting('billing.currency_symbol', '$') }}{{ number_format($item->price, 2) }}</strong></td>
                        <td class="align-middle">
                            @if($item->is_available)
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Available</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i>Unavailable</span>
                            @endif
                        </td>
                        <td class="align-middle text-center" style="white-space:nowrap;">
                            <a href="{{ route('admin.menu-items.show', $item) }}" class="btn btn-xs btn-info" title="View Item"><i class="fas fa-eye"></i></a>
                            @can('manage menu')
                            <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-xs btn-warning" title="Edit Item"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-xs btn-danger" title="Delete Item"
                                    data-confirm="Delete menu item '{{ $item->name }}'? This cannot be undone."
                                    data-confirm-title="Delete Menu Item"
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
    @if($items->hasPages())
    <div class="card-footer pb-0">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endif
@stop
