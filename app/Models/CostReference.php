<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostReference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_code',
        'service_description',
        'standard_cost',
        'purchase_price',
        'selling_price_unit',
        'selling_price_total',
        'unit',
        'source',
        'hospital_id',
        'simrs_kode_brng',
        'is_synced_from_simrs',
        'last_synced_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'standard_cost' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'selling_price_unit' => 'decimal:2',
        'selling_price_total' => 'decimal:2',
        'is_synced_from_simrs' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the pathway steps that use this cost reference.
     */
    public function pathwaySteps()
    {
        return $this->hasMany(PathwayStep::class);
    }
}
