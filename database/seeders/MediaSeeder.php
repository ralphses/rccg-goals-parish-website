<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear the directory first to avoid accumulating old images
        // Be careful with this in production
        // Storage::disk('public')->deleteDirectory('media');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found, skipping media seeding.');
            return;
        }

        Media::factory()->count(20)->make()->each(function ($media) use ($users) {
            $media->mediable_id = $users->random()->id;
            $media->mediable_type = User::class;
            $media->save();
        });
    }
}