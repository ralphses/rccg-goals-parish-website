<?php

namespace Database\Seeders;

use App\enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@rccggoalsparish.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
            'phone' => '08000000000',
        ]);

        // Pastor
        User::create([
            'name' => 'Zonal Pastor',
            'email' => 'zonal-pastor@rccggoalsparish.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PASTOR,
            'status' => UserStatus::ACTIVE,
        ]);

        // Editor
        User::create([
            'name' => 'Content Editor',
            'email' => 'editor@rccggoalsparish.com',
            'password' => Hash::make('password'),
            'role' => UserRole::EDITOR,
            'status' => UserStatus::ACTIVE,
        ]);

        // Media
        User::create([
            'name' => 'Media Manager',
            'email' => 'media@rccggoalsparish.com',
            'password' => Hash::make('password'),
            'role' => UserRole::MEDIA,
            'status' => UserStatus::ACTIVE,
        ]);
    }
}
