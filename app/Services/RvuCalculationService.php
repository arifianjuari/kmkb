<?php

namespace App\Services;

use App\Models\RvuValue;
use App\Models\CostReference;

class RvuCalculationService
{
    /**
     * Calculate RVU value from factors.
     *
     * @param int $timeFactor Waktu dalam menit
     * @param int $professionalismFactor 1-5
     * @param int $difficultyFactor 1-10
     * @param float $normalizationFactor Default 1.0
     * @return float
     */
    public function calculateRvuValue($timeFactor, $professionalismFactor, $difficultyFactor, $normalizationFactor = 1.0)
    {
        if ($normalizationFactor == 0) {
            $normalizationFactor = 1.0;
        }
        
        return round(($timeFactor * $professionalismFactor * $difficultyFactor) / $normalizationFactor, 4);
    }

    /**
     * Get active RVU for a cost reference and period.
     *
     * @param int $hospitalId
     * @param int $costReferenceId
     * @param int $year
     * @param int|null $month
     * @return RvuValue|null
     */
    public function getActiveRvuForPeriod($hospitalId, $costReferenceId, $year, $month = null)
    {
        $query = RvuValue::where('hospital_id', $hospitalId)
            ->where('cost_reference_id', $costReferenceId)
            ->where('period_year', $year)
            ->where('is_active', true);
        
        if ($month !== null) {
            $query->where('period_month', $month);
        } else {
            $query->whereNull('period_month');
        }
        
        return $query->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get RVU-weighted volume.
     *
     * @param float $serviceVolume
     * @param float $rvuValue
     * @return float
     */
    public function getRvuWeightedVolume($serviceVolume, $rvuValue)
    {
        return round($serviceVolume * $rvuValue, 4);
    }

    /**
     * Validate RVU factors.
     *
     * @param int $timeFactor
     * @param int $professionalismFactor
     * @param int $difficultyFactor
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateRvuFactors($timeFactor, $professionalismFactor, $difficultyFactor)
    {
        $errors = [];
        
        if ($timeFactor < 1) {
            $errors[] = 'Time factor must be at least 1 minute';
        }
        
        if (!in_array($professionalismFactor, [1, 2, 3, 4, 5])) {
            $errors[] = 'Professionalism factor must be between 1 and 5';
        }
        
        if ($difficultyFactor < 1 || $difficultyFactor > 10) {
            $errors[] = 'Difficulty factor must be between 1 and 10';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

