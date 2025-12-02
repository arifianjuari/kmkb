<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ServiceVolume extends Model
{
    use HasFactory, BelongsToHospital;

    protected $fillable = [
        'hospital_id',
        'period_month',
        'period_year',
        'cost_reference_id',
        'tariff_class_id',
        'total_quantity',
    ];

    protected $casts = [
        'total_quantity' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function costReference()
    {
        return $this->belongsTo(CostReference::class);
    }

    public function tariffClass()
    {
        return $this->belongsTo(TariffClass::class);
    }
}








