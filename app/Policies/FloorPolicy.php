<?php

namespace App\Policies;

use App\Models\Floor;
use App\Models\User;

class FloorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function update(User $user, Floor $floor): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('manager') && $floor->manger_id === $user->id;
    }

    public function delete(User $user, Floor $floor): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('manager') && $floor->manger_id === $user->id;
    }
}