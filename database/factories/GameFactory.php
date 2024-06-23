<?php

namespace Database\Factories;
use App\Models\Game;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'die1' => $this->faker->numberBetween(1, 6),
            'die2' => $this->faker->numberBetween(1, 6),
            'win' => $this->faker->boolean,
        ];
    }
}
