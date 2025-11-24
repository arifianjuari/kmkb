<?php

namespace App\Policies;

use App\Models\Reference;
use App\Models\User;

class ReferencePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reference $reference): bool
    {
        return $user->isSuperadmin() || $this->belongsToSameHospital($user, $reference);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reference $reference): bool
    {
        return $this->canManage($user) && $this->belongsToSameHospital($user, $reference);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reference $reference): bool
    {
        return $this->canManage($user) && $this->belongsToSameHospital($user, $reference);
    }

    /**
     * Determine whether the user can pin/unpin the model.
     */
    public function pin(User $user, Reference $reference): bool
    {
        return $this->update($user, $reference);
    }

    protected function canManage(User $user): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_MUTU,
            User::ROLE_KLAIM,
            User::ROLE_MANAJEMEN,
        ], true);
    }
}

