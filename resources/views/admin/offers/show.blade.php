@extends('layouts.app')

@section('page_title', 'Offer: ' . $offer->name)

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Offers', 'url' => route('admin.offers.index')],
        ['label' => $offer->name],
    ]])
@endsection

@section('main_content')
<div class="row">
    <div class="col-lg-8">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ $offer->name }}</h3>
                <div>
                    @can('manage offers')
                    <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('admin.offers.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if ($offer->description)
                    <p class="text-muted">{{ $offer->description }}</p>
                    <hr>
                @endif

                <dl class="row">
                    <dt class="col-sm-4">Discount Type</dt>
                    <dd class="col-sm-8">
                        @if ($offer->type === 'percentage')
                            <span class="badge badge-info"><i class="fas fa-percent mr-1"></i> Percentage</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-dollar-sign mr-1"></i> Fixed Amount</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Discount Value</dt>
                    <dd class="col-sm-8">
                        <strong class="text-success h5">
                            {{ $offer->type === 'percentage' ? $offer->value . '%' : number_format($offer->value, 2) . ' off' }}
                        </strong>
                    </dd>

                    <dt class="col-sm-4">Min Order Amount</dt>
                    <dd class="col-sm-8">{{ $offer->min_order_amount ? number_format($offer->min_order_amount, 2) : '—' }}</dd>

                    <dt class="col-sm-4">Max Discount Cap</dt>
                    <dd class="col-sm-8">{{ $offer->max_discount_amount ? number_format($offer->max_discount_amount, 2) : '—' }}</dd>

                    <dt class="col-sm-4">Valid From</dt>
                    <dd class="col-sm-8">{{ $offer->starts_at->format('d M Y, H:i') }}</dd>

                    <dt class="col-sm-4">Valid Until</dt>
                    <dd class="col-sm-8">{{ $offer->ends_at->format('d M Y, H:i') }}</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $offer->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $offer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>

                    @if ($offer->applicable_days)
                    <dt class="col-sm-4">Applicable Days</dt>
                    <dd class="col-sm-8">
                        @foreach ($offer->applicable_days as $day)
                            <span class="badge badge-light border mr-1">{{ ucfirst($day) }}</span>
                        @endforeach
                    </dd>
                    @endif

                    @if ($offer->applicable_from_time && $offer->applicable_to_time)
                    <dt class="col-sm-4">Time Window</dt>
                    <dd class="col-sm-8">{{ $offer->applicable_from_time }} – {{ $offer->applicable_to_time }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Applicable Menu Items</h3>
            </div>
            <div class="card-body p-0">
                @if ($offer->menuItems->isEmpty())
                    <p class="text-muted p-3 mb-0"><em>Applies to all menu items.</em></p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($offer->menuItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item->name }}
                                <small class="text-muted">{{ number_format($item->price, 2) }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
