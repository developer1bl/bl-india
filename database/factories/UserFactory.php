<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Admin User', //$this->faker->name(),
            'email' => 'Admin@blindia.com',  //$this->faker->unique()->safeEmail(),
            'phone' => 987654321,
            'email_verified_at' => now(),
            'password' => 12345678,//'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'is_online' => false,
            'is_active' => true,
            'is_approved' => true,
            'is_email_verified' => true,
            'role_id' => 1
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Attach roles to the user
            $user->roles()->attach(1);
        });
    }
}
