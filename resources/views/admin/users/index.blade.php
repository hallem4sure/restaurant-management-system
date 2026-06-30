@extends('layouts.app')

@section('page_title', 'User & Staff Management')

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-muted">Manage system users, roles, and access</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add New User
        </a>
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
                    <label>Search Name / Email</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-3 form-group">
                    <label>Role</label>
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control">
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
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td class="align-middle">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Avatar" class="img-circle elevation-1" width="40" height="40" style="object-fit:cover">
                        @else
                            <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" alt="Placeholder" class="img-circle elevation-1" width="40">
                        @endif
                    </td>
                    <td class="align-middle"><strong>{{ $user->name }}</strong><br><small class="text-muted">{{ $user->phone ?? 'No phone' }}</small></td>
                    <td class="align-middle">{{ $user->email }}</td>
                    <td class="align-middle">
                        @foreach($user->roles as $role)
                            <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                        @endforeach
                    </td>
                    <td class="align-middle">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'success' : 'danger' }}"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                title="{{ $user->id === auth()->id() ? 'Cannot deactivate yourself' : 'Toggle Status' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="align-middle text-center">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" {{ $user->id === auth()->id() ? 'disabled' : '' }} title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No users found.</td>
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
