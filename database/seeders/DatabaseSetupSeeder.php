<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_AUTHOR = 'author';
    const ROLE_READER = 'reader';

    public function run(): void
    {
        $roles = [
            self::ROLE_ADMIN => Role::firstOrCreate(['name' => self::ROLE_ADMIN]),
            self::ROLE_AUTHOR => Role::firstOrCreate(['name' => self::ROLE_AUTHOR]),
            self::ROLE_READER => Role::firstOrCreate(['name' => self::ROLE_READER]),
        ];

        $users = [
            [
                'name' => 'User 1',
                'email' => 'user1@example.com',
                'password' => 'password', // Hanya string biasa, akan di-hash
                'roles' => [self::ROLE_ADMIN, self::ROLE_AUTHOR, self::ROLE_READER]
            ],
            [
                'name' => 'User 2',
                'email' => 'user2@example.com',
                'password' => 'password',
                'roles' => [self::ROLE_ADMIN, self::ROLE_AUTHOR, self::ROLE_READER]
            ],
            [
                'name' => 'User 3',
                'email' => 'user3@example.com',
                'password' => 'password',
                'roles' => [self::ROLE_AUTHOR, self::ROLE_READER]
            ],
            [
                'name' => 'User 4',
                'email' => 'user4@example.com',
                'password' => 'password',
                'roles' => [self::ROLE_READER]
            ],
            [
                'name' => 'User 5',
                'email' => 'user5@example.com',
                'password' => 'password',
                'roles' => [self::ROLE_READER]
            ],
        ];

        foreach ($users as $userData) {
            // create user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']), // Menggunakan Hash::make
            ]);

            // attach role to user
            $roleIds = array_map(fn($role) => $roles[$role]->id, $userData['roles']);
            $user->roles()->attach($roleIds);
        }
    }
}
