<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathwayStep extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinical_pathway_id',
        'step_order',
        'description',
        'action',
        'criteria',
        'estimated_duration',
        'estimated_cost',
        'cost_reference_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estimated_duration' => 'integer',
        'estimated_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the clinical pathway that owns the step.
     */
    public function clinicalPathway()
    {
        return $this->belongsTo(ClinicalPathway::class);
    }

    /**
     * Get the cost reference for the step.
     */
    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    /**
     * Get the case details for the pathway step.
     */
    public function caseDetails()
    {
        return $this->hasMany(CaseDetail::class);
    }
}
