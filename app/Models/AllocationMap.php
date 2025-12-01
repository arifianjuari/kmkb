<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class AllocationMap extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'source_cost_center_id',
        'allocation_driver_id',
        'step_sequence',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sourceCostCenter()
    {
        return $this->belongsTo(CostCenter::class, 'source_cost_center_id');
    }

    public function allocationDriver()
    {
        return $this->belongsTo(AllocationDriver::class);
    }
}







