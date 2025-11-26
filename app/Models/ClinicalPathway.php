<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ClinicalPathway extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'diagnosis_code',
        'version',
        'effective_date',
        'created_by',
        'status',
        'hospital_id',
        'unit_cost_version',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Allow superadmin to access any clinical pathway
        if (auth()->check() && auth()->user()->isSuperadmin()) {
            return $this->where('id', $value)->first();
        }

        // Get the hospital ID from session or authenticated user
        $hospitalId = session('hospital_id', auth()->user()->hospital_id ?? null);

        if (!$hospitalId) {
            // If no hospital context, return null (will result in 404)
            return null;
        }

        // Find the model scoped to the current hospital
        return $this->where('id', $value)
                    ->where('hospital_id', $hospitalId)
                    ->first();
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'effective_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the pathway steps for the clinical pathway.
     */
    public function steps()
    {
        return $this->hasMany(PathwayStep::class);
    }

    /**
     * Get the patient cases for the clinical pathway.
     */
    public function patientCases()
    {
        return $this->hasMany(PatientCase::class);
    }

    /**
     * Get the user that created the pathway.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
