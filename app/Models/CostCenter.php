<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class CostCenter extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'code',
        'name',
        'type',
        'parent_id',
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
     * Get the parent cost center.
     */
    public function parent()
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    /**
     * Get the child cost centers.
     */
    public function children()
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    /**
     * Get the cost references for this cost center.
     */
    public function costReferences()
    {
        return $this->hasMany(CostReference::class);
    }

    /**
     * Get the GL expenses for this cost center.
     */
    public function glExpenses()
    {
        return $this->hasMany(GlExpense::class);
    }

    /**
     * Get the driver statistics for this cost center.
     */
    public function driverStatistics()
    {
        return $this->hasMany(DriverStatistic::class);
    }

    /**
     * Get allocation maps where this is the source.
     */
    public function allocationMapsAsSource()
    {
        return $this->hasMany(AllocationMap::class, 'source_cost_center_id');
    }

    /**
     * Get allocation results where this is the source.
     */
    public function allocationResultsAsSource()
    {
        return $this->hasMany(AllocationResult::class, 'source_cost_center_id');
    }

    /**
     * Get allocation results where this is the target.
     */
    public function allocationResultsAsTarget()
    {
        return $this->hasMany(AllocationResult::class, 'target_cost_center_id');
    }
}


