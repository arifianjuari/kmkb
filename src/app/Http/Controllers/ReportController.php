<?php

namespace App\Http\Controllers;

use App\Models\ClinicalPathway;
use App\Models\PatientCase;
use App\Models\CaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the main dashboard with summary reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            
        // Get compliance by pathway
        $complianceByPathway = PatientCase::select(
                'clinical_pathways.name as pathway_name',
                DB::raw('AVG(compliance_percentage) as avg_compliance')
            )
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->groupBy('clinical_pathways.name')
            ->get();
            
        return view('reports.index', compact(
            'totalPathways', 
            'totalCases', 
            'averageCompliance', 
            'totalCostVariance', 
            'recentCases', 
            'complianceByPathway'
        ));
    }

    /**
     * Display compliance report.
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
        
        $cases = $query->paginate(20);
        
        $pathways = ClinicalPathway::all();
        
        return view('reports.compliance', compact('cases', 'pathways'));
    }

    /**
     * Display cost variance report.
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
        
        $cases = $query->paginate(20);
        
        $pathways = ClinicalPathway::all();
        
        return view('reports.cost_variance', compact('cases', 'pathways'));
    }

    /**
     * Display pathway performance report.
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
            
        return view('reports.pathway_performance', compact('pathways'));
    }

    /**
     * Export report to PDF.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($type)
    {
        // In a real implementation, you would generate a PDF here
        // For now, we'll just return a response
        return response()->download(resource_path('dummy-report.pdf'), "{$type}_report.pdf");
    }

    /**
     * Export report to Excel.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function exportExcel($type)
    {
        // In a real implementation, you would generate an Excel file here
        // For now, we'll just return a response
        return response()->download(resource_path('dummy-report.xlsx'), "{$type}_report.xlsx");
    }
}
