<?php

namespace Database\Factories;

use App\enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'can_login' => false, // default to false for generated users
            'role' => UserRole::MEMBER,
            'phone' => fake()->phoneNumber(),
            'avatar' => null,
            'status' => UserStatus::ACTIVE,
            'last_login_at' => now(),
            'address' => fake()->address(),
            'day_joined' => fake()->date(),
            'what_attracted_you' => fake()->sentence(),
            'state_of_origin' => fake()->state(),
            'occupation' => fake()->jobTitle(),
            'hobbies' => fake()->sentence(),
            'favourite_quote' => fake()->sentence(),
            'birthday' => fake()->date(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
