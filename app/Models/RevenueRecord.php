<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class RevenueRecord extends Model
{
    use HasFactory, BelongsToHospital;

    // Category constants
    const CATEGORY_RAWAT_JALAN = 'rawat_jalan';
    const CATEGORY_RAWAT_INAP = 'rawat_inap';
    const CATEGORY_IGD = 'igd';
    const CATEGORY_PENUNJANG = 'penunjang';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'revenue_source_id',
        'period_year',
        'period_month',
        'category',
        'gross_revenue',
        'net_revenue',
        'claim_count',
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'gross_revenue' => 'decimal:2',
        'net_revenue' => 'decimal:2',
        'claim_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the revenue source for this record.
     */
    public function revenueSource()
    {
        return $this->belongsTo(RevenueSource::class);
    }

    /**
     * Get the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
     * Scope by revenue source.
     */
    public function scopeBySource($query, int $sourceId)
    {
        return $query->where('revenue_source_id', $sourceId);
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_RAWAT_JALAN => 'Rawat Jalan',
            self::CATEGORY_RAWAT_INAP => 'Rawat Inap',
            self::CATEGORY_IGD => 'IGD',
            self::CATEGORY_PENUNJANG => 'Penunjang',
            default => $this->category ?? 'Semua',
        };
    }

    /**
     * Get all category options.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_RAWAT_JALAN => 'Rawat Jalan',
            self::CATEGORY_RAWAT_INAP => 'Rawat Inap',
            self::CATEGORY_IGD => 'IGD',
            self::CATEGORY_PENUNJANG => 'Penunjang',
        ];
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
}
