<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findUser(int $id): User;
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): User;
    public function deleteUser(int $id): bool;
    public function toggleStatus(int $id): User;
    public function resetPassword(int $id, string $newPassword): User;
}
