<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'table_id' => Table::factory(),
            'order_no' => 'ORD-' . $this->faker->unique()->bothify('?????'),
            'status' => 'pending',
            'subtotal' => 100,
            'tax' => 0,
            'service_charge' => 0,
            'total' => 100,
        ];
    }
}