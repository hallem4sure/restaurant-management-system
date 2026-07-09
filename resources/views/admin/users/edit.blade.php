@extends('layouts.app')

@section('page_title', 'Edit User: ' . $user->name)

@section('main_content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Edit Profile</h3>
            </div>
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST" data-loading enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="form-group text-center">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Avatar" class="img-circle elevation-2 mb-3" width="100" height="100" style="object-fit:cover">
                        @else
                            <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" alt="Placeholder" class="img-circle elevation-2 mb-3" width="100">
                        @endif
                        <div>
                            <label for="avatar_file" class="btn btn-sm btn-outline-secondary">Change Avatar</label>
                            <input type="file" name="avatar_file" id="avatar_file" class="d-none" accept="image/*">
                        </div>
                        @error('avatar_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="role">Assign Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">— Select Role —</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $user->roles->first()->name ?? '') == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Password Reset Panel --}}
    @can('manage users')
    <div class="col-md-4">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-key mr-1"></i> Reset Password</h3>
            </div>
            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                @csrf @method('PATCH')
                <div class="card-body">
                    <p class="small text-muted">Manually reset this user's password. They will be able to log in with the new password immediately.</p>
                    
                    <div class="form-group">
                        <label>New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-danger btn-sm" onsubmit="return confirm('Are you sure you want to change this password?');">
                        <i class="fas fa-lock mr-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>
@stop
