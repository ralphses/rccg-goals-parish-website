<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        Department::create([
            'name' => "Administration",
            'description' => "Responsible for overall administration of the parish.",
            'leader_id' => null,
            'status' => UserStatus::ACTIVE,
        ]);

        Department::create([
            'name' => "Media",
            'description' => "Handles all media-related activities including photography, videography, and social media management.",
            'leader_id' => null,
            'status' => UserStatus::ACTIVE,
        ]);

         Department::create([
            'name' => "Content",
            'description' => "Manages all content creation and editing for the parish website.",
            'leader_id' => null,
            'status' => UserStatus::ACTIVE,
        ]);

         Department::create([
            'name' => "Events",
            'description' => "Organizes and manages all events for the parish.",
            'leader_id' => null,
            'status' => UserStatus::ACTIVE,
        ]);

         Department::create([
            'name' => "Testimonies",
            'description' => "Collects and manages testimonies from parish members.",
            'leader_id' => null,
            'status' => UserStatus::ACTIVE,
        ]);

         Department::create([
            'name' => "Analytics",
            'description' => "Monitors and analyzes website traffic and user behavior.",
            'leader_id' => null,
            'status' => UserStatus::ACTIVE,
        ]);

        // Generate extra users for testing
        Department::factory(10)->create();
    }
}
