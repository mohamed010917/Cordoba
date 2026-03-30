<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    public function update(User $user, Room $room): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (! $user->hasRole('manager')) {
            return false;
        }

        return $room->floor()->where('manger_id', $user->id)->exists();
    }

    public function delete(User $user, Room $room): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (! $user->hasRole('manager')) {
            return false;
        }

        return $room->floor()->where('manger_id', $user->id)->exists();
    }
}
