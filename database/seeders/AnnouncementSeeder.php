<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
    {
        $usersToNotify = User::whereIn('role', ['admin', 'editor'])->get();

        Announcement::factory(10)->create()->each(function ($announcement) use ($usersToNotify) {
            // ... media creation ...

            // Create notifications for each announcement
            foreach ($usersToNotify as $user) {
                AppNotification::factory()->create([
                    'user_id' => $user->id,
                    'title' => 'New Announcement Request',
                    'message' => 'A new announcement "' . $announcement->title . '" has been submitted for approval.',
                    'link' => route('dashboard.announcements.show', $announcement->id),
                ]);
            }
        });
    }
}