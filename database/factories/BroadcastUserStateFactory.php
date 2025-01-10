<?php

namespace Database\Factories;

use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BroadcastUserState>
 */
class BroadcastUserStateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BroadcastUserState::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'broadcast_id' => Broadcast::factory(),
            'user_id' => User::factory(),
            'read_at' => null,
        ];
    }
}