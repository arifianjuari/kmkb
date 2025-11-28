<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Check if the user belongs to the same hospital as the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function belongsToSameHospital($user, $model)
    {
        // If the model doesn't have a hospital_id, allow access
        if (!isset($model->hospital_id)) {
            return true;
        }
        
        // Check if the user's hospital_id matches the model's hospital_id
        return $user->hospital_id === $model->hospital_id;
    }

    /**
     * Check if user can view (read-only access).
     * Observer role has read-only access to all data in their hospital.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return bool
     */
    protected function canView($user, $model = null)
    {
        // Superadmin can view everything
        if ($user->isSuperadmin()) {
            return true;
        }

        // Observer can view data in their hospital
        if ($user->isObserver()) {
            if ($model === null) {
                return true; // Can view list
            }
            return $this->belongsToSameHospital($user, $model);
        }

        // Other roles need to be checked individually
        return false;
    }
}
