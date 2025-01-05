<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Agent $agent): bool
    {
        return $user->id === $agent->id_user;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Agent $agent): bool
    {
        return $user->id === $agent->id_user;
    }

    public function delete(User $user, Agent $agent): bool
    {
        return $user->id === $agent->id_user;
    }

    public function restore(User $user, Agent $agent): bool
    {
        return $user->id === $agent->id_user;
    }

    public function forceDelete(User $user, Agent $agent): bool
    {
        return $user->id === $agent->id_user;
    }
}
