<?php

namespace App\Http\Controllers\PatientCase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientCase;
use App\Models\CaseDetail;
use App\Models\ClinicalPathway;
use Illuminate\Support\Facades\DB;

class CaseVarianceController extends Controller
{
    /**
     * Show case variance index - list of cases to view variance analysis
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $pathwayId = $request->get('pathway_id');
        
        $query = PatientCase::where('hospital_id', hospital('id'))
            ->with(['clinicalPathway', 'inputBy'])
            ->latest();

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('medical_record_number', 'like', "%$q%")
                    ->orWhere('patient_id', 'like', "%$q%")
                    ->orWhere('primary_diagnosis', 'like', "%$q%");
            });
        }
        
        if ($pathwayId) {
            $query->where('clinical_pathway_id', $pathwayId);
        }

        $cases = $query->paginate(15)->withQueryString();
        
        $pathways = ClinicalPathway::where('hospital_id', hospital('id'))
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();
        
        return view('cases.variance-index', compact('cases', 'q', 'pathwayId', 'pathways'));
    }
    
    public function show(PatientCase $case)
    {
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $case->load([
            'clinicalPathway.steps.costReference',
            'caseDetails.pathwayStep.costReference',
            'caseDetails.costReference',
            'inputBy'
        ]);
        
        // Planned steps from pathway
        $plannedSteps = $case->clinicalPathway ? $case->clinicalPathway->steps : collect();
        
        // Actual steps (performed case details)
        $actualSteps = $case->caseDetails->where('performed', 1);
        
        // Step compliance analysis
        $stepCompliance = [];
        foreach ($plannedSteps as $step) {
            $matchingDetails = $actualSteps->where('pathway_step_id', $step->id);
            $performed = $matchingDetails->where('performed', 1)->count() > 0;
            
            $stepCompliance[] = [
                'step' => $step,
                'planned' => true,
                'performed' => $performed,
                'details' => $matchingDetails,
                'estimated_cost' => ($step->estimated_cost ?? 0) * ($step->quantity ?? 1),
                'actual_cost' => $matchingDetails->sum(function($d) {
                    return ($d->actual_cost ?? 0) * ($d->quantity ?? 1);
                }),
            ];
        }
        
        // Custom steps (not in pathway)
        $customSteps = $actualSteps->filter(function($detail) {
            return $detail->isCustomStep();
        })->map(function($detail) {
            return [
                'detail' => $detail,
                'planned' => false,
                'performed' => true,
                'actual_cost' => ($detail->actual_cost ?? 0) * ($detail->quantity ?? 1),
            ];
        });
        
        // Cost variance analysis
        $actualTotalCost = $actualSteps->sum(function($detail) {
            return ($detail->actual_cost ?? 0) * ($detail->quantity ?? 1);
        });
        
        $pathwayEstimatedCost = $plannedSteps->sum(function($step) {
            return ($step->estimated_cost ?? 0) * ($step->quantity ?? 1);
        });
        
        // Variance by step
        $varianceByStep = [];
        foreach ($stepCompliance as $item) {
            $variance = $item['estimated_cost'] - $item['actual_cost'];
            $variancePercent = $item['estimated_cost'] > 0 
                ? ($variance / $item['estimated_cost']) * 100 
                : 0;
            
            $varianceByStep[] = [
                'step' => $item['step'],
                'performed' => $item['performed'],
                'estimated_cost' => $item['estimated_cost'],
                'actual_cost' => $item['actual_cost'],
                'variance' => $variance,
                'variance_percent' => $variancePercent,
                'status' => $variance >= 0 ? 'favorable' : 'unfavorable',
            ];
        }
        
        // Compliance statistics
        $totalPlannedSteps = $plannedSteps->count();
        $performedPlannedSteps = collect($stepCompliance)->where('performed', true)->count();
        $complianceRate = $totalPlannedSteps > 0 
            ? ($performedPlannedSteps / $totalPlannedSteps) * 100 
            : 0;
        
        // Summary statistics
        $summary = [
            'total_planned_steps' => $totalPlannedSteps,
            'performed_planned_steps' => $performedPlannedSteps,
            'compliance_rate' => $complianceRate,
            'custom_steps_count' => $customSteps->count(),
            'actual_total_cost' => $actualTotalCost,
            'pathway_estimated_cost' => $pathwayEstimatedCost,
            'cost_variance' => $actualTotalCost - $pathwayEstimatedCost,
            'cost_variance_percent' => $pathwayEstimatedCost > 0 
                ? (($actualTotalCost - $pathwayEstimatedCost) / $pathwayEstimatedCost) * 100 
                : 0,
            'ina_cbg_tariff' => $case->ina_cbg_tariff ?? 0,
            'ina_cbg_variance' => ($case->ina_cbg_tariff ?? 0) - $actualTotalCost,
            'favorable_variances' => collect($varianceByStep)->where('status', 'favorable')->count(),
            'unfavorable_variances' => collect($varianceByStep)->where('status', 'unfavorable')->count(),
        ];
        
        return view('cases.variance', compact(
            'case',
            'plannedSteps',
            'actualSteps',
            'stepCompliance',
            'customSteps',
            'varianceByStep',
            'summary'
        ));
    }
}

