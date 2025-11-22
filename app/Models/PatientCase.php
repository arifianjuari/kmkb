<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class PatientCase extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * Get the patient name.
     * 
     * @return string
     */
    public function getPatientNameAttribute()
    {
        // For now, return the patient_id since we don't have a separate patient name field
        // In a real application, this would be retrieved from a patient management system
        return $this->patient_id;
    }

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
        // Allow superadmin to access any patient case
        if (auth()->check() && auth()->user()->hasRole('superadmin')) {
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'medical_record_number',
        'clinical_pathway_id',
        'admission_date',
        'discharge_date',
        'primary_diagnosis',
        'ina_cbg_code',
        'actual_total_cost',
        'ina_cbg_tariff',
        'additional_diagnoses',
        'compliance_percentage',
        'cost_variance',
        'input_by',
        'input_date',
        'hospital_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admission_date' => 'date',
        'discharge_date' => 'date',
        'actual_total_cost' => 'decimal:2',
        'ina_cbg_tariff' => 'decimal:2',
        'compliance_percentage' => 'decimal:2',
        'cost_variance' => 'decimal:2',
        'input_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the clinical pathway for the patient case.
     */
    public function clinicalPathway()
    {
        return $this->belongsTo(ClinicalPathway::class);
    }

    /**
     * Get the user who input the case.
     */
    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    /**
     * Get the case details for the patient case.
     */
    public function caseDetails()
    {
        return $this->hasMany(CaseDetail::class);
    }
}
