<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'logo_path',
        'theme_color',
        'address',
        'contact',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the users for the hospital.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the clinical pathways for the hospital.
     */
    public function clinicalPathways()
    {
        return $this->hasMany(ClinicalPathway::class);
    }

    /**
     * Get the pathway steps for the hospital.
     */
    public function pathwaySteps()
    {
        return $this->hasMany(PathwayStep::class);
    }

    /**
     * Get the patient cases for the hospital.
     */
    public function patientCases()
    {
        return $this->hasMany(PatientCase::class);
    }

    /**
     * Get the case details for the hospital.
     */
    public function caseDetails()
    {
        return $this->hasMany(CaseDetail::class);
    }

    /**
     * Get the cost references for the hospital.
     */
    public function costReferences()
    {
        return $this->hasMany(CostReference::class);
    }

    /**
     * Get the audit logs for the hospital.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
