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
        'display_order',
        'category',
        'description',
        'service_code',
        'criteria',
        'estimated_cost',
        'quantity',
        'cost_reference_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'estimated_cost' => 'decimal:0',
        'quantity' => 'integer',
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

    /**
     * Determine if this step is conditional (has criteria defined).
     */
    public function isConditional(): bool
    {
        $c = is_string($this->criteria) ? trim($this->criteria) : '';
        return $c !== '';
    }

    /**
     * Computed total cost (not persisted): estimated_cost x quantity
     */
    public function getTotalCostAttribute(): string
    {
        $qty = (float) ($this->quantity ?? 1);
        $unit = (float) ($this->estimated_cost ?? 0);
        $total = $qty * $unit;
        // format to 0 decimals (integer string)
        return number_format($total, 0, '.', '');
    }
}
