@extends('layouts.app')

@section('page_title', 'Menu Item Details')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Items', 'url' => route('admin.menu-items.index')],
        ['label' => $menuItem->name],
    ]])
@endsection

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $menuItem->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menu-items.edit', $menuItem) }}" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.menu-items.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if($menuItem->images->count() > 0)
                    <div id="itemCarousel" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($menuItem->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100" alt="Item Image">
                                </div>
                            @endforeach
                        </div>
                        @if($menuItem->images->count() > 1)
                            <a class="carousel-control-prev" href="#itemCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#itemCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center p-5 bg-light">
                        <i class="fas fa-image fa-4x text-muted"></i>
                        <p class="mt-2 text-muted">No images uploaded.</p>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $menuItem->id }}</dd>

                    <dt class="col-sm-3">Category</dt>
                    <dd class="col-sm-9">{{ $menuItem->category->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Subcategory</dt>
                    <dd class="col-sm-9">{{ $menuItem->subcategory->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Price</dt>
                    <dd class="col-sm-9">{{ setting('billing.currency_symbol', '$') }}{{ number_format($menuItem->price, 2) }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $menuItem->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Tags</dt>
                    <dd class="col-sm-9">
                        @forelse($menuItem->tags as $tag)
                            <span class="badge" style="background-color: {{ $tag->color ?? '#6c757d' }}; color: #fff;">{{ $tag->name }}</span>
                        @empty
                            <span class="text-muted">No tags</span>
                        @endforelse
                    </dd>

                    <dt class="col-sm-3">Sort Order</dt>
                    <dd class="col-sm-9">{{ $menuItem->sort_order }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($menuItem->is_available)
                            <span class="badge badge-success">Available</span>
                        @else
                            <span class="badge badge-danger">Unavailable</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@stop
