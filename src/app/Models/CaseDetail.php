<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'quantity',
        'actual_cost',
        'service_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'actual_cost' => 'decimal:2',
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
}
