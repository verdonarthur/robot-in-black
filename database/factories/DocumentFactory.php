<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->text(10),
            'content' => 'test',
            'id_user' => User::factory(),
            'id_agent' => Agent::factory(),
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

    public function withAgent(Agent $agent): self
    {
        return $this->state(function () use ($agent) {
            return [
                'id_agent' => $agent->id,
            ];
        });
    }

    public function withEmbedding(): self
    {
        return $this->afterCreating(function (Document $document) {
            $embedding = collect(range(0, 767))->toJson();
            $document->embeddings()->create(['embedding' => $embedding]);
        });
    }
}
