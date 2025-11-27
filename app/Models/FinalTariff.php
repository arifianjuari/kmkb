<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class FinalTariff extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'cost_reference_id',
        'tariff_class_id',
        'unit_cost_calculation_id',
        'sk_number',
        'base_unit_cost',
        'margin_percentage',
        'jasa_sarana',
        'jasa_pelayanan',
        'final_tariff_price',
        'effective_date',
        'expired_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_unit_cost' => 'decimal:2',
        'margin_percentage' => 'decimal:4',
        'jasa_sarana' => 'decimal:2',
        'jasa_pelayanan' => 'decimal:2',
        'final_tariff_price' => 'decimal:2',
        'effective_date' => 'date',
        'expired_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cost reference for this final tariff.
     */
    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    /**
     * Get the tariff class for this final tariff.
     */
    public function tariffClass()
    {
        return $this->belongsTo(TariffClass::class);
    }

    /**
     * Get the unit cost calculation for this final tariff.
     */
    public function unitCostCalculation()
    {
        return $this->belongsTo(UnitCostCalculation::class);
    }

    /**
     * Get the hospital for this final tariff.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Scope a query to filter by active tariffs (not expired).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $date = null)
    {
        $date = $date ?? now();
        return $query->where('effective_date', '<=', $date)
            ->where(function($q) use ($date) {
                $q->whereNull('expired_date')
                  ->orWhere('expired_date', '>=', $date);
            });
    }

    /**
     * Scope a query to filter by tariff class.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|null  $tariffClassId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTariffClass($query, $tariffClassId)
    {
        if ($tariffClassId) {
            return $query->where('tariff_class_id', $tariffClassId);
        }
        return $query;
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
     * Check if this tariff is currently active.
     *
     * @param  string|null  $date
     * @return bool
     */
    public function isActive($date = null)
    {
        $date = $date ?? now();
        return $this->effective_date <= $date && 
               ($this->expired_date === null || $this->expired_date >= $date);
    }
}

