<?php

namespace Database\Factories;

use App\enums\EventStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'event_date' => fake()->dateTime(),
            'location' => fake()->address(),
            'department_id' => fake()->randomElement([1, 2, 3, 4, 5]), // Adjust based on your departments
            'status' => fake()->randomElement([EventStatus::CANCELLED->value, EventStatus::COMPLETED->value, EventStatus::ONGOING->value, EventStatus::UPCOMING->value]),
            'image' => fake()->imageUrl(),
            'image_media_id' => null,
            'video_link' => fake()->url(),
            'video_media_id' => null,
            'description_heading' => fake()->sentence(),
        ];
    }
}
