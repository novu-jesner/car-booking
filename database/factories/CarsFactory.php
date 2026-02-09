<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' ' . $this->faker->word,
            'image' => $this->faker->imageUrl(640, 480, 'transport', true),
            'license_plate' => $this->faker->unique()->bothify('??-###-??'),
            'brand' => $this->faker->company,
            'type' => $this->faker->randomElement(['SUV', 'Sedan', 'Truck', 'Coupe']),
            'seater' => $this->faker->numberBetween(2, 8),
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
            'remarks' => $this->faker->sentence(),
        ];
    }
}
