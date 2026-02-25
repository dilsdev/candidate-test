<?php

namespace Database\Factories;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CltLayerFactory extends Factory
{
    protected $model = CltLayer::class;

    public function definition(): array
    {
        return [
            'layup_id' => CltLayup::factory(),
            'layer_order' => fake()->numberBetween(1, 20),
            'thickness' => fake()->randomFloat(2, 5, 100),
            'width' => fake()->randomFloat(2, 50, 500),
            'angle' => fake()->randomElement([0, 90, 45, -45]),
        ];
    }
}
