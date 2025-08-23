<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Get all users
     */
    public function getAllUsers(array $data): LengthAwarePaginator
    {
        $builder = $this->user->newQuery();

        if ($search = data_get($data, 'search')) {
            $builder = $this->user->searchFilter($builder, $search);
        }

        return $builder->paginate(data_get($data, 'per_page', User::PER_PAGE));
    }

    /**
     * Get a specific user
     */
    public function getUser(int $userId): User
    {
        return $this->user->findOrFail($userId);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        });
    }

    /**
     * Update an existing user
     */
    public function updateUser(array $data, int $userId): User
    {
        return DB::transaction(function () use ($data, $userId) {
            $user = $this->getUser($userId);

            if (data_get($data, 'password')) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
            $user->refresh();

            return $user;
        });
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->delete();
        });
    }
}
