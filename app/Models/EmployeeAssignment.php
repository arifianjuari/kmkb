<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'cost_center_id',
        'fte_percentage',
        'effective_date',
        'end_date',
        'is_primary',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fte_percentage' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee for this assignment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the cost center for this assignment.
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Scope for active assignments (no end date or end date in the future).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    /**
     * Scope for assignments active in a specific period.
     */
    public function scopeActiveInPeriod($query, $month, $year)
    {
        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        return $query->where('effective_date', '<=', $endOfMonth)
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $startOfMonth);
            });
    }

    /**
     * Scope for assignments in a specific cost center.
     */
    public function scopeInCostCenter($query, $costCenterId)
    {
        return $query->where('cost_center_id', $costCenterId);
    }

    /**
     * Get FTE as percentage string.
     */
    public function getFtePercentageDisplayAttribute()
    {
        return number_format($this->fte_percentage * 100, 0) . '%';
    }

    /**
     * Calculate total FTE per cost center for a period.
     * 
     * @param int $hospitalId
     * @param int $month
     * @param int $year
     * @return \Illuminate\Support\Collection
     */
    public static function getFtePerCostCenter($hospitalId, $month, $year)
    {
        return self::query()
            ->join('employees', 'employee_assignments.employee_id', '=', 'employees.id')
            ->join('cost_centers', 'employee_assignments.cost_center_id', '=', 'cost_centers.id')
            ->where('employees.hospital_id', $hospitalId)
            ->where('employees.status', Employee::STATUS_ACTIVE)
            ->activeInPeriod($month, $year)
            ->selectRaw('cost_centers.id as cost_center_id, cost_centers.code, cost_centers.name, SUM(employee_assignments.fte_percentage) as total_fte, COUNT(DISTINCT employees.id) as employee_count')
            ->groupBy('cost_centers.id', 'cost_centers.code', 'cost_centers.name')
            ->get();
    }
}
