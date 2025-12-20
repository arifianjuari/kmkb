<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class HouseholdExpense extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'cost_center_id',
        'household_item_id',
        'period_month',
        'period_year',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cost center for this expense.
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the household item for this expense.
     */
    public function householdItem()
    {
        return $this->belongsTo(HouseholdItem::class);
    }

    /**
     * Scope to filter by period.
     */
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('period_month', $month)->where('period_year', $year);
    }

    /**
     * Scope to filter by cost center.
     */
    public function scopeForCostCenter($query, $costCenterId)
    {
        return $query->where('cost_center_id', $costCenterId);
    }
}
