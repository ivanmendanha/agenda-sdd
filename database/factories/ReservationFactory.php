<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startMinutes = fake()->numberBetween(8 * 60, (18 * 60) - 1);
        $latestEnd = min($startMinutes + (4 * 60), 18 * 60);
        $endMinutes = fake()->numberBetween($startMinutes + 1, $latestEnd);

        return [
            'room_id' => Room::factory(),
            'date' => fake()->date(),
            'starts_at' => $this->formatTime($startMinutes),
            'ends_at' => $this->formatTime($endMinutes),
            'subject' => fake()->sentence(),
            'status' => 'confirmed',
        ];
    }

    /**
     * Indicate that the reservation is not confirmed.
     */
    public function unconfirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unconfirmed',
        ]);
    }

    private function formatTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
