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
     * Compliance Percentage = ((Used Pathway Steps - Custom Steps Count) / Pathway Steps Count) × 100
     * 
     * Used Pathway Steps = Count of case details that are:
     *   - Not custom steps (have pathway_step_id)
     *   - Performed = true
     * 
     * Custom Steps = Count of case details that are:
     *   - Custom steps (pathway_step_id is null)
     *   - These reduce compliance as they indicate deviation from standard pathway
     * 
     * Pathway Steps Count = Total steps in the clinical pathway
     * 
     * Note: Compliance cannot go below 0%.
     */
    public function computeCompliance(PatientCase $case): float
    {
        $case->loadMissing(['clinicalPathway.steps', 'caseDetails']);
        
        // Get total pathway steps count
        $pathwayStepsCount = $case->clinicalPathway?->steps->count() ?? 0;
        
        // Get used pathway steps - only count steps that are performed and not custom
        $usedPathwayStepIds = $case->caseDetails->filter(function($detail) {
            // Only count pathway steps that are performed (performed = 1 or true)
            return !$detail->isCustomStep() && $detail->performed;
        })->pluck('pathway_step_id')->unique();
        
        $usedPathwayStepsCount = $usedPathwayStepIds->count();
        
        // Count custom steps (non-compliance penalty)
        // Custom steps reduce compliance as they indicate deviation from standard pathway
        $customStepsCount = $case->caseDetails->filter(function($detail) {
            return $detail->isCustomStep();
        })->count();

        // Handle division by zero
        if ($pathwayStepsCount === 0) {
            return 100.00;
        }

        // Calculate compliance: (Used Pathway Steps - Custom Steps) / Pathway Steps Count × 100
        // Custom steps act as a penalty, reducing the compliance percentage
        $compliance = (($usedPathwayStepsCount - $customStepsCount) / $pathwayStepsCount) * 100;
        
        // Ensure compliance doesn't go below 0%
        return round(max(0, $compliance), 2);
    }
}
