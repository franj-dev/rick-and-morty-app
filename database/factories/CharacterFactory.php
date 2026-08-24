<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name' => $this->faker->name(),
            'status' => 'Alive',
            'species' => 'Human',
            'type' => '',
            'gender' => 'Male',
            'image' => $this->faker->imageUrl(),
        ];
    }
}