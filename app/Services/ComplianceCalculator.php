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
     * Compliance Percentage = (Used Pathway Steps / Pathway Steps Count) × 100
     * Used Pathway Steps = Count of case details that are:
     *   - Not custom steps (have pathway_step_id)
     *   - Performed = true
     * Pathway Steps Count = Total steps in the clinical pathway
     * 
     * This matches the calculation logic used in the case detail view.
     */
    public function computeCompliance(PatientCase $case): float
    {
        $case->loadMissing(['clinicalPathway.steps', 'caseDetails']);
        
        // Get total pathway steps count
        $pathwayStepsCount = $case->clinicalPathway?->steps->count() ?? 0;
        
        // Get used pathway steps - only count steps that are performed and not custom
        // This matches the logic in PatientCaseController::show()
        $usedPathwayStepIds = $case->caseDetails->filter(function($detail) {
            // Only count pathway steps that are performed (performed = 1 or true)
            return !$detail->isCustomStep() && $detail->performed;
        })->pluck('pathway_step_id')->unique();
        
        $usedPathwayStepsCount = $usedPathwayStepIds->count();

        // Handle division by zero
        if ($pathwayStepsCount === 0) {
            return 100.00;
        }

        // Calculate compliance: (Used Pathway Steps / Pathway Steps Count) × 100
        return round(($usedPathwayStepsCount / $pathwayStepsCount) * 100, 2);
    }
}
