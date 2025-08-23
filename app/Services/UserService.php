<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    /**
     * Update an existing user
     */
    public function updateUser(array $data, int $userId): User
    {
        $user = $this->getUser($userId);

        if (data_get($data, 'password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->refresh();

        return $user;
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
