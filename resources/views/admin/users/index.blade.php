@extends('layouts.app')

@section('page_title', 'Staff & Users')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Staff & Users'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage system users, roles, and access permissions.</p>
        @can('manage users')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add New User
        </a>
        @endcan
    </div>
</div>

{{-- Filters --}}
<div class="card card-outline card-secondary mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Users</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body pb-0">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="search">Search Name / Email</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-3 form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="is_active">Status</label>
                    <select name="is_active" id="is_active" class="form-control">
                        <option value="">All</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 form-group d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Avatar</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td class="align-middle">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Avatar of {{ $user->name }}" class="img-circle elevation-1" width="40" height="40" style="object-fit:cover">
                        @else
                            <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" alt="Default Avatar" class="img-circle elevation-1" width="40">
                        @endif
                    </td>
                    <td class="align-middle"><strong>{{ $user->name }}</strong><br><small class="text-muted">{{ $user->phone ?? 'No phone' }}</small></td>
                    <td class="align-middle">{{ $user->email }}</td>
                    <td class="align-middle">
                        @foreach($user->roles as $role)
                            @php
                                $roleColors = ['admin'=>'danger','waiter'=>'primary','cashier'=>'success','kitchen_staff'=>'warning'];
                                $roleColor = $roleColors[$role->name] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $roleColor }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                        @endforeach
                    </td>
                    <td class="align-middle">
                        @can('manage users')
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            @if($user->id !== auth()->id())
                                <button type="button" class="btn btn-sm btn-{{ $user->is_active ? 'success' : 'secondary' }}"
                                    data-confirm="{{ $user->is_active ? 'Deactivate' : 'Activate' }} user {{ $user->name }}?"
                                    data-confirm-title="{{ $user->is_active ? 'Deactivate' : 'Activate' }} User"
                                    data-confirm-icon="{{ $user->is_active ? 'warning' : 'question' }}"
                                    data-confirm-btn="Yes, {{ $user->is_active ? 'deactivate' : 'activate' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>{{ $user->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            @else
                                <span class="badge badge-success" title="This is your account">
                                    <i class="fas fa-check-circle mr-1"></i> Active (You)
                                </span>
                            @endif
                        </form>
                        @else
                        <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                            <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @endcan
                    </td>
                    <td class="align-middle text-center" style="white-space:nowrap;">
                        @can('manage users')
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit User"><i class="fas fa-edit"></i></a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" title="Delete User"
                                data-confirm="Delete user {{ $user->name }}? This cannot be undone."
                                data-confirm-title="Delete User"
                                data-confirm-btn="Yes, delete user">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-users fa-2x text-muted mb-2 d-block"></i>
                        <span class="text-muted">No users found matching your filters.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@stop
