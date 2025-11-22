<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicalPathway;
use App\Models\PatientCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get compliance report data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function compliance(Request $request)
    {
        $query = PatientCase::with('clinicalPathway');
        
        // Apply filters if provided
        if ($request->has('pathway_id') && $request->pathway_id) {
            $query->where('clinical_pathway_id', $request->pathway_id);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('admission_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('admission_date', '<=', $request->date_to);
        }
        
        $cases = $query->get();
        
        return response()->json($cases);
    }

    /**
     * Get cost variance report data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function costVariance(Request $request)
    {
        $query = PatientCase::with('clinicalPathway');
        
        // Apply filters if provided
        if ($request->has('pathway_id') && $request->pathway_id) {
            $query->where('clinical_pathway_id', $request->pathway_id);
        }
        
        if ($request->has('variance_type') && $request->variance_type) {
            if ($request->variance_type == 'over') {
                $query->where('cost_variance', '>', 0);
            } elseif ($request->variance_type == 'under') {
                $query->where('cost_variance', '<', 0);
            }
        }
        
        $cases = $query->get();
        
        return response()->json($cases);
    }

    /**
     * Get pathway performance report data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pathwayPerformance(Request $request)
    {
        $pathways = ClinicalPathway::withCount('patientCases')
            ->withAvg('patientCases', 'compliance_percentage')
            ->withSum('patientCases', 'cost_variance')
            ->get();
            
        return response()->json($pathways);
    }
}
