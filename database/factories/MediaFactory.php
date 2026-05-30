<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use App\Enums\MediaCategory;
use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class MediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Ensure the target directory exists
        if (!Storage::disk('public')->exists('media')) {
            Storage::disk('public')->makeDirectory('media');
        }

        $category = $this->faker->randomElement(MediaCategory::cases());
        $imagePath = $this->faker->image(storage_path('app/public/media'), 640, 480, null, false);

        return [
            'title' => $this->faker->sentence,
            'file_name' => basename($imagePath),
            'file_path' => 'media/' . basename($imagePath),
            'size' => $this->faker->numberBetween(1000, 5000),
            'media_type' => $this->faker->randomElement(MediaType::cases()),
            'category' => $category,
            'is_public' => $this->faker->boolean,
        ];
    }
}