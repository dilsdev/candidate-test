<?php

namespace Database\Factories;

use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CltLayupFactory extends Factory
{
    protected $model = CltLayup::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => fake()->word() . ' Layup ' . fake()->unique()->numberBetween(1, 9999),
        ];
    }
}
