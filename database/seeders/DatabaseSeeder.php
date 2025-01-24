<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\AgentFactory;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
        $users = User::factory(5)->create();
        $agentsFromRndUser = AgentFactory::new()->count(2)->withUser($users[0])->create();
        DocumentFactory::new()->count(5)->withAgent($agentsFromRndUser[0])->withUser($users[0])->withEmbedding()->create();

        $mainUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $mainAgents = AgentFactory::new()->count(2)->withUser($mainUser)->create();
        DocumentFactory::new()->count(5)->withAgent($mainAgents[0])->withUser($mainUser)->withEmbedding()->create();
    }
}
