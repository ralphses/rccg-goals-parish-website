<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\enums\AnnouncementFrequency;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'service_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'frequency' => $this->faker->randomElement(AnnouncementFrequency::cases()),
            'is_active' => $this->faker->boolean,
            'is_approved' => $this->faker->boolean,
            'last_announced_at' => null,
        ];
    }
}
