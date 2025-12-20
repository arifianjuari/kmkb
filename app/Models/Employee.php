<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class Employee extends Model
{
    use HasFactory, BelongsToHospital;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_RESIGNED = 'resigned';

    // Employment Types
    const EMPLOYMENT_PNS = 'pns';
    const EMPLOYMENT_PPPK = 'pppk';
    const EMPLOYMENT_TNI = 'tni';
    const EMPLOYMENT_POLRI = 'polri';
    const EMPLOYMENT_BUMN = 'bumn';
    const EMPLOYMENT_CONTRACT = 'contract';
    const EMPLOYMENT_HONORARY = 'honorary';
    const EMPLOYMENT_OUTSOURCE = 'outsource';

    // Education Levels
    const EDUCATION_SD = 'sd';
    const EDUCATION_SMP = 'smp';
    const EDUCATION_SMA = 'sma';
    const EDUCATION_D1 = 'd1';
    const EDUCATION_D2 = 'd2';
    const EDUCATION_D3 = 'd3';
    const EDUCATION_D4 = 'd4';
    const EDUCATION_S1 = 's1';
    const EDUCATION_S2 = 's2';
    const EDUCATION_S3 = 's3';
    const EDUCATION_SPECIALIST = 'specialist';

    // Professional Categories
    const CATEGORY_DOCTOR_SPECIALIST = 'doctor_specialist';
    const CATEGORY_DOCTOR_GENERAL = 'doctor_general';
    const CATEGORY_NURSE = 'nurse';
    const CATEGORY_MIDWIFE = 'midwife';
    const CATEGORY_HEALTH_ANALYST = 'health_analyst';
    const CATEGORY_PHARMACIST = 'pharmacist';
    const CATEGORY_NUTRITIONIST = 'nutritionist';
    const CATEGORY_RADIOGRAPHER = 'radiographer';
    const CATEGORY_PHYSIOTHERAPIST = 'physiotherapist';
    const CATEGORY_ADMIN = 'admin';
    const CATEGORY_NON_MEDICAL = 'non_medical';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'employee_number',
        'name',
        'job_title',
        'employment_type',
        'education_level',
        'professional_category',
        'base_salary',
        'allowances',
        'join_date',
        'resign_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'join_date' => 'date',
        'resign_date' => 'date',
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all assignments for this employee.
     */
    public function assignments()
    {
        return $this->hasMany(EmployeeAssignment::class);
    }

    /**
     * Get active assignments for this employee.
     */
    public function activeAssignments()
    {
        return $this->hasMany(EmployeeAssignment::class)
            ->whereNull('end_date')
            ->orWhere('end_date', '>=', now());
    }

    /**
     * Get the primary assignment.
     */
    public function primaryAssignment()
    {
        return $this->hasOne(EmployeeAssignment::class)
            ->where('is_primary', true)
            ->whereNull('end_date');
    }

    /**
     * Scope for active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for inactive employees.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope for resigned employees.
     */
    public function scopeResigned($query)
    {
        return $query->where('status', self::STATUS_RESIGNED);
    }

    /**
     * Scope for employees active in a specific period.
     */
    public function scopeActiveInPeriod($query, $month, $year)
    {
        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('join_date')
                  ->orWhere('join_date', '<=', $startOfMonth);
            })
            ->where(function ($q) use ($endOfMonth) {
                $q->whereNull('resign_date')
                  ->orWhere('resign_date', '>=', $endOfMonth);
            });
    }

    /**
     * Get total FTE for this employee across all active assignments.
     */
    public function getTotalFteAttribute()
    {
        return $this->activeAssignments()->sum('fte_percentage');
    }

    /**
     * Get total salary (base + allowances).
     */
    public function getTotalSalaryAttribute()
    {
        return ($this->base_salary ?? 0) + ($this->allowances ?? 0);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Non-Aktif',
            self::STATUS_RESIGNED => 'Resign',
            default => $this->status,
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_INACTIVE => 'bg-yellow-100 text-yellow-800',
            self::STATUS_RESIGNED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get employment type label.
     */
    public function getEmploymentTypeLabelAttribute()
    {
        return self::getEmploymentTypes()[$this->employment_type] ?? $this->employment_type;
    }

    /**
     * Get education level label.
     */
    public function getEducationLevelLabelAttribute()
    {
        return self::getEducationLevels()[$this->education_level] ?? $this->education_level;
    }

    /**
     * Get professional category label.
     */
    public function getProfessionalCategoryLabelAttribute()
    {
        return self::getProfessionalCategories()[$this->professional_category] ?? $this->professional_category;
    }

    /**
     * Get all available statuses.
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Non-Aktif',
            self::STATUS_RESIGNED => 'Resign',
        ];
    }

    /**
     * Get all employment types.
     */
    public static function getEmploymentTypes()
    {
        return [
            self::EMPLOYMENT_PNS => 'PNS/ASN',
            self::EMPLOYMENT_PPPK => 'PPPK',
            self::EMPLOYMENT_TNI => 'TNI',
            self::EMPLOYMENT_POLRI => 'Polri',
            self::EMPLOYMENT_BUMN => 'BUMN/BUMD',
            self::EMPLOYMENT_CONTRACT => 'Kontrak',
            self::EMPLOYMENT_HONORARY => 'Honorer',
            self::EMPLOYMENT_OUTSOURCE => 'Outsource',
        ];
    }

    /**
     * Get all education levels.
     */
    public static function getEducationLevels()
    {
        return [
            self::EDUCATION_SD => 'SD',
            self::EDUCATION_SMP => 'SMP',
            self::EDUCATION_SMA => 'SMA/SMK',
            self::EDUCATION_D1 => 'D1',
            self::EDUCATION_D2 => 'D2',
            self::EDUCATION_D3 => 'D3',
            self::EDUCATION_D4 => 'D4',
            self::EDUCATION_S1 => 'S1',
            self::EDUCATION_S2 => 'S2',
            self::EDUCATION_S3 => 'S3',
            self::EDUCATION_SPECIALIST => 'Spesialis',
        ];
    }

    /**
     * Get all professional categories.
     */
    public static function getProfessionalCategories()
    {
        return [
            self::CATEGORY_DOCTOR_SPECIALIST => 'Dokter Spesialis',
            self::CATEGORY_DOCTOR_GENERAL => 'Dokter Umum',
            self::CATEGORY_NURSE => 'Perawat',
            self::CATEGORY_MIDWIFE => 'Bidan',
            self::CATEGORY_HEALTH_ANALYST => 'Analis Kesehatan',
            self::CATEGORY_PHARMACIST => 'Apoteker/Farmasi',
            self::CATEGORY_NUTRITIONIST => 'Ahli Gizi',
            self::CATEGORY_RADIOGRAPHER => 'Radiografer',
            self::CATEGORY_PHYSIOTHERAPIST => 'Fisioterapis',
            self::CATEGORY_ADMIN => 'Administrasi',
            self::CATEGORY_NON_MEDICAL => 'Non Medis',
        ];
    }
}

