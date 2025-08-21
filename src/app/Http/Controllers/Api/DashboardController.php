<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicalPathway;
use App\Models\PatientCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard summary data.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        // Get summary statistics
        $totalPathways = ClinicalPathway::count();
        $totalCases = PatientCase::count();
        $averageCompliance = PatientCase::avg('compliance_percentage');
        $totalCostVariance = PatientCase::sum('cost_variance');
        
        // Get recent cases
        $recentCases = PatientCase::with('clinicalPathway')
            ->latest()
            ->limit(5)
            ->get();
            
        // Get cases by pathway
        $casesByPathway = PatientCase::select(
                'clinical_pathways.name as pathway_name',
                DB::raw('COUNT(*) as case_count')
            )
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->groupBy('clinical_pathways.name')
            ->get();
            
        $summary = [
            'total_pathways' => $totalPathways,
            'total_cases' => $totalCases,
            'average_compliance' => $averageCompliance,
            'total_cost_variance' => $totalCostVariance,
            'recent_cases' => $recentCases,
            'cases_by_pathway' => $casesByPathway,
        ];
            
        return response()->json($summary);
    }

    /**
     * Get dashboard trends data.
     *
     * @return \Illuminate\Http\Response
     */
    public function trends()
    {
        // Get compliance trend (last 30 days)
        $complianceTrend = PatientCase::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(compliance_percentage) as avg_compliance')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Get cost variance trend (last 30 days)
        $costVarianceTrend = PatientCase::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(cost_variance) as total_variance')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $trends = [
            'compliance_trend' => $complianceTrend,
            'cost_variance_trend' => $costVarianceTrend,
        ];
            
        return response()->json($trends);
    }
}
