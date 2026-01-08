<?php

namespace Database\Factories\Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Products\Infrastructure\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'parent_id' => $this->faker->randomElement([
                Category::factory(),
                null,
            ]),
            'description' => $this->faker->sentence,
        ];
    }
}
