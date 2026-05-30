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
            'email' => 'admin@goalsparish.org',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
            'phone' => '08000000000',
        ]);

        // Pastor
        User::create([
            'name' => 'Senior Pastor',
            'email' => 'pastor@goalsparish.org',
            'password' => Hash::make('password'),
            'role' => UserRole::PASTOR,
            'status' => UserStatus::ACTIVE,
        ]);

        // Editor
        User::create([
            'name' => 'Content Editor',
            'email' => 'editor@goalsparish.org',
            'password' => Hash::make('password'),
            'role' => UserRole::EDITOR,
            'status' => UserStatus::ACTIVE,
        ]);

        // Media
        User::create([
            'name' => 'Media Manager',
            'email' => 'media@goalsparish.org',
            'password' => Hash::make('password'),
            'role' => UserRole::MEDIA,
            'status' => UserStatus::ACTIVE,
        ]);

        // Generate extra users for testing
        User::factory(10)->create();
    }
}
