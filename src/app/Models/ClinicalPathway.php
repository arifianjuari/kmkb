<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalPathway extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'diagnosis_code',
        'version',
        'effective_date',
        'created_by',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'effective_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the pathway steps for the clinical pathway.
     */
    public function steps()
    {
        return $this->hasMany(PathwayStep::class);
    }

    /**
     * Get the patient cases for the clinical pathway.
     */
    public function patientCases()
    {
        return $this->hasMany(PatientCase::class);
    }

    /**
     * Get the user that created the pathway.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
