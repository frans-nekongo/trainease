<?php

namespace Database\Factories\Organisation;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation\Division>
 */
class DivisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'division_name' => $this->faker->unique()->word(),
            'organisation_id' => \App\Models\Organisation\Organisation::factory(),
        ];
    }
}
