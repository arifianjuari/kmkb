<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class UnitOfMeasurement extends Model
{
    use HasFactory, BelongsToHospital;

    protected $table = 'units_of_measurement';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'code',
        'name',
        'symbol',
        'category',
        'context',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Category options for dropdown
     */
    public const CATEGORIES = [
        'area' => 'Area/Luas',
        'weight' => 'Weight/Berat',
        'count' => 'Count/Jumlah',
        'time' => 'Time/Waktu',
        'volume' => 'Volume',
        'service' => 'Service/Layanan',
        'other' => 'Other/Lainnya',
    ];

    /**
     * Context options for dropdown
     */
    public const CONTEXTS = [
        'allocation' => 'Allocation (Driver)',
        'service' => 'Service (Cost Reference)',
        'both' => 'Both (Keduanya)',
    ];

    /**
     * Get the allocation drivers that use this unit.
     */
    public function allocationDrivers()
    {
        return $this->hasMany(AllocationDriver::class);
    }

    /**
     * Get the cost references that use this unit.
     */
    public function costReferences()
    {
        return $this->hasMany(CostReference::class);
    }

    /**
     * Scope to filter only active units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter units for allocation drivers.
     */
    public function scopeForAllocation($query)
    {
        return $query->whereIn('context', ['allocation', 'both']);
    }

    /**
     * Scope to filter units for service/cost references.
     */
    public function scopeForService($query)
    {
        return $query->whereIn('context', ['service', 'both']);
    }

    /**
     * Get display name with symbol.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->symbol && $this->symbol !== $this->name) {
            return "{$this->name} ({$this->symbol})";
        }
        return $this->name;
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Get context label.
     */
    public function getContextLabelAttribute()
    {
        return self::CONTEXTS[$this->context] ?? $this->context;
    }
}
