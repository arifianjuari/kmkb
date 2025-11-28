<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToHospital;

class ExpenseCategory extends Model
{
    use HasFactory, BelongsToHospital;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hospital_id',
        'account_code',
        'account_name',
        'cost_type',
        'allocation_category',
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
     * Get the cost references for this expense category.
     */
    public function costReferences()
    {
        return $this->hasMany(CostReference::class);
    }

    /**
     * Get the GL expenses for this expense category.
     */
    public function glExpenses()
    {
        return $this->hasMany(GlExpense::class);
    }
}





