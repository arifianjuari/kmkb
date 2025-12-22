<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ServiceFeeIndex extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The table associated with the model.
     */
    protected $table = 'service_fee_indexes';

    // Category constants (matching distribution in ServiceFeeConfig)
    const CATEGORY_MEDIS = 'medis';
    const CATEGORY_KEPERAWATAN = 'keperawatan';
    const CATEGORY_PENUNJANG = 'penunjang';
    const CATEGORY_MANAJEMEN = 'manajemen';

    // Role constants
    const ROLE_DPJP = 'dpjp';
    const ROLE_OPERATOR = 'operator';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_ANESTHESIOLOGIST = 'anesthesiologist';
    const ROLE_NURSE_PRIMARY = 'nurse_primary';
    const ROLE_NURSE_ASSISTANT = 'nurse_assistant';
    const ROLE_MIDWIFE = 'midwife';
    const ROLE_LAB_ANALYST = 'lab_analyst';
    const ROLE_RADIOGRAPHER = 'radiographer';
    const ROLE_PHARMACIST = 'pharmacist';
    const ROLE_NUTRITIONIST = 'nutritionist';
    const ROLE_PHYSIOTHERAPIST = 'physiotherapist';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hospital_id',
        'service_fee_config_id',
        'category',
        'professional_category',
        'role',
        'base_index',
        'education_factor',
        'risk_factor',
        'emergency_factor',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'base_index' => 'decimal:4',
        'education_factor' => 'decimal:2',
        'risk_factor' => 'decimal:2',
        'emergency_factor' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the configuration for this index.
     */
    public function config()
    {
        return $this->belongsTo(ServiceFeeConfig::class, 'service_fee_config_id');
    }

    /**
     * Get the assignments using this index.
     */
    public function assignments()
    {
        return $this->hasMany(ServiceFeeAssignment::class);
    }

    /**
     * Get the calculated final index.
     */
    public function getFinalIndexAttribute(): float
    {
        return round(
            $this->base_index * $this->education_factor * $this->risk_factor * $this->emergency_factor,
            4
        );
    }

    /**
     * Scope for active indexes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_MEDIS => 'Jasa Medis',
            self::CATEGORY_KEPERAWATAN => 'Jasa Keperawatan',
            self::CATEGORY_PENUNJANG => 'Jasa Penunjang',
            self::CATEGORY_MANAJEMEN => 'Jasa Manajemen',
        ];
    }

    /**
     * Get all roles.
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_DPJP => 'DPJP (Dokter Penanggung Jawab)',
            self::ROLE_OPERATOR => 'Dokter Operator',
            self::ROLE_ASSISTANT => 'Dokter Asisten',
            self::ROLE_ANESTHESIOLOGIST => 'Dokter Anestesi',
            self::ROLE_NURSE_PRIMARY => 'Perawat Primer',
            self::ROLE_NURSE_ASSISTANT => 'Perawat Asisten',
            self::ROLE_MIDWIFE => 'Bidan',
            self::ROLE_LAB_ANALYST => 'Analis Laboratorium',
            self::ROLE_RADIOGRAPHER => 'Radiografer',
            self::ROLE_PHARMACIST => 'Apoteker/Farmasi',
            self::ROLE_NUTRITIONIST => 'Ahli Gizi',
            self::ROLE_PHYSIOTHERAPIST => 'Fisioterapis',
            self::ROLE_ADMIN => 'Administrasi',
            self::ROLE_MANAGER => 'Manajer/Kepala',
        ];
    }

    /**
     * Get role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::getRoles()[$this->role] ?? $this->role;
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get professional category label.
     */
    public function getProfessionalCategoryLabelAttribute(): string
    {
        return Employee::getProfessionalCategories()[$this->professional_category] ?? $this->professional_category;
    }
}
