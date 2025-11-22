<?php

namespace App\Models\Traits;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

trait BelongsToHospital
{
    /**
     * Boot the BelongsToHospital trait for a model.
     */
    public static function bootBelongsToHospital()
    {
        static::addGlobalScope('hospital', function (Builder $builder) {
            // Don't apply scope for superadmin users
            if (auth()->check() && auth()->user()->isSuperadmin()) {
                return;
            }
            
            // Only apply scope if we have an authenticated user and hospital context
            $table = $builder->getModel()->getTable();
            if (auth()->check() && session()->has('hospital_id')) {
                $builder->where($table . '.hospital_id', session('hospital_id'));
            } elseif (auth()->check() && auth()->user()->hospital_id) {
                $builder->where($table . '.hospital_id', auth()->user()->hospital_id);
            }
        });
    }

    /**
     * Get the hospital that owns this model.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
