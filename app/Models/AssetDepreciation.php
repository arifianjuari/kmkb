<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixed_asset_id',
        'period_month',
        'period_year',
        'depreciation_amount',
        'accumulated_depreciation',
        'book_value',
        'is_synced_to_gl',
        'gl_expense_id',
        'notes',
    ];

    protected $casts = [
        'period_month' => 'integer',
        'period_year' => 'integer',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'is_synced_to_gl' => 'boolean',
    ];

    /**
     * Get the fixed asset.
     */
    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
    }

    /**
     * Get the linked GL expense record.
     */
    public function glExpense()
    {
        return $this->belongsTo(GlExpense::class);
    }

    /**
     * Get the period as formatted string.
     */
    public function getPeriodDisplayAttribute(): string
    {
        // Historical data has period 0/0
        if ($this->period_month == 0 && $this->period_year == 0) {
            return 'Data Historis';
        }
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return ($months[$this->period_month] ?? $this->period_month) . ' ' . $this->period_year;
    }
}
