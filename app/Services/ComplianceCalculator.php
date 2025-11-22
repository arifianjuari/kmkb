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
     * Compliance Percentage = (Standard Steps Count / Pathway Steps Count) × 100
     * Standard Steps Count = Case Steps Count - Custom Steps Count
     * Pathway Steps Count = Total steps in the clinical pathway
     * 
     * If Standard Steps Count exceeds Pathway Steps Count, compliance should decrease
     * to penalize over-treatment rather than increase beyond 100%
     */
    public function computeCompliance(PatientCase $case): float
    {
        $case->loadMissing(['clinicalPathway.steps', 'caseDetails']);
        
        // Get total pathway steps count
        $pathwayStepsCount = $case->clinicalPathway?->steps->count() ?? 0;
        
        // Get custom steps count
        $customStepsCount = $case->caseDetails->filter(function($detail) {
            return $detail->isCustomStep();
        })->count();
        
        // Calculate standard steps count
        $standardStepsCount = $case->caseDetails->count() - $customStepsCount;

        // Handle division by zero
        if ($pathwayStepsCount === 0) {
            return 100.00;
        }

        // If standard steps count exceeds pathway steps count,
        // calculate penalty to reduce compliance instead of exceeding 100%
        if ($standardStepsCount > $pathwayStepsCount) {
            // Calculate how much it exceeds as a ratio
            $excessRatio = ($standardStepsCount - $pathwayStepsCount) / $pathwayStepsCount;
            // Calculate base compliance (100%)
            $baseCompliance = 100.00;
            // Apply penalty - reduce compliance by the excess ratio
            // This ensures that significant over-treatment results in lower compliance
            $compliance = $baseCompliance - ($excessRatio * 50); // 50% penalty factor
            // Ensure compliance doesn't go below 0
            return max(0, round($compliance, 2));
        }

        // Normal calculation when standard steps don't exceed pathway steps
        return round(($standardStepsCount / $pathwayStepsCount) * 100, 2);
    }
}
