<?php

namespace App\Http\Controllers;

use App\Models\ClinicalPathway;
use App\Models\Hospital;
use App\Models\PatientCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = auth()->user();

        // Superadmin: global overview of hospitals and users
        if ($authUser && $authUser->isSuperadmin()) {
            $totalHospitals = Hospital::count();
            $totalUsers = User::count();
            $usersByRole = User::select('role', DB::raw('COUNT(*) as count'))
                ->groupBy('role')
                ->pluck('count', 'role');
            $recentHospitals = Hospital::latest()->limit(5)->get();
            $recentUsers = User::with('hospital')->latest()->limit(5)->get();

            return view('dashboard.superadmin', compact(
                'totalHospitals',
                'totalUsers',
                'usersByRole',
                'recentHospitals',
                'recentUsers'
            ));
        }

        // Non-superadmin: hospital-scoped clinical metrics
        $hospitalId = $authUser?->hospital_id;

        // Get summary statistics (scoped)
        $totalPathways = ClinicalPathway::where('hospital_id', $hospitalId)->count();
        $totalCases = PatientCase::where('hospital_id', $hospitalId)->count();
        $averageCompliance = PatientCase::where('hospital_id', $hospitalId)->avg('compliance_percentage');
        $totalCostVariance = PatientCase::where('hospital_id', $hospitalId)->sum('cost_variance');
        
        // Get recent cases (scoped)
        $recentCases = PatientCase::with('clinicalPathway')
            ->where('hospital_id', $hospitalId)
            ->latest()
            ->limit(5)
            ->get();
            
        // Get compliance trend (last 30 days, scoped)
        $complianceTrend = PatientCase::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(compliance_percentage) as avg_compliance')
            )
            ->where('hospital_id', $hospitalId)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Get cost variance trend (last 30 days, scoped)
        $costVarianceTrend = PatientCase::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(cost_variance) as total_variance')
            )
            ->where('hospital_id', $hospitalId)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Get cases by pathway (scoped)
        $casesByPathway = PatientCase::select(
                'clinical_pathways.name as pathway_name',
                DB::raw('COUNT(*) as case_count')
            )
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('patient_cases.hospital_id', $hospitalId)
            ->groupBy('clinical_pathways.name')
            ->get();
            
        // Use new multi-tab dashboard view
        return view('dashboard.kmkb');
    }
}
