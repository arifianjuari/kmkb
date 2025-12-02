<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostReference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_code',
        'service_description',
        'standard_cost',
        'purchase_price',
        'selling_price_unit',
        'selling_price_total',
        'unit',
        'source',
        'hospital_id',
        'simrs_kode_brng',
        'is_synced_from_simrs',
        'last_synced_at',
        'cost_center_id',
        'expense_category_id',
        'is_bundle',
        'active_from',
        'active_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'standard_cost' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'selling_price_unit' => 'decimal:2',
        'selling_price_total' => 'decimal:2',
        'is_synced_from_simrs' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the pathway steps that use this cost reference.
     */
    public function pathwaySteps()
    {
        return $this->hasMany(PathwayStep::class);
    }

    /**
     * Get the cost center for this cost reference.
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the expense category for this cost reference.
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the RVU values for this cost reference.
     */
    public function rvuValues()
    {
        return $this->hasMany(RvuValue::class);
    }

    /**
     * Get active RVU for a specific period.
     *
     * @param int $year
     * @param int|null $month
     * @return RvuValue|null
     */
    public function getActiveRvu($year, $month = null)
    {
        $query = $this->rvuValues()
            ->where('period_year', $year)
            ->where('is_active', true);
        
        if ($month !== null) {
            $query->where('period_month', $month);
        } else {
            $query->whereNull('period_month');
        }
        
        return $query->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the latest RVU value for this cost reference.
     *
     * @return RvuValue|null
     */
    public function getLatestRvu()
    {
        return $this->rvuValues()
            ->where('is_active', true)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
