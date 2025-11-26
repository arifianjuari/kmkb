<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ComplianceCalculator;

class CaseDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_case_id',
        'pathway_step_id',
        'service_item',
        'service_code',
        'status',
        'performed',
        'quantity',
        'actual_cost',
        'service_date',
        'hospital_id',
        'cost_reference_id',
        'unit_cost_applied',
        'tariff_applied',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'actual_cost' => 'decimal:2',
        'unit_cost_applied' => 'decimal:2',
        'tariff_applied' => 'decimal:2',
        'service_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient case that owns the detail.
     */
    public function patientCase()
    {
        return $this->belongsTo(PatientCase::class);
    }

    /**
     * Get the pathway step for the case detail.
     */
    public function pathwayStep()
    {
        return $this->belongsTo(PathwayStep::class);
    }

    /**
     * Get the hospital for the case detail.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Check if this case detail is a custom (non-standard) step.
     *
     * @return bool
     */
    public function isCustomStep()
    {
        return $this->pathway_step_id === null;
    }

    /**
     * Recalculate compliance on create/update/delete of case details.
     */
    protected static function booted(): void
    {
        $recalc = function (CaseDetail $detail) {
            $case = $detail->patientCase()
                ->with(['clinicalPathway.steps', 'caseDetails.hospital'])
                ->first();
            if ($case) {
                $calculator = new ComplianceCalculator();
                $case->compliance_percentage = $calculator->computeCompliance($case);
                $case->save();
            }
        };

        static::created($recalc);
        static::updated($recalc);
        static::deleted($recalc);
    }
}
