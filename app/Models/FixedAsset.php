<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class FixedAsset extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'asset_category_id',
        'cost_center_id',
        'asset_code',
        'name',
        'description',
        // BMN Identification
        'jenis_bmn',
        'kode_satker',
        'nama_satker',
        'nup',
        'kode_register',
        // Inventory fields
        'brand',
        'model',
        'serial_number',
        'manufacturer',
        'location',
        'condition',
        // Document fields
        'jenis_dokumen',
        'no_dokumen',
        'no_bpkb',
        'no_polisi',
        'no_sertifikat',
        'jenis_sertifikat',
        'status_sertifikasi',
        // Address fields
        'alamat_lengkap',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'kode_kabupaten_kota',
        'provinsi',
        'kode_provinsi',
        'kode_pos',
        // Land/Building fields
        'luas_tanah_seluruhnya',
        'luas_tanah_bangunan',
        'luas_tanah_sarana',
        'luas_lahan_kosong',
        'luas_bangunan',
        'luas_tapak_bangunan',
        'luas_pemanfaatan',
        'jumlah_lantai',
        // Financial fields
        'acquisition_date',
        'tanggal_buku_pertama',
        'acquisition_cost',
        'useful_life_years',
        'salvage_value',
        'nilai_mutasi',
        'nilai_penyusutan',
        'nilai_buku',
        // Maintenance & Calibration
        'warranty_end_date',
        'last_maintenance_date',
        'next_maintenance_date',
        'calibration_due_date',
        // Status & Disposal
        'status',
        'status_penggunaan',
        'no_psp',
        'tanggal_psp',
        'sbsk',
        'optimalisasi',
        'penghuni',
        'pengguna',
        'disposal_date',
        'tanggal_penghapusan',
        'disposal_reason',
        'disposal_value',
        // Organization DJKN
        'kode_kpknl',
        'uraian_kpknl',
        'uraian_kanwil_djkn',
        'nama_kl',
        'nama_e1',
        'nama_korwil',
        'lokasi_ruang',
        // Metadata
        'bmn_metadata',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'tanggal_buku_pertama' => 'date',
        'acquisition_cost' => 'decimal:2',
        'useful_life_years' => 'integer',
        'salvage_value' => 'decimal:2',
        'nilai_mutasi' => 'decimal:2',
        'nilai_penyusutan' => 'decimal:2',
        'nilai_buku' => 'decimal:2',
        'warranty_end_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'calibration_due_date' => 'date',
        'disposal_date' => 'date',
        'tanggal_penghapusan' => 'date',
        'tanggal_psp' => 'date',
        'disposal_value' => 'decimal:2',
        'nup' => 'integer',
        'jumlah_lantai' => 'integer',
        'luas_tanah_seluruhnya' => 'decimal:2',
        'luas_tanah_bangunan' => 'decimal:2',
        'luas_tanah_sarana' => 'decimal:2',
        'luas_lahan_kosong' => 'decimal:2',
        'luas_bangunan' => 'decimal:2',
        'luas_tapak_bangunan' => 'decimal:2',
        'luas_pemanfaatan' => 'decimal:2',
        'bmn_metadata' => 'array',
    ];

    /**
     * Status constants
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISPOSED = 'disposed';
    public const STATUS_SOLD = 'sold';
    public const STATUS_IN_REPAIR = 'in_repair';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_DISPOSED => 'Dihapuskan',
            self::STATUS_SOLD => 'Dijual',
            self::STATUS_IN_REPAIR => 'Dalam Perbaikan',
        ];
    }

    /**
     * Condition constants
     */
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';
    public const CONDITION_DAMAGED = 'damaged';

    public static function getConditionOptions(): array
    {
        return [
            self::CONDITION_GOOD => 'Baik',
            self::CONDITION_FAIR => 'Cukup',
            self::CONDITION_POOR => 'Kurang',
            self::CONDITION_DAMAGED => 'Rusak',
        ];
    }

    /**
     * Jenis BMN constants
     */
    public const JENIS_BMN_TANAH = 'TANAH';
    public const JENIS_BMN_BANGUNAN = 'BANGUNAN';
    public const JENIS_BMN_PERALATAN = 'PERALATAN DAN MESIN';
    public const JENIS_BMN_JALAN = 'JALAN, IRIGASI DAN JARINGAN';
    public const JENIS_BMN_ASET_TETAP_LAINNYA = 'ASET TETAP LAINNYA';
    public const JENIS_BMN_KDP = 'KONSTRUKSI DALAM PENGERJAAN';

    public static function getJenisBmnOptions(): array
    {
        return [
            self::JENIS_BMN_TANAH => 'Tanah',
            self::JENIS_BMN_BANGUNAN => 'Bangunan',
            self::JENIS_BMN_PERALATAN => 'Peralatan dan Mesin',
            self::JENIS_BMN_JALAN => 'Jalan, Irigasi dan Jaringan',
            self::JENIS_BMN_ASET_TETAP_LAINNYA => 'Aset Tetap Lainnya',
            self::JENIS_BMN_KDP => 'Konstruksi Dalam Pengerjaan',
        ];
    }

    /**
     * Get jenis BMN display name.
     */
    public function getJenisBmnDisplayAttribute(): string
    {
        return self::getJenisBmnOptions()[$this->jenis_bmn] ?? $this->jenis_bmn ?? '-';
    }

    /**
     * Get the asset category.
     */
    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    /**
     * Get the cost center.
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the depreciation records.
     */
    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    /**
     * Scope to only active assets.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Calculate monthly depreciation using straight-line method.
     */
    public function getMonthlyDepreciationAttribute(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }
        
        $depreciableAmount = $this->acquisition_cost - ($this->salvage_value ?? 0);
        $annualDepreciation = $depreciableAmount / $this->useful_life_years;
        
        return round($annualDepreciation / 12, 2);
    }

    /**
     * Calculate annual depreciation.
     */
    public function getAnnualDepreciationAttribute(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }
        
        $depreciableAmount = $this->acquisition_cost - ($this->salvage_value ?? 0);
        return round($depreciableAmount / $this->useful_life_years, 2);
    }

    /**
     * Get number of months elapsed since acquisition date.
     */
    public function getMonthsElapsedAttribute(): int
    {
        if (!$this->acquisition_date) {
            return 0;
        }

        $acquisitionDate = $this->acquisition_date instanceof \Carbon\Carbon 
            ? $this->acquisition_date 
            : \Carbon\Carbon::parse($this->acquisition_date);
        
        $now = \Carbon\Carbon::now();
        
        // If acquisition date is in the future, return 0
        if ($acquisitionDate->gt($now)) {
            return 0;
        }

        // Calculate months difference
        return $acquisitionDate->diffInMonths($now);
    }

    /**
     * Get maximum depreciation months (useful life in months).
     */
    public function getMaxDepreciationMonthsAttribute(): int
    {
        return $this->useful_life_years * 12;
    }

    /**
     * Get accumulated depreciation calculated from acquisition date to now.
     * Option A: Auto-calculate based on months elapsed.
     */
    public function getAccumulatedDepreciation(?\DateTime $upToDate = null): float
    {
        $monthsElapsed = $this->months_elapsed;
        $maxMonths = $this->max_depreciation_months;
        
        // Cap at max depreciation months
        $effectiveMonths = min($monthsElapsed, $maxMonths);
        
        $accumulated = $effectiveMonths * $this->monthly_depreciation;
        $maxDepreciation = $this->acquisition_cost - ($this->salvage_value ?? 0);
        
        // Never exceed depreciable amount
        return min($accumulated, $maxDepreciation);
    }

    /**
     * Get current book value (auto-calculated).
     */
    public function getCurrentBookValueAttribute(): float
    {
        $accumulated = $this->getAccumulatedDepreciation();
        $bookValue = $this->acquisition_cost - $accumulated;
        
        // Never go below salvage value
        return max($bookValue, $this->salvage_value ?? 0);
    }

    /**
     * Check if depreciation is fully completed.
     */
    public function getIsFullyDepreciatedAttribute(): bool
    {
        return $this->months_elapsed >= $this->max_depreciation_months;
    }


    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get condition display name.
     */
    public function getConditionDisplayAttribute(): string
    {
        return self::getConditionOptions()[$this->condition] ?? $this->condition;
    }
}
