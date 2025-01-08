<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'prompt' => fake()->text(10),
            'id_user' => User::factory(),
        ];
    }

    public function withUser(User $user): self
    {
        return $this->state(function () use ($user) {
            return [
                'id_user' => $user->id,
            ];
        });
    }
}
