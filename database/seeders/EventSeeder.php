<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mediaDept = \App\Models\Department::where('name', 'Media')->first();

        Event::create([
            'title' => 'Annual Parish Picnic',
            'event_date' => now()->addDays(30),
            'location' => 'Central Park',
            'department_id' => $mediaDept->id,
            'status' => \App\Enums\EventStatus::UPCOMING,
        ]);

        Event::create([
            'title' => 'Youth Retreat',
            'event_date' => now()->addDays(60),
            'location' => 'Mountain Resort',
            'department_id' => $mediaDept->id,
            'status' => \App\Enums\EventStatus::UPCOMING,
        ]);

        Event::create([
            'title' => 'Community Service Day',
            'event_date' => now()->addDays(90),
            'location' => 'Local Shelter',
            'department_id' => $mediaDept->id,
            'status' => \App\Enums\EventStatus::UPCOMING,
        ]);

          Event::factory(10)->create();
    }
}
