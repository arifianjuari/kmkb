<?php

namespace App\Services;

use App\Models\PatientCase;

class ComplianceCalculator
{
    private CriteriaEvaluator $evaluator;

    public function __construct(?CriteriaEvaluator $evaluator = null)
    {
        $this->evaluator = $evaluator ?? new CriteriaEvaluator();
    }

    /**
     * Compute compliance percentage for a patient case.
     * 
     * Compliance calculation logic (matching show.blade.php):
     * 1. Base Compliance = (Used Pathway Steps / Pathway Steps Count) × 100
     *    - Used Pathway Steps = case details that are performed = true AND not custom step
     * 2. Over-treatment Penalty:
     *    - If Standard Steps Count > Used Steps, apply penalty
     *    - Standard Steps Count = all case details that are not custom step (regardless of performed status)
     *    - Penalty = ((Standard Steps Count - Used Steps) / Pathway Steps Count) × 100
     *    - Final Compliance = Base Compliance - Penalty (minimum 0)
     */
    public function computeCompliance(PatientCase $case): float
    {
        $case->loadMissing(['clinicalPathway.steps', 'caseDetails']);
        
        // Get total pathway steps count
        $pathwayStepsCount = $case->clinicalPathway?->steps->count() ?? 0;
        
        // Handle division by zero
        if ($pathwayStepsCount === 0) {
            return 100.00;
        }
        
        // Get used pathway steps (performed = true AND not custom step)
        $usedSteps = $case->caseDetails->filter(function($detail) {
            return !$detail->isCustomStep() && $detail->performed;
        })->count();
        
        // Get standard steps count (all case details that are not custom step, regardless of performed)
        $standardStepsOnlyCount = $case->caseDetails->filter(function($detail) {
            return !$detail->isCustomStep();
        })->count();
        
        // Base compliance = (Used Steps / Pathway Steps Count) × 100
        $baseCompliance = round(($usedSteps / $pathwayStepsCount) * 100, 2);
        
        // Over-treatment penalty (based on standard steps exceeding used steps)
        $isOverTreatment = $standardStepsOnlyCount > $usedSteps;
        if ($isOverTreatment) {
            $overTreatmentCount = $standardStepsOnlyCount - $usedSteps;
            $overTreatmentPenalty = round(($overTreatmentCount / $pathwayStepsCount) * 100, 2);
            $baseCompliance = max(0, $baseCompliance - $overTreatmentPenalty);
        }
        
        return round($baseCompliance, 2);
    }
}
