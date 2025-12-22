<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ServiceFeeConfig extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'name',
        'period_year',
        'jasa_pelayanan_pct',
        'jasa_sarana_pct',
        'pct_medis',
        'pct_keperawatan',
        'pct_penunjang',
        'pct_manajemen',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'period_year' => 'integer',
        'jasa_pelayanan_pct' => 'decimal:2',
        'jasa_sarana_pct' => 'decimal:2',
        'pct_medis' => 'decimal:2',
        'pct_keperawatan' => 'decimal:2',
        'pct_penunjang' => 'decimal:2',
        'pct_manajemen' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the indexes for this configuration.
     */
    public function indexes()
    {
        return $this->hasMany(ServiceFeeIndex::class);
    }

    /**
     * Get the calculations using this configuration.
     */
    public function calculations()
    {
        return $this->hasMany(ServiceFeeCalculation::class);
    }

    /**
     * Get the user who created this config.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this config.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active configs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('period_year', $year);
    }

    /**
     * Get the total distribution percentage (should equal 100).
     */
    public function getTotalDistributionAttribute(): float
    {
        return $this->pct_medis + $this->pct_keperawatan + $this->pct_penunjang + $this->pct_manajemen;
    }

    /**
     * Check if distribution percentages are valid (sum to 100).
     */
    public function isDistributionValid(): bool
    {
        return abs($this->total_distribution - 100) < 0.01;
    }

    /**
     * Check if main ratio is valid (sum to 100).
     */
    public function isRatioValid(): bool
    {
        return abs(($this->jasa_pelayanan_pct + $this->jasa_sarana_pct) - 100) < 0.01;
    }

    /**
     * Get distribution as array.
     */
    public function getDistributionArray(): array
    {
        return [
            'medis' => $this->pct_medis,
            'keperawatan' => $this->pct_keperawatan,
            'penunjang' => $this->pct_penunjang,
            'manajemen' => $this->pct_manajemen,
        ];
    }
}
