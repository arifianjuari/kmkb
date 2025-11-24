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
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display the detailed reports dashboard with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        try {
            $pathways = ClinicalPathway::all();

            // Helper to apply common filters
            $applyFilters = function ($q) use ($request) {
                if ($request->filled('pathway_id')) {
                    $q->where('clinical_pathway_id', $request->pathway_id);
                }
                if ($request->filled('date_from')) {
                    $q->whereDate('admission_date', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $q->whereDate('admission_date', '<=', $request->date_to);
                }
            };

            // Summary metrics
            $totalCases = PatientCase::where($applyFilters)->count();
            $averageCompliance = PatientCase::where($applyFilters)->avg('compliance_percentage');
            $totalCharges = PatientCase::where($applyFilters)->sum('actual_total_cost');
            $totalCostVariance = PatientCase::where($applyFilters)->sum('cost_variance');

            // Cases by pathway
            $casesByPathway = PatientCase::select(
                    'clinical_pathways.name as pathway_name',
                    DB::raw('COUNT(*) as case_count'),
                    DB::raw('AVG(compliance_percentage) as avg_compliance')
                )
                ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
                ->when($request->filled('pathway_id'), function ($q) use ($request) {
                    $q->where('clinical_pathway_id', $request->pathway_id);
                })
                ->when($request->filled('date_from'), function ($q) use ($request) {
                    $q->whereDate('admission_date', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function ($q) use ($request) {
                    $q->whereDate('admission_date', '<=', $request->date_to);
                })
                ->groupBy('clinical_pathways.name')
                ->get();

            // Monthly trend
            $monthlyTrend = PatientCase::select(
                    DB::raw('DATE_FORMAT(admission_date, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as case_count'),
                    DB::raw('AVG(compliance_percentage) as avg_compliance')
                )
                ->where(function ($q) use ($applyFilters) {
                    $applyFilters($q);
                })
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return view('reports.dashboard', compact(
                'pathways',
                'totalCases',
                'averageCompliance',
                'totalCharges',
                'totalCostVariance',
                'casesByPathway',
                'monthlyTrend'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load reports dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display compliance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function compliance(Request $request)
    {
        try {
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
            
            // Calculate compliance statistics
            $highComplianceCount = PatientCase::where('compliance_percentage', '>=', 90)->count();
            $mediumComplianceCount = PatientCase::whereBetween('compliance_percentage', [70, 89])->count();
            $lowComplianceCount = PatientCase::where('compliance_percentage', '<', 70)->count();
            
            return view('reports.compliance', compact('cases', 'pathways', 'highComplianceCount', 'mediumComplianceCount', 'lowComplianceCount'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load compliance report: ' . $e->getMessage());
        }
    }

    /**
     * Display cost variance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function costVariance(Request $request)
    {
        try {
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
            
            // Calculate cost variance statistics
            $overBudgetCount = PatientCase::where('cost_variance', '>', 0)->count();
            $underBudgetCount = PatientCase::where('cost_variance', '<', 0)->count();
            $totalVariance = PatientCase::sum('cost_variance');
            
            return view('reports.cost_variance', compact('cases', 'pathways', 'overBudgetCount', 'underBudgetCount', 'totalVariance'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load cost variance report: ' . $e->getMessage());
        }
    }

    /**
     * Display pathway performance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pathwayPerformance(Request $request)
    {
        try {
            // Get all pathways for dropdown filter
            $allPathways = ClinicalPathway::orderBy('name')->get();
            
            // Build base query for filtering
            $baseQuery = PatientCase::query();
            
            if ($request->filled('pathway_id')) {
                $baseQuery->where('clinical_pathway_id', $request->pathway_id);
            }
            
            if ($request->filled('date_from')) {
                $baseQuery->whereDate('admission_date', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $baseQuery->whereDate('admission_date', '<=', $request->date_to);
            }
            
            $caseIds = $baseQuery->pluck('id');
            
            // Build query for pathway metrics
            $pathwayMetricsQuery = PatientCase::select(
                    'clinical_pathways.id as pathway_id',
                    'clinical_pathways.name as pathway_name',
                    DB::raw('COUNT(DISTINCT patient_cases.id) as total_cases'),
                    DB::raw('COALESCE(AVG(patient_cases.compliance_percentage), 0) as avg_compliance'),
                    DB::raw('COALESCE(AVG(patient_cases.cost_variance), 0) as avg_cost_variance'),
                    DB::raw('COALESCE(AVG(CASE 
                        WHEN patient_cases.discharge_date IS NOT NULL 
                        THEN DATEDIFF(patient_cases.discharge_date, patient_cases.admission_date)
                        ELSE DATEDIFF(CURDATE(), patient_cases.admission_date)
                    END), 0) as avg_length_of_stay')
                )
                ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id');
            
            if ($caseIds->count() > 0) {
                $pathwayMetricsQuery->whereIn('patient_cases.id', $caseIds);
            } else {
                // If no cases match the filter, return empty result
                $pathwayMetricsQuery->whereRaw('1 = 0');
            }
            
            $pathwayMetricsQuery->groupBy('clinical_pathways.id', 'clinical_pathways.name');
            
            $pathwayMetrics = $pathwayMetricsQuery->get();
            
            // Calculate avg_steps_completed for each pathway
            foreach ($pathwayMetrics as $metric) {
                $pathwayCaseIds = PatientCase::where('clinical_pathway_id', $metric->pathway_id);
                
                if ($caseIds->count() > 0) {
                    $pathwayCaseIds->whereIn('id', $caseIds);
                } else {
                    $pathwayCaseIds->whereRaw('1 = 0');
                }
                
                $pathwayCaseIds = $pathwayCaseIds->pluck('id');
                
                if ($pathwayCaseIds->count() > 0) {
                    $stepCounts = CaseDetail::whereIn('patient_case_id', $pathwayCaseIds)
                        ->where(function($query) {
                            $query->where('performed', 1)
                                  ->orWhere('status', 'completed');
                        })
                        ->select('patient_case_id', DB::raw('COUNT(*) as step_count'))
                        ->groupBy('patient_case_id')
                        ->pluck('step_count');
                    
                    $metric->avg_steps_completed = $stepCounts->count() > 0 ? $stepCounts->avg() : 0;
                } else {
                    $metric->avg_steps_completed = 0;
                }
            }
            
            // Get step analysis if pathway_id is selected
            $stepAnalysis = collect([]);
            if ($request->filled('pathway_id') && $caseIds->count() > 0) {
                $stepAnalysisQuery = CaseDetail::select(
                        'pathway_steps.step_order as day',
                        'pathway_steps.description as activity',
                        DB::raw('COUNT(case_details.id) as times_performed'),
                        DB::raw('AVG(CASE 
                            WHEN case_details.performed = 1 OR case_details.status = "completed" 
                            THEN 100 
                            ELSE 0 
                        END) as compliance_rate'),
                        DB::raw('COALESCE(AVG(case_details.actual_cost), 0) as avg_actual_cost'),
                        DB::raw('COALESCE(AVG(pathway_steps.estimated_cost * COALESCE(pathway_steps.quantity, 1)), 0) as standard_cost'),
                        DB::raw('COALESCE(AVG(case_details.actual_cost - (pathway_steps.estimated_cost * COALESCE(pathway_steps.quantity, 1))), 0) as avg_variance')
                    )
                    ->join('patient_cases', 'case_details.patient_case_id', '=', 'patient_cases.id')
                    ->join('pathway_steps', 'case_details.pathway_step_id', '=', 'pathway_steps.id')
                    ->whereIn('patient_cases.id', $caseIds)
                    ->where('patient_cases.clinical_pathway_id', $request->pathway_id)
                    ->groupBy('pathway_steps.step_order', 'pathway_steps.description')
                    ->orderBy('pathway_steps.step_order');
                
                $stepAnalysis = $stepAnalysisQuery->get();
            }
                
            return view('reports.pathway_performance', compact('allPathways', 'pathwayMetrics', 'stepAnalysis'));
        } catch (\Exception $e) {
            \Log::error('Pathway performance report error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to load pathway performance report: ' . $e->getMessage());
        }
    }

    /**
     * Show export page with recent exports (stubbed list for now).
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        // No export persistence implemented yet; provide empty collection to the view
        $recentExports = collect([]);
        return view('reports.export', compact('recentExports'));
    }

    /**
     * Handle export generation (stub implementation).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateExport(Request $request)
    {
        $request->validate([
            'export_data' => 'required|array',
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        // In a real implementation, queue an export job and persist metadata
        return redirect()->route('reports.export')->with('status', 'Export generation queued successfully.');
    }

    /**
     * Download a generated export (stub).
     *
     * @param  mixed  $export
     * @return \Illuminate\Http\Response
     */
    public function downloadExport($export)
    {
        // No persistence layer for exports yet; fail gracefully
        return redirect()->route('reports.export')->with('error', 'Export not found or not ready yet.');
    }

    /**
     * Export report to PDF.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($type)
    {
        try {
            // In a real implementation, you would generate a PDF here
            // For now, we'll just return a response
            return response()->download(resource_path('dummy-report.pdf'), "{$type}_report.pdf");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export PDF report: ' . $e->getMessage());
        }
    }

    /**
     * Export report to Excel.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function exportExcel($type)
    {
        try {
            // In a real implementation, you would generate an Excel file here
            // For now, we'll just return a response
            return response()->download(resource_path('dummy-report.xlsx'), "{$type}_report.xlsx");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export Excel report: ' . $e->getMessage());
        }
    }
}
