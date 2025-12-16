<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class StandardResourceUsage extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'service_id',
        'service_code',
        'service_name',
        'category',
        'bmhp_id',
        'quantity',
        'unit',
        'unit_of_measurement_id',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the hospital for this standard resource usage.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the service (tindakan/pemeriksaan) for this standard resource usage.
     */
    public function service()
    {
        return $this->belongsTo(CostReference::class, 'service_id');
    }

    /**
     * Get the BMHP for this standard resource usage.
     */
    public function bmhp()
    {
        return $this->belongsTo(CostReference::class, 'bmhp_id');
    }

    /**
     * Get the user who created this standard resource usage.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this standard resource usage.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the unit of measurement for this standard resource usage.
     */
    public function unitOfMeasurement()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }

    /**
     * Get the display unit (from UoM or legacy field).
     */
    public function getUnitDisplayAttribute()
    {
        if ($this->unitOfMeasurement) {
            return $this->unitOfMeasurement->symbol ?? $this->unitOfMeasurement->name;
        }
        return $this->unit;
    }

    /**
     * Calculate total cost of BMHP (quantity * harga BMHP).
     *
     * @return float
     */
    public function getTotalCost()
    {
        if (!$this->bmhp) {
            return 0;
        }

        $bmhpPrice = $this->bmhp->purchase_price ?? $this->bmhp->standard_cost ?? 0;
        return round($this->quantity * $bmhpPrice, 2);
    }

    /**
     * Scope a query to only include active standard resource usages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by service.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $serviceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope a query to filter by hospital.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $hospitalId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForHospital($query, $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }
}
