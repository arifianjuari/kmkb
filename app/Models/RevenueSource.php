<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class RevenueSource extends Model
{
    use HasFactory, BelongsToHospital;

    // Default revenue source codes
    const CODE_BPJS = 'bpjs';
    const CODE_UMUM = 'umum';
    const CODE_ASURANSI = 'asuransi';
    const CODE_JAMKESDA = 'jamkesda';
    const CODE_CORPORATE = 'corporate';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the revenue records for this source.
     */
    public function revenueRecords()
    {
        return $this->hasMany(RevenueRecord::class);
    }

    /**
     * Scope for active sources.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get default revenue sources for seeding.
     */
    public static function getDefaultSources(): array
    {
        return [
            [
                'code' => self::CODE_BPJS,
                'name' => 'BPJS/JKN',
                'description' => 'Badan Penyelenggara Jaminan Sosial Kesehatan',
                'sort_order' => 1,
            ],
            [
                'code' => self::CODE_UMUM,
                'name' => 'Pasien Umum',
                'description' => 'Pasien bayar mandiri (out-of-pocket)',
                'sort_order' => 2,
            ],
            [
                'code' => self::CODE_ASURANSI,
                'name' => 'Asuransi Swasta',
                'description' => 'Jaminan dari asuransi komersial',
                'sort_order' => 3,
            ],
            [
                'code' => self::CODE_JAMKESDA,
                'name' => 'Jamkesda/Jampersal',
                'description' => 'Jaminan Kesehatan Daerah',
                'sort_order' => 4,
            ],
            [
                'code' => self::CODE_CORPORATE,
                'name' => 'Perusahaan/Corporate',
                'description' => 'Jaminan dari perusahaan/institusi',
                'sort_order' => 5,
            ],
        ];
    }
}
