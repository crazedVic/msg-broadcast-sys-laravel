<?php

namespace Database\Factories;

use App\Models\Broadcast;
use Illuminate\Database\Eloquent\Factories\Factory;

class BroadcastFactory extends Factory
{
    protected $model = Broadcast::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
        ];
    }
}