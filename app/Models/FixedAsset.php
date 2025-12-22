<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class FixedAsset extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'asset_category_id',
        'cost_center_id',
        'asset_code',
        'name',
        'description',
        'brand',
        'model',
        'serial_number',
        'manufacturer',
        'location',
        'condition',
        'acquisition_date',
        'acquisition_cost',
        'useful_life_years',
        'salvage_value',
        'warranty_end_date',
        'last_maintenance_date',
        'next_maintenance_date',
        'calibration_due_date',
        'status',
        'disposal_date',
        'disposal_reason',
        'disposal_value',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'useful_life_years' => 'integer',
        'salvage_value' => 'decimal:2',
        'warranty_end_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'calibration_due_date' => 'date',
        'disposal_date' => 'date',
        'disposal_value' => 'decimal:2',
    ];

    /**
     * Status constants
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_SOLD = 'sold';
    public const STATUS_IN_REPAIR = 'in_repair';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_DISPOSED => 'Dihapuskan',
            self::STATUS_SOLD => 'Dijual',
            self::STATUS_IN_REPAIR => 'Dalam Perbaikan',
        ];
    }

    /**
     * Condition constants
     */
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';
    public const CONDITION_DAMAGED = 'damaged';

    public static function getConditionOptions(): array
    {
        return [
            self::CONDITION_GOOD => 'Baik',
            self::CONDITION_FAIR => 'Cukup',
            self::CONDITION_POOR => 'Kurang',
            self::CONDITION_DAMAGED => 'Rusak',
        ];
    }

    /**
     * Get the asset category.
     */
    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    /**
     * Get the cost center.
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the depreciation records.
     */
    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    /**
     * Scope to only active assets.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Calculate monthly depreciation using straight-line method.
     */
    public function getMonthlyDepreciationAttribute(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }
        
        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;
        $annualDepreciation = $depreciableAmount / $this->useful_life_years;
        
        return round($annualDepreciation / 12, 2);
    }

    /**
     * Calculate annual depreciation.
     */
    public function getAnnualDepreciationAttribute(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }
        
        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;
        return round($depreciableAmount / $this->useful_life_years, 2);
    }

    /**
     * Get accumulated depreciation up to a specific date.
     */
    public function getAccumulatedDepreciation(?\DateTime $upToDate = null): float
    {
        $query = $this->depreciations();
        
        if ($upToDate) {
            $query->where(function ($q) use ($upToDate) {
                $q->where('period_year', '<', $upToDate->format('Y'))
                  ->orWhere(function ($q2) use ($upToDate) {
                      $q2->where('period_year', $upToDate->format('Y'))
                         ->where('period_month', '<=', $upToDate->format('n'));
                  });
            });
        }
        
        return (float) $query->sum('depreciation_amount');
    }

    /**
     * Get current book value.
     */
    public function getCurrentBookValueAttribute(): float
    {
        $accumulated = $this->getAccumulatedDepreciation();
        return max(0, $this->acquisition_cost - $accumulated);
    }

    /**
     * Check if depreciation is fully completed.
     */
    public function getIsFullyDepreciatedAttribute(): bool
    {
        return $this->current_book_value <= $this->salvage_value;
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get condition display name.
     */
    public function getConditionDisplayAttribute(): string
    {
        return self::getConditionOptions()[$this->condition] ?? $this->condition;
    }
}
