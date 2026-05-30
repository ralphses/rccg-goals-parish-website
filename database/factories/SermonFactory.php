<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sermon>
 */
class SermonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'message' => $this->faker->paragraphs(3, true),
            'sermon_date' => $this->faker->date,
            'duration' => $this->faker->numberBetween(30, 60) . ' minutes',
            'speaker_id' => User::factory(),
            'cover_image' => $this->faker->imageUrl,
            'audio_url' => $this->faker->url,
            'video_url' => $this->faker->url,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'published_at' => $this->faker->dateTimeThisMonth,
        ];
    }
}