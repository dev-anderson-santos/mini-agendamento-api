<?php

namespace Database\Factories;

use App\Enums\ScheduleStatus;
use App\Models\Hour;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'hour_id' => Hour::factory(),
            'date' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'user_id' => User::factory(),
            'status' => ScheduleStatus::SCHEDULED,
            'cancelled_at' => null,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduleStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);
    }
}
