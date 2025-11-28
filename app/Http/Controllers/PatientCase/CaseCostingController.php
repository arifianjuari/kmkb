<?php

namespace App\Http\Controllers\PatientCase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientCase;
use App\Models\CaseDetail;
use App\Models\ClinicalPathway;
use Illuminate\Support\Facades\DB;

class CaseCostingController extends Controller
{
    /**
     * Show case costing index - list of cases to view costing
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
        
        return view('cases.costing-index', compact('cases', 'q', 'pathwayId', 'pathways'));
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
        
        // Calculate actual costs
        $performedDetails = $case->caseDetails->where('performed', 1);
        $actualTotalCost = $performedDetails->sum(function($detail) {
            return ($detail->actual_cost ?? 0) * ($detail->quantity ?? 1);
        });
        
        // Calculate pathway estimated costs
        $pathwayEstimatedCost = 0;
        if ($case->clinicalPathway) {
            $pathwayEstimatedCost = $case->clinicalPathway->steps->sum(function($step) {
                return ($step->estimated_cost ?? 0) * ($step->quantity ?? 1);
            });
        }
        
        // Cost breakdown by category
        $costByCategory = $performedDetails->groupBy(function($detail) {
            if ($detail->isCustomStep()) {
                return 'Custom Steps';
            }
            return $detail->pathwayStep->category ?? 'Other';
        })->map(function($details, $category) {
            return [
                'category' => $category,
                'count' => $details->count(),
                'total_cost' => $details->sum(function($d) {
                    return ($d->actual_cost ?? 0) * ($d->quantity ?? 1);
                }),
            ];
        })->sortByDesc('total_cost');
        
        // Cost breakdown by day
        $costByDay = $performedDetails->groupBy(function($detail) {
            if ($detail->isCustomStep()) {
                return 'Custom';
            }
            return $detail->pathwayStep->step_order ?? 'Other';
        })->map(function($details, $day) {
            return [
                'day' => $day,
                'count' => $details->count(),
                'total_cost' => $details->sum(function($d) {
                    return ($d->actual_cost ?? 0) * ($d->quantity ?? 1);
                }),
            ];
        })->sortKeys();
        
        // Unit cost vs actual cost comparison
        $unitCostComparison = $performedDetails->map(function($detail) {
            $unitCost = $detail->unit_cost_applied ?? 0;
            $actualCost = $detail->actual_cost ?? 0;
            $quantity = $detail->quantity ?? 1;
            
            return [
                'detail_id' => $detail->id,
                'service' => $detail->isCustomStep() 
                    ? ($detail->service_item ?? 'Custom Service')
                    : ($detail->pathwayStep->description ?? 'N/A'),
                'unit_cost' => $unitCost,
                'actual_cost' => $actualCost,
                'quantity' => $quantity,
                'unit_cost_total' => $unitCost * $quantity,
                'actual_cost_total' => $actualCost * $quantity,
                'variance' => ($unitCost * $quantity) - ($actualCost * $quantity),
            ];
        });
        
        // Summary statistics
        $summary = [
            'actual_total_cost' => $actualTotalCost,
            'pathway_estimated_cost' => $pathwayEstimatedCost,
            'cost_variance' => $pathwayEstimatedCost > 0 ? $actualTotalCost - $pathwayEstimatedCost : 0,
            'cost_variance_percent' => $pathwayEstimatedCost > 0 
                ? (($actualTotalCost - $pathwayEstimatedCost) / $pathwayEstimatedCost) * 100 
                : 0,
            'ina_cbg_tariff' => $case->ina_cbg_tariff ?? 0,
            'ina_cbg_variance' => ($case->ina_cbg_tariff ?? 0) - $actualTotalCost,
            'total_services' => $performedDetails->count(),
            'total_quantity' => $performedDetails->sum('quantity'),
        ];
        
        return view('cases.costing', compact(
            'case',
            'performedDetails',
            'actualTotalCost',
            'pathwayEstimatedCost',
            'costByCategory',
            'costByDay',
            'unitCostComparison',
            'summary'
        ));
    }
}

