<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ServiceFeeAssignment extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'cost_reference_id',
        'service_fee_index_id',
        'participation_pct',
        'headcount',
        'duration_minutes',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'participation_pct' => 'decimal:2',
        'headcount' => 'integer',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cost reference (service/tindakan).
     */
    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    /**
     * Get the service fee index.
     */
    public function serviceFeeIndex()
    {
        return $this->belongsTo(ServiceFeeIndex::class);
    }

    /**
     * Scope for active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by cost reference.
     */
    public function scopeForCostReference($query, int $costReferenceId)
    {
        return $query->where('cost_reference_id', $costReferenceId);
    }

    /**
     * Get the effective index points (considering participation and headcount).
     */
    public function getEffectivePointsAttribute(): float
    {
        if (!$this->serviceFeeIndex) {
            return 0;
        }
        
        return round(
            $this->serviceFeeIndex->final_index * ($this->participation_pct / 100) * $this->headcount,
            4
        );
    }

    /**
     * Get a summary label for display.
     */
    public function getSummaryLabelAttribute(): string
    {
        $index = $this->serviceFeeIndex;
        if (!$index) {
            return '-';
        }
        
        $label = $index->role_label;
        if ($this->headcount > 1) {
            $label .= " (×{$this->headcount})";
        }
        
        return $label;
    }
}
