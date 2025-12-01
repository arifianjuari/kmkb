<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class AllocationResult extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'period_month',
        'period_year',
        'source_cost_center_id',
        'target_cost_center_id',
        'allocation_step',
        'allocated_amount',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sourceCostCenter()
    {
        return $this->belongsTo(CostCenter::class, 'source_cost_center_id');
    }

    public function targetCostCenter()
    {
        return $this->belongsTo(CostCenter::class, 'target_cost_center_id');
    }
}







