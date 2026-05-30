<?php

namespace Database\Factories;

use App\Models\Sermon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SermonAttachment>
 */
class SermonAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sermon_id' => Sermon::factory(),
            'file_path' => $this->faker->url,
            'file_type' => $this->faker->fileExtension,
            'file_name' => $this->faker->word . '.' . $this->faker->fileExtension,
        ];
    }
}