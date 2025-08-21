<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientCase extends Model
{
    use HasFactory;

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
        'compliance_percentage',
        'cost_variance',
        'input_by',
        'input_date',
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
