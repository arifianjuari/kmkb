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
}
