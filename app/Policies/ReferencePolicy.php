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
        return $this->hasPermission($user, 'view-references');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reference $reference): bool
    {
        if (!$this->hasPermission($user, 'view-references')) {
            return false;
        }

        // Management auditor can view all references in their hospital
        if ($user->isObserver()) {
            return $this->belongsToSameHospital($user, $reference);
        }
        
        return $user->isSuperadmin() || $this->belongsToSameHospital($user, $reference);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Management auditor is read-only
        if ($user->isObserver()) {
            return false;
        }
        return $this->hasPermission($user, 'create-references');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reference $reference): bool
    {
        // Management auditor is read-only
        if ($user->isObserver()) {
            return false;
        }
        return $this->hasPermission($user, 'update-references') 
            && $this->belongsToSameHospital($user, $reference);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reference $reference): bool
    {
        // Management auditor is read-only
        if ($user->isObserver()) {
            return false;
        }
        return $this->hasPermission($user, 'delete-references') 
            && $this->belongsToSameHospital($user, $reference);
    }

    /**
     * Determine whether the user can pin/unpin the model.
     */
    public function pin(User $user, Reference $reference): bool
    {
        return $this->update($user, $reference);
    }
}

