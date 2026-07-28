<?php

namespace Database\Factories;

use App\enums\TestimonyAnnouncementType;
use App\Models\Testimony;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimony>
 */
class TestimonyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Testimony::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isApproved = $this->faker->boolean;
        $announced = $isApproved ? $this->faker->boolean : false;

        return [
            'testifier_name' => $this->faker->name,
            'testifier_phone' => $this->faker->phoneNumber,
            'testifier_email' => $this->faker->unique()->safeEmail,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'announce_in_service' => $this->faker->boolean,
            'announcement_type' => $this->faker->randomElement(TestimonyAnnouncementType::cases())->value,
            'is_featured' => $this->faker->boolean,
            'is_approved' => $isApproved,
            'approved_at' => $isApproved ? $this->faker->dateTimeThisMonth : null,
            'announced' => $announced,
            'announced_at' => $announced ? $this->faker->dateTimeThisMonth : null,
        ];
    }
}
