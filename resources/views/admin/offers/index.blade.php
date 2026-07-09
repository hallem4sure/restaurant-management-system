@extends('layouts.app')

@section('page_title', 'Offers & Promotions')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Offers'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Create and manage promotional offers and discounts.</p>
        @can('manage offers')
        <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Offer
        </a>
        @endcan
    </div>
</div>

@if ($offers->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-percent fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Offers Yet</h4>
            <p class="text-muted">Create your first promotional offer to attract more customers.</p>
            @can('manage offers')
            <a href="{{ route('admin.offers.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Offer
            </a>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tags mr-1"></i> All Offers</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Value</th>
                    <th scope="col">Date Range</th>
                    <th scope="col">Items</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offers as $offer)
                <tr>
                    <td>
                        <strong>{{ $offer->name }}</strong>
                        @if ($offer->description)
                            <br><small class="text-muted">{{ Str::limit($offer->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        @if ($offer->type === 'percentage')
                            <span class="badge badge-info"><i class="fas fa-percent mr-1"></i>Percentage</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-tag mr-1"></i>Fixed</span>
                        @endif
                    </td>
                    <td>
                        <strong class="text-success">
                            {{ $offer->type === 'percentage' ? $offer->value . '%' : setting('billing.currency_symbol','$') . number_format($offer->value, 2) . ' off' }}
                        </strong>
                    </td>
                    <td>
                        <small>
                            <i class="fas fa-calendar-alt mr-1 text-muted"></i>
                            {{ $offer->starts_at->format('d M Y') }}<br>
                            <i class="fas fa-calendar-check mr-1 text-muted"></i>
                            {{ $offer->ends_at->format('d M Y') }}
                        </small>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $offer->menuItems_count ?? '—' }} items</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-xs {{ $offer->is_active ? 'btn-success' : 'btn-secondary' }}" title="Toggle status">
                                <i class="fas {{ $offer->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                                {{ $offer->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="{{ route('admin.offers.show', $offer) }}" class="btn btn-xs btn-info" title="View Offer"><i class="fas fa-eye"></i></a>
                        @can('manage offers')
                        <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-xs btn-warning" title="Edit Offer"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-xs btn-danger" title="Delete Offer"
                                data-confirm="Delete offer '{{ $offer->name }}'? This cannot be undone."
                                data-confirm-title="Delete Offer"
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
</div>
@endif
@stop
