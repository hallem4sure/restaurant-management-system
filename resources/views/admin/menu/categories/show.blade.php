@extends('layouts.app')

@section('page_title', 'Menu Category Details')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Categories', 'url' => route('admin.menu-categories.index')],
        ['label' => $menuCategory->name],
    ]])
@endsection

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $menuCategory->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menu-categories.edit', $menuCategory) }}" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9">{{ $menuCategory->id }}</dd>

            <dt class="col-sm-3">Section</dt>
            <dd class="col-sm-9">{{ $menuCategory->section->name ?? 'N/A' }}</dd>

            <dt class="col-sm-3">Name</dt>
            <dd class="col-sm-9">{{ $menuCategory->name }}</dd>

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9">{{ $menuCategory->description ?? 'N/A' }}</dd>

            <dt class="col-sm-3">Sort Order</dt>
            <dd class="col-sm-9">{{ $menuCategory->sort_order }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                @if($menuCategory->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </dd>
        </dl>
    </div>
</div>
@stop
