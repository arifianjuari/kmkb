<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToHospital;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Division extends Model
{
    use HasFactory, BelongsToHospital, HasUlids;

    protected $fillable = [
        'hospital_id',
        'parent_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent division.
     */
    public function parent()
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }

    /**
     * Get the child divisions.
     */
    public function children()
    {
        return $this->hasMany(Division::class, 'parent_id');
    }
}
