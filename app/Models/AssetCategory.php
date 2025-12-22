<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class AssetCategory extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'code',
        'name',
        'type',
        'default_useful_life_years',
        'description',
        'is_active',
    ];

    protected $casts = [
        'default_useful_life_years' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Asset type options
     */
    public const TYPE_ALKES = 'alkes';
    public const TYPE_SARPRAS = 'sarpras';
    public const TYPE_KENDARAAN = 'kendaraan';
    public const TYPE_BANGUNAN = 'bangunan';
    public const TYPE_IT = 'it';
    public const TYPE_LAINNYA = 'lainnya';

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_ALKES => 'Alat Kesehatan',
            self::TYPE_SARPRAS => 'Sarana Prasarana',
            self::TYPE_KENDARAAN => 'Kendaraan',
            self::TYPE_BANGUNAN => 'Bangunan',
            self::TYPE_IT => 'Perangkat IT',
            self::TYPE_LAINNYA => 'Lainnya',
        ];
    }

    /**
     * Get all fixed assets in this category.
     */
    public function fixedAssets()
    {
        return $this->hasMany(FixedAsset::class);
    }

    /**
     * Scope to only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the display name for the type.
     */
    public function getTypeDisplayAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? $this->type;
    }
}
