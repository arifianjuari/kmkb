<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class UnitCostCalculation extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'period_year',
        'period_month',
        'cost_reference_id',
        'direct_cost_material',
        'direct_cost_labor',
        'indirect_cost_overhead',
        'total_unit_cost',
        'version_label',
        'rvu_value',
        'rvu_weighted_volume',
        'unit_cost_with_rvu',
        'rvu_value_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'direct_cost_material' => 'decimal:2',
        'direct_cost_labor' => 'decimal:2',
        'indirect_cost_overhead' => 'decimal:2',
        'total_unit_cost' => 'decimal:2',
        'rvu_value' => 'decimal:4',
        'rvu_weighted_volume' => 'decimal:4',
        'unit_cost_with_rvu' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cost reference for this unit cost calculation.
     */
    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    /**
     * Get the RVU value used in this calculation.
     */
    public function rvuValue()
    {
        return $this->belongsTo(RvuValue::class, 'rvu_value_id');
    }

    /**
     * Get the hospital for this unit cost calculation.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Scope a query to filter by version label.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $version
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVersion($query, $version)
    {
        return $query->where('version_label', $version);
    }

    /**
     * Scope a query to filter by cost reference.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $costReferenceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCostReference($query, $costReferenceId)
    {
        return $query->where('cost_reference_id', $costReferenceId);
    }

    /**
     * Scope a query to get the latest version for a cost reference.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $costReferenceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestForCostReference($query, $costReferenceId)
    {
        return $query->where('cost_reference_id', $costReferenceId)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter by period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @param  int|null  $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPeriod($query, $year, $month = null)
    {
        $query->where('period_year', $year);
        if ($month !== null) {
            $query->where('period_month', $month);
        }
        return $query;
    }
}
