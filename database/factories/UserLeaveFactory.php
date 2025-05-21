<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLeave>
 */
class UserLeaveFactory extends Factory
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
            'leave_date_start' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'leave_date_end' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
            'desc_leave' => $this->faker->sentence()
        ];
    }
}
