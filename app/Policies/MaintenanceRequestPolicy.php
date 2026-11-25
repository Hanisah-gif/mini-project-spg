<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    public function view(User $user, MaintenanceRequest $request): bool
    {
        return $user->isAdmin()
            || $request->user_id === $user->id
            || ($user->isTechnician() && $request->technician_id === $user->id);
    }

    public function update(User $user, MaintenanceRequest $request): bool
    {
        return $user->isAdmin()
            || (
                $request->user_id === $user->id
                && $request->status === 'pending'
                && $request->technician_id === null
            )
            || (
                $user->isTechnician()
                && $request->technician_id === $user->id
            );
    }

    public function delete(User $user, MaintenanceRequest $request): bool
    {
        return $user->isAdmin()
            || (
                $request->user_id === $user->id
                && $request->status === 'pending'
                && $request->technician_id === null
            );
    }
}
