<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class DriverStatistic extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'period_month',
        'period_year',
        'cost_center_id',
        'allocation_driver_id',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function allocationDriver()
    {
        return $this->belongsTo(AllocationDriver::class);
    }
}








