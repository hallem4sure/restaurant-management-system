<?php

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;

class UserService implements UserServiceInterface
{
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles')->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        return $query->paginate($perPage);
    }

    public function findUser(int $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    public function createUser(array $data): User
    {
        if (isset($data['avatar_file'])) {
            $file = $data['avatar_file'];
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }

        $data['password'] = Hash::make($data['password']);
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->findUser($id);

        if (isset($data['avatar_file'])) {
            $file = $data['avatar_file'];
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;

            // Optionally delete old avatar here if needed, but not strictly required
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'avatar' => $data['avatar'] ?? $user->avatar,
        ]);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->findUser($id);

        if ($user->id === auth()->id()) {
            throw new Exception("You cannot delete your own account.");
        }

        // Check if user has related records
        $hasRelations = $user->orders()->exists() || 
                        $user->bills()->exists() || 
                        $user->reservations()->exists();

        if ($hasRelations) {
            throw new Exception("Cannot delete user because they have associated records (orders, bills, or reservations). Please deactivate the account instead.");
        }

        return $user->delete();
    }

    public function toggleStatus(int $id): User
    {
        $user = $this->findUser($id);

        if ($user->id === auth()->id()) {
            throw new Exception("You cannot deactivate your own account.");
        }

        $user->update(['is_active' => !$user->is_active]);

        return $user;
    }

    public function resetPassword(int $id, string $newPassword): User
    {
        $user = $this->findUser($id);
        $user->update(['password' => Hash::make($newPassword)]);
        
        return $user;
    }
}
