<?php

namespace Database\Factories\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\User;
use Modules\Orders\Infrastructure\Models\Order;
use Modules\Products\Infrastructure\Models\Product;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'vat' => 23.0,
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),
            'user_id' => $this->faker->randomElement([
                User::factory(),
                null,
            ]),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::factory()->count(3)->create();

            $pivotData = [];

            foreach ($products as $product) {
                $pivotData[$product->id] = [
                    'quantity' => rand(1, 5),
                    'unit_price' => $product->price,
                ];
            }

            $order->products()->attach($pivotData);
        });
    }
}
