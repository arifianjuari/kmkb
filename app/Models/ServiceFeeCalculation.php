<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ServiceFeeCalculation extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'cost_reference_id',
        'service_fee_config_id',
        'period_year',
        'period_month',
        'total_index_points',
        'point_value',
        'calculated_fee',
        'breakdown',
        'calculation_method',
        'calculated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'total_index_points' => 'decimal:4',
        'point_value' => 'decimal:2',
        'calculated_fee' => 'decimal:2',
        'breakdown' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cost reference.
     */
    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    /**
     * Get the configuration used.
     */
    public function config()
    {
        return $this->belongsTo(ServiceFeeConfig::class, 'service_fee_config_id');
    }

    /**
     * Get the user who calculated this.
     */
    public function calculator()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    /**
     * Scope by period.
     */
    public function scopeByPeriod($query, int $year, ?int $month = null)
    {
        $query->where('period_year', $year);
        if ($month !== null) {
            $query->where('period_month', $month);
        }
        return $query;
    }

    /**
     * Scope by cost reference.
     */
    public function scopeForCostReference($query, int $costReferenceId)
    {
        return $query->where('cost_reference_id', $costReferenceId);
    }

    /**
     * Get period label.
     */
    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return ($months[$this->period_month] ?? '') . ' ' . $this->period_year;
    }

    /**
     * Get formatted calculated fee.
     */
    public function getFormattedFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->calculated_fee, 0, ',', '.');
    }

    /**
     * Get breakdown summary.
     */
    public function getBreakdownSummaryAttribute(): array
    {
        if (!$this->breakdown) {
            return [];
        }

        $summary = [];
        foreach ($this->breakdown as $item) {
            $role = $item['role'] ?? 'unknown';
            if (!isset($summary[$role])) {
                $summary[$role] = [
                    'role' => $role,
                    'role_label' => $item['role_label'] ?? $role,
                    'total_points' => 0,
                    'count' => 0,
                ];
            }
            $summary[$role]['total_points'] += $item['points'] ?? 0;
            $summary[$role]['count']++;
        }

        return array_values($summary);
    }
}
