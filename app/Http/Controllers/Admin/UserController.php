<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Exception;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $filters = $request->only(['search', 'role', 'is_active']);
        $users = $this->userService->getAllUsers($filters);
        $roles = Role::pluck('name');
        
        return view('admin.users.index', compact('users', 'filters', 'roles'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::pluck('name');
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $this->userService->createUser($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::pluck('name');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $this->userService->updateUser($user->id, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        try {
            $this->userService->deleteUser($user->id);
            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        try {
            $this->userService->toggleStatus($user->id);
            return back()->with('success', 'User status updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetPassword(ResetPasswordRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        $this->userService->resetPassword($user->id, $request->password);
        
        return back()->with('success', 'User password reset successfully.');
    }
}
