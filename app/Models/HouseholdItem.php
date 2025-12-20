<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class HouseholdItem extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'code',
        'name',
        'unit',
        'unit_of_measurement_id',
        'default_price',
        'is_active',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the unit of measurement for this item.
     */
    public function unitOfMeasurement()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }

    /**
     * Get the household expenses for this item.
     */
    public function householdExpenses()
    {
        return $this->hasMany(HouseholdExpense::class);
    }

    /**
     * Scope to only active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the display unit (from UoM or fallback to unit field).
     */
    public function getUnitDisplayAttribute()
    {
        if ($this->unitOfMeasurement) {
            return $this->unitOfMeasurement->display_name;
        }
        return $this->unit;
    }
}
