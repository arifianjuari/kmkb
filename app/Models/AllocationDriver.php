<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class AllocationDriver extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'name',
        'unit_measurement',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the driver statistics for this allocation driver.
     */
    public function driverStatistics()
    {
        return $this->hasMany(DriverStatistic::class);
    }

    /**
     * Get the allocation maps for this allocation driver.
     */
    public function allocationMaps()
    {
        return $this->hasMany(AllocationMap::class);
    }
}






