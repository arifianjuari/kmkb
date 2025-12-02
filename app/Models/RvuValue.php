<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class RvuValue extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * Professionalism factor constants
     */
    const PROFESSIONALISM_PERAWAT = 1;
    const PROFESSIONALISM_NURSE_BIDAN = 2;
    const PROFESSIONALISM_DOKTER_UMUM = 3;
    const PROFESSIONALISM_DOKTER_SPESIALIS = 4;
    const PROFESSIONALISM_DOKTER_SUBSPESIALIS = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'cost_reference_id',
        'cost_center_id',
        'period_year',
        'period_month',
        'time_factor',
        'professionalism_factor',
        'difficulty_factor',
        'normalization_factor',
        'rvu_value',
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
        'period_year' => 'integer',
        'period_month' => 'integer',
        'time_factor' => 'integer',
        'professionalism_factor' => 'integer',
        'difficulty_factor' => 'integer',
        'normalization_factor' => 'decimal:4',
        'rvu_value' => 'decimal:4',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($rvuValue) {
            // Auto-calculate RVU value before saving
            if (!$rvuValue->rvu_value || $rvuValue->isDirty(['time_factor', 'professionalism_factor', 'difficulty_factor', 'normalization_factor'])) {
                $rvuValue->rvu_value = $rvuValue->calculateRvuValue();
            }
        });
    }

    /**
     * Get the cost reference for this RVU value.
     */
    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    /**
     * Get the cost center for this RVU value.
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the user who created this RVU value.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this RVU value.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get professionalism label attribute.
     *
     * @return string
     */
    public function getProfessionalismLabelAttribute()
    {
        return match($this->professionalism_factor) {
            self::PROFESSIONALISM_PERAWAT => 'Perawat',
            self::PROFESSIONALISM_NURSE_BIDAN => 'Nurse/Bidan',
            self::PROFESSIONALISM_DOKTER_UMUM => 'Dokter Umum',
            self::PROFESSIONALISM_DOKTER_SPESIALIS => 'Dokter Spesialis',
            self::PROFESSIONALISM_DOKTER_SUBSPESIALIS => 'Dokter Subspesialis',
            default => 'Unknown',
        };
    }

    /**
     * Get all professionalism options.
     *
     * @return array
     */
    public static function getProfessionalismOptions()
    {
        return [
            self::PROFESSIONALISM_PERAWAT => 'Perawat',
            self::PROFESSIONALISM_NURSE_BIDAN => 'Nurse/Bidan',
            self::PROFESSIONALISM_DOKTER_UMUM => 'Dokter Umum',
            self::PROFESSIONALISM_DOKTER_SPESIALIS => 'Dokter Spesialis',
            self::PROFESSIONALISM_DOKTER_SUBSPESIALIS => 'Dokter Subspesialis',
        ];
    }

    /**
     * Calculate RVU value from factors.
     *
     * @return float
     */
    public function calculateRvuValue()
    {
        $normalization = $this->normalization_factor ?? 1.0;
        if ($normalization == 0) {
            $normalization = 1.0;
        }
        
        return round(($this->time_factor * $this->professionalism_factor * $this->difficulty_factor) / $normalization, 4);
    }

    /**
     * Scope a query to filter by period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $year
     * @param  int|null  $month
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPeriod($query, $year, $month = null)
    {
        $query->where('period_year', $year);
        if ($month !== null) {
            $query->where('period_month', $month);
        } else {
            $query->whereNull('period_month');
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
     * Scope a query to only include active RVU values.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to get the latest RVU for a cost reference.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $costReferenceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestForCostReference($query, $costReferenceId)
    {
        return $query->where('cost_reference_id', $costReferenceId)
            ->where('is_active', true)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc');
    }
}
