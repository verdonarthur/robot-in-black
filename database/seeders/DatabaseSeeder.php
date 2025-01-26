<?php

namespace Database\Seeders;

use App\Enums\UserGroupEnum;
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
        $groups = User\Group::all();
        $userGroup = $groups->first(fn(User\Group $group) => $group->name === UserGroupEnum::USER);
        $adminGroup = $groups->first(fn(User\Group $group) => $group->name === UserGroupEnum::ADMIN);

        $users = User::factory(5)->create(['group_id' => $userGroup->id]);
        $agentsFromRndUser = AgentFactory::new()->count(2)->withUser($users[0])->create();
        DocumentFactory::new()->count(5)->withAgent($agentsFromRndUser[0])->withUser($users[0])->withEmbedding()->create();

        $adminUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'group_id' => $adminGroup->id,
        ]);
        $mainAgents = AgentFactory::new()->count(2)->withUser($adminUser)->create();
        DocumentFactory::new()->count(5)->withAgent($mainAgents[0])->withUser($adminUser)->withEmbedding()->create();
    }
}
