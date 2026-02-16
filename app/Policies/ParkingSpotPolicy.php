<?php

namespace App\Policies;

use App\Models\ParkingSpot;
use App\Models\User;

class ParkingSpotPolicy
{
    public function view(User $user, ParkingSpot $parkingSpot): bool
    {
        return $parkingSpot->user_id === $user->id;
    }

    public function update(User $user, ParkingSpot $parkingSpot): bool
    {
        return $parkingSpot->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }
}