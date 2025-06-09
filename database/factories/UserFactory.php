<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'username' => fake()->name(),
            'password' => static::$password ??= Hash::make('password'),
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'phone' => substr(preg_replace('/\D/', '', fake()->phoneNumber()), 0, 15),
            'is_admin' => 0
        ];
    }
}
