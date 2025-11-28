<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicalPathway;
use App\Models\PatientCase;
use App\Models\CostCenter;
use App\Models\AllocationResult;
use App\Models\GlExpense;
use App\Models\UnitCostCalculation;
use App\Models\CostReference;
use App\Models\ServiceVolume;
use App\Models\PathwayStep;
use App\Models\CaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard summary data.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load dashboard summary: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get dashboard trends data.
     *
     * @return \Illuminate\Http\Response
     */
    public function trends()
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load dashboard trends: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get overview tab data.
     */
    public function overview(Request $request)
    {
        try {
            $hospitalId = hospital('id');
            
            if (!$hospitalId) {
                return response()->json(['error' => 'Hospital context tidak ditemukan'], 400);
            }
            
            $period = $request->get('period', date('Y-m'));
            [$year, $month] = explode('-', $period);
            $payerType = $request->get('payerType', 'all');
            $kelasRawat = $request->get('kelasRawat', 'all');

            $query = PatientCase::where('patient_cases.hospital_id', $hospitalId)
                ->whereYear('patient_cases.admission_date', $year)
                ->whereMonth('patient_cases.admission_date', $month);

            if ($payerType === 'jkn') {
                $query->whereNotNull('patient_cases.ina_cbg_code');
            } elseif ($payerType === 'non_jkn') {
                $query->whereNull('patient_cases.ina_cbg_code');
            }

            // KPI Summary
            $totalCost = $query->sum('patient_cases.actual_total_cost');
            $avgSelisihCbg = (clone $query)->whereNotNull('patient_cases.ina_cbg_code')
                ->selectRaw('AVG(patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) as avg_selisih')
                ->first()->avg_selisih ?? 0;
            $avgCompliance = $query->avg('patient_cases.compliance_percentage') ?? 0;
            $avgCostVariance = $query->avg('patient_cases.cost_variance') ?? 0;

            $kpis = [
                [
                    'id' => 1,
                    'label' => 'Total Biaya (Actual Cost)',
                    'value' => 'Rp ' . number_format($totalCost, 0, ',', '.'),
                    'change' => null,
                    'color' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300',
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'action' => 'biaya_tarif'
                ],
                [
                    'id' => 2,
                    'label' => 'Rata-rata Selisih vs INA-CBG',
                    'value' => 'Rp ' . number_format($avgSelisihCbg, 0, ',', '.'),
                    'change' => null,
                    'color' => $avgSelisihCbg >= 0 ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300',
                    'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                    'action' => 'variance_jkn'
                ],
                [
                    'id' => 3,
                    'label' => 'Pathway Compliance Overall',
                    'value' => number_format($avgCompliance, 2) . '%',
                    'change' => null,
                    'color' => $avgCompliance >= 80 ? 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300' : ($avgCompliance >= 50 ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300'),
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'action' => 'pathway_mutu'
                ],
                [
                    'id' => 4,
                    'label' => 'Cost Variance Overall',
                    'value' => number_format($avgCostVariance, 2) . '%',
                    'change' => null,
                    'color' => $avgCostVariance >= 0 ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300',
                    'icon' => 'M12 6v12m6-6H6',
                    'action' => 'variance_jkn'
                ]
            ];

            // Cost vs INA-CBG Chart (last 6 months)
            $costVsCbg = $this->getCostVsCbgChart($hospitalId, $year, $month);

            // Compliance vs LOS Chart (top 5 pathways by volume)
            $complianceVsLos = $this->getComplianceVsLosChart($hospitalId, $year, $month);

            // Top 5 Pathways
            $topPathways = $this->getTopPathways($hospitalId, $year, $month, 5);

            return response()->json([
                'kpis' => $kpis,
                'costVsCbg' => $costVsCbg,
                'complianceVsLos' => $complianceVsLos,
                'topPathways' => $topPathways
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load overview: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get biaya & tarif tab data.
     */
    public function biayaTarif(Request $request)
    {
        try {
            $hospitalId = hospital('id');
            
            if (!$hospitalId) {
                return response()->json(['error' => 'Hospital context tidak ditemukan'], 400);
            }
            
            $period = $request->get('period', date('Y-m'));
            [$year, $month] = explode('-', $period);
            $tarifFilter = $request->get('tarifFilter', 'all');
            $selectedServices = $request->get('services', []); // Layanan yang dipilih user

            // Top Cost Centers
            $topCostCenters = $this->getTopCostCenters($hospitalId, $year, $month);

            // Unit Cost Trend - dengan filter layanan jika dipilih
            $unitCostTrend = $this->getUnitCostTrend($hospitalId, $year, $month, $selectedServices);

            // Tarif Internal vs Unit Cost (Non-JKN)
            $tarifVsUnitCost = $this->getTarifVsUnitCost($hospitalId, $year, $month);
            
            // Filter berdasarkan status jika diperlukan
            if ($tarifFilter === 'defisit') {
                $tarifVsUnitCost = array_filter($tarifVsUnitCost, fn($item) => $item['status'] === 'Defisit');
            } elseif ($tarifFilter === 'surplus') {
                $tarifVsUnitCost = array_filter($tarifVsUnitCost, fn($item) => $item['status'] === 'Surplus');
            }
            $tarifVsUnitCost = array_values($tarifVsUnitCost); // Re-index array

            // Unit Cost vs INA-CBG (JKN)
            $unitCostVsCbg = $this->getUnitCostVsCbg($hospitalId, $year, $month);

            // Available services for trend
            $availableServices = CostReference::where('hospital_id', $hospitalId)
                ->whereNotNull('cost_center_id')
                ->select('id', 'service_description as name')
                ->limit(20)
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name ?? 'Service ' . $item->id])
                ->toArray();

            return response()->json([
                'topCostCenters' => $topCostCenters,
                'unitCostTrend' => $unitCostTrend,
                'tarifVsUnitCost' => $tarifVsUnitCost,
                'unitCostVsCbg' => $unitCostVsCbg,
                'availableServices' => $availableServices,
                'tarifFilter' => $tarifFilter
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load biaya tarif: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get pathway & mutu tab data.
     */
    public function pathwayMutu(Request $request)
    {
        try {
            $hospitalId = hospital('id');
            
            if (!$hospitalId) {
                return response()->json(['error' => 'Hospital context tidak ditemukan'], 400);
            }
            
            $period = $request->get('period', date('Y-m'));
            [$year, $month] = explode('-', $period);
            $pathwayId = $request->get('pathway');

            // Compliance Chart
            $compliance = $this->getPathwayCompliance($hospitalId, $year, $month, $pathwayId);

            // LOS Chart
            $los = $this->getPathwayLos($hospitalId, $year, $month, $pathwayId);

            // Summary Table
            $summary = $this->getPathwaySummary($hospitalId, $year, $month, $pathwayId);

            // Non-compliant Steps
            $nonCompliantSteps = $this->getNonCompliantSteps($hospitalId, $year, $month, $pathwayId);

            // Available pathways
            $availablePathways = ClinicalPathway::where('hospital_id', $hospitalId)
                ->select('id', 'name')
                ->get()
                ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
                ->toArray();

            return response()->json([
                'compliance' => $compliance,
                'los' => $los,
                'summary' => $summary,
                'nonCompliantSteps' => $nonCompliantSteps,
                'availablePathways' => $availablePathways
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load pathway mutu: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get variance & JKN tab data.
     */
    public function varianceJkn(Request $request)
    {
        try {
            $hospitalId = hospital('id');
            $period = $request->get('period', date('Y-m'));
            [$year, $month] = explode('-', $period);
            $varianceType = $request->get('varianceType', 'actual_vs_inacbg');

            $query = PatientCase::where('patient_cases.hospital_id', $hospitalId)
                ->whereYear('patient_cases.admission_date', $year)
                ->whereMonth('patient_cases.admission_date', $month)
                ->whereNotNull('patient_cases.ina_cbg_code');

            // Distribution Chart
            $distribution = $this->getVarianceDistribution($hospitalId, $year, $month, $varianceType);

            // KPIs
            $casesHighVariance = (clone $query)->whereRaw('((actual_total_cost - ina_cbg_tariff) / ina_cbg_tariff * 100) > 20')->count();
            $casesLowVariance = (clone $query)->whereRaw('((actual_total_cost - ina_cbg_tariff) / ina_cbg_tariff * 100) < -10')->count();
            $totalDefisit = (clone $query)->whereRaw('actual_total_cost > ina_cbg_tariff')
                ->selectRaw('SUM(actual_total_cost - ina_cbg_tariff) as total')
                ->first()->total ?? 0;
            $avgDefisit = (clone $query)->whereRaw('actual_total_cost > ina_cbg_tariff')
                ->selectRaw('AVG(actual_total_cost - ina_cbg_tariff) as avg')
                ->first()->avg ?? 0;

            $kpis = [
                [
                    'id' => 1,
                    'label' => 'Kasus Variance > +20%',
                    'value' => $casesHighVariance,
                    'color' => 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300'
                ],
                [
                    'id' => 2,
                    'label' => 'Kasus Variance < -10%',
                    'value' => $casesLowVariance,
                    'color' => 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300'
                ],
                [
                    'id' => 3,
                    'label' => 'Total Defisit INA-CBG',
                    'value' => 'Rp ' . number_format($totalDefisit, 0, ',', '.'),
                    'color' => 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300'
                ],
                [
                    'id' => 4,
                    'label' => 'Rata-rata Defisit/Kasus',
                    'value' => 'Rp ' . number_format($avgDefisit, 0, ',', '.'),
                    'color' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-300'
                ]
            ];

            // Top 10 Cases
            $topCases = $this->getTopDeficitCases($hospitalId, $year, $month, 10);

            // By Pathway
            $byPathway = $this->getVarianceByPathway($hospitalId, $year, $month);

            return response()->json([
                'distribution' => $distribution,
                'kpis' => $kpis,
                'topCases' => $topCases,
                'byPathway' => $byPathway
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load variance JKN: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get data & proses tab data.
     */
    public function dataProses()
    {
        try {
            $hospitalId = hospital('id');

            // Status cards
            $status = [
                'gl' => $this->getGlStatus($hospitalId),
                'allocation' => $this->getAllocationStatus($hospitalId),
                'unitCost' => $this->getUnitCostStatus($hospitalId),
                'tarif' => $this->getTarifStatus($hospitalId)
            ];

            // Data quality checks
            $checks = $this->getDataQualityChecks($hospitalId);

            // Process logs
            $logs = $this->getProcessLogs($hospitalId, 20);

            return response()->json([
                'status' => $status,
                'checks' => $checks,
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load data proses: ' . $e->getMessage()], 500);
        }
    }

    // Helper methods
    private function getCostVsCbgChart($hospitalId, $year, $month)
    {
        $months = [];
        $actualCosts = [];
        $inaCbgClaims = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($year, $month, 1)->subMonths($i);
            $m = $date->format('Y-m');
            [$y, $m] = explode('-', $m);

            $cases = PatientCase::where('patient_cases.hospital_id', $hospitalId)
                ->whereYear('patient_cases.admission_date', $y)
                ->whereMonth('patient_cases.admission_date', $m)
                ->whereNotNull('patient_cases.ina_cbg_code');

            $months[] = $date->format('M Y');
            $actualCosts[] = $cases->sum('patient_cases.actual_total_cost') ?? 0;
            $inaCbgClaims[] = $cases->sum('patient_cases.ina_cbg_tariff') ?? 0;
        }

        return [
            'labels' => $months,
            'actualCost' => $actualCosts,
            'inaCbgClaim' => $inaCbgClaims
        ];
    }

    private function getComplianceVsLosChart($hospitalId, $year, $month)
    {
        $pathways = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId)
            ->select('clinical_pathways.id', 'clinical_pathways.name')
            ->selectRaw('COALESCE(clinical_pathways.standard_los, 5) as standard_los')
            ->selectRaw('AVG(patient_cases.compliance_percentage) as avg_compliance')
            ->selectRaw('AVG(DATEDIFF(patient_cases.discharge_date, patient_cases.admission_date)) as avg_los')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name', 'clinical_pathways.standard_los')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        if ($pathways->isEmpty()) {
            return [
                'labels' => [],
                'compliance' => [],
                'losStandard' => [],
                'losActual' => []
            ];
        }

        return [
            'labels' => $pathways->pluck('name')->toArray(),
            'compliance' => $pathways->pluck('avg_compliance')->map(fn($v) => round($v ?? 0, 2))->toArray(),
            'losStandard' => $pathways->pluck('standard_los')->map(fn($v) => $v ?? 5)->toArray(),
            'losActual' => $pathways->pluck('avg_los')->map(fn($v) => round($v ?? 0, 1))->toArray()
        ];
    }

    private function getTopPathways($hospitalId, $year, $month, $limit = 5)
    {
        return PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId)
            ->select('clinical_pathways.id', 'clinical_pathways.name')
            ->selectRaw('AVG(patient_cases.compliance_percentage) as compliance')
            ->selectRaw('AVG(patient_cases.actual_total_cost) as avgCost')
            ->selectRaw('AVG(patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) as selisih')
            ->whereNotNull('patient_cases.ina_cbg_code')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                $status = $item->compliance >= 80 ? 'hijau' : ($item->compliance >= 50 ? 'kuning' : 'merah');
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'compliance' => round($item->compliance, 2),
                    'avgCost' => round($item->avgCost, 0),
                    'selisih' => round($item->selisih, 0),
                    'status' => $status
                ];
            })
            ->toArray();
    }

    private function getTopCostCenters($hospitalId, $year, $month)
    {
        // Get total cost per cost center dari AllocationResult (alokasi) + GL Expenses (biaya langsung)
        $allocationCosts = AllocationResult::where('allocation_results.hospital_id', $hospitalId)
            ->where('allocation_results.period_year', $year)
            ->where('allocation_results.period_month', $month)
            ->join('cost_centers', 'allocation_results.target_cost_center_id', '=', 'cost_centers.id')
            ->where('cost_centers.hospital_id', $hospitalId)
            ->select('cost_centers.id', 'cost_centers.name')
            ->selectRaw('SUM(allocation_results.allocated_amount) as allocated_cost')
            ->groupBy('cost_centers.id', 'cost_centers.name')
            ->get()
            ->keyBy('id');

        $glCosts = GlExpense::where('gl_expenses.hospital_id', $hospitalId)
            ->where('gl_expenses.period_year', $year)
            ->where('gl_expenses.period_month', $month)
            ->join('cost_centers', 'gl_expenses.cost_center_id', '=', 'cost_centers.id')
            ->where('cost_centers.hospital_id', $hospitalId)
            ->select('cost_centers.id', 'cost_centers.name')
            ->selectRaw('SUM(gl_expenses.amount) as direct_cost')
            ->groupBy('cost_centers.id', 'cost_centers.name')
            ->get()
            ->keyBy('id');

        // Combine costs
        $combined = [];
        foreach ($allocationCosts as $id => $item) {
            $combined[$id] = [
                'name' => $item->name,
                'total_cost' => ($item->allocated_cost ?? 0) + ($glCosts[$id]->direct_cost ?? 0)
            ];
        }

        foreach ($glCosts as $id => $item) {
            if (!isset($combined[$id])) {
                $combined[$id] = [
                    'name' => $item->name,
                    'total_cost' => $item->direct_cost ?? 0
                ];
            }
        }

        // Sort dan ambil top 10
        usort($combined, function($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });

        $top10 = array_slice($combined, 0, 10);

        if (empty($top10)) {
            return [
                'labels' => [],
                'data' => []
            ];
        }

        return [
            'labels' => array_column($top10, 'name'),
            'data' => array_map(fn($v) => round($v['total_cost'], 0), $top10)
        ];
    }

    private function getUnitCostTrend($hospitalId, $year, $month, $selectedServices = [])
    {
        $months = [];
        $datasets = [];

        // Generate month labels (6 bulan terakhir)
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($year, $month, 1)->subMonths($i);
            $months[] = $date->format('M Y');
        }

        $servicesToProcess = [];

        // Jika user memilih layanan spesifik, gunakan yang dipilih
        if (!empty($selectedServices) && is_array($selectedServices)) {
            $servicesToProcess = array_map('intval', $selectedServices);
        } else {
            // Jika tidak ada pilihan, ambil top 5 berdasarkan volume
            $topServices = ServiceVolume::where('hospital_id', $hospitalId)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->select('cost_reference_id')
                ->selectRaw('SUM(total_quantity) as total_volume')
                ->groupBy('cost_reference_id')
                ->orderByRaw('SUM(total_quantity) DESC')
                ->limit(5)
                ->get();

            $servicesToProcess = $topServices->pluck('cost_reference_id')->toArray();
        }

        if (empty($servicesToProcess)) {
            return [
                'labels' => $months,
                'datasets' => []
            ];
        }

        // Get unit cost trend untuk setiap service
        foreach ($servicesToProcess as $serviceId) {
            $costRef = CostReference::where('hospital_id', $hospitalId)
                ->find($serviceId);
            
            if (!$costRef) continue;

            $serviceData = [];
            foreach ($months as $monthLabel) {
                // Parse month label untuk mendapatkan year dan month
                $date = Carbon::createFromFormat('M Y', $monthLabel);
                $y = $date->year;
                $m = $date->month;

                $unitCost = UnitCostCalculation::where('hospital_id', $hospitalId)
                    ->where('cost_reference_id', $serviceId)
                    ->where('period_year', $y)
                    ->where('period_month', $m)
                    ->value('total_unit_cost');

                $serviceData[] = $unitCost ? round($unitCost, 0) : 0;
            }

            $datasets[] = [
                'label' => $costRef->service_description ?? $costRef->service_code,
                'data' => $serviceData
            ];
        }

        return [
            'labels' => $months,
            'datasets' => $datasets
        ];
    }

    private function getTarifVsUnitCost($hospitalId, $year, $month)
    {
        // Get cost references dengan tarif internal (selling_price_unit) dan unit cost
        $versionLabel = "UC_{$year}_" . str_pad($month, 2, '0', STR_PAD_LEFT);

        $costReferences = CostReference::where('hospital_id', $hospitalId)
            ->whereNotNull('cost_center_id')
            ->whereNotNull('selling_price_unit')
            ->where('selling_price_unit', '>', 0)
            ->get();

        $results = [];

        foreach ($costReferences as $costRef) {
            // Get unit cost dari UnitCostCalculation
            $unitCost = UnitCostCalculation::where('hospital_id', $hospitalId)
                ->where('cost_reference_id', $costRef->id)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->value('total_unit_cost');

            // Jika tidak ada unit cost untuk periode ini, coba ambil yang terbaru
            if (!$unitCost) {
                $unitCost = UnitCostCalculation::where('hospital_id', $hospitalId)
                    ->where('cost_reference_id', $costRef->id)
                    ->orderBy('period_year', 'desc')
                    ->orderBy('period_month', 'desc')
                    ->value('total_unit_cost');
            }

            // Fallback ke standard_cost jika tidak ada unit cost calculation
            if (!$unitCost) {
                $unitCost = $costRef->standard_cost ?? 0;
            }

            $tarifInternal = $costRef->selling_price_unit ?? 0;
            $margin = $tarifInternal - $unitCost;
            $marginPercent = $unitCost > 0 ? ($margin / $unitCost) * 100 : 0;

            // Tentukan status
            if ($margin < 0) {
                $status = 'Defisit';
            } elseif ($marginPercent >= 10) {
                $status = 'Surplus';
            } else {
                $status = 'BEP';
            }

            $results[] = [
                'kode' => $costRef->service_code,
                'nama' => $costRef->service_description ?? $costRef->service_code,
                'unitCost' => round($unitCost, 0),
                'tarifInternal' => round($tarifInternal, 0),
                'margin' => round($margin, 0),
                'marginPercent' => round($marginPercent, 2),
                'status' => $status
            ];
        }

        // Sort by margin (defisit terbesar dulu)
        usort($results, function($a, $b) {
            return $a['margin'] <=> $b['margin'];
        });

        return $results;
    }

    private function getUnitCostVsCbg($hospitalId, $year, $month)
    {
        return PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->whereNotNull('patient_cases.ina_cbg_code')
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId)
            ->select('clinical_pathways.name as pathway')
            ->selectRaw('AVG(patient_cases.actual_total_cost) as avgUnitCost')
            ->selectRaw('AVG(patient_cases.ina_cbg_tariff) as avgCbg')
            ->selectRaw('AVG(patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) as selisih')
            ->selectRaw('AVG((patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) / patient_cases.ina_cbg_tariff * 100) as selisihPercent')
            ->selectRaw('COUNT(*) as volume')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name')
            ->get()
            ->map(fn($item) => [
                'pathway' => $item->pathway,
                'avgUnitCost' => round($item->avgUnitCost, 0),
                'avgCbg' => round($item->avgCbg, 0),
                'selisih' => round($item->selisih, 0),
                'selisihPercent' => round($item->selisihPercent, 2),
                'volume' => $item->volume
            ])
            ->toArray();
    }

    private function getPathwayCompliance($hospitalId, $year, $month, $pathwayId = null)
    {
        $query = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId);

        if ($pathwayId && $pathwayId !== 'all') {
            $query->where('clinical_pathways.id', $pathwayId);
        }

        $data = $query->select('clinical_pathways.name')
            ->selectRaw('AVG(patient_cases.compliance_percentage) as compliance')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name')
            ->get();

        if ($data->isEmpty()) {
            \Log::info('No compliance data found', [
                'hospital_id' => $hospitalId,
                'year' => $year,
                'month' => $month,
                'pathway_id' => $pathwayId
            ]);
        }

        return [
            'labels' => $data->pluck('name')->toArray(),
            'data' => $data->pluck('compliance')->map(fn($v) => round($v ?? 0, 2))->toArray()
        ];
    }

    private function getPathwayLos($hospitalId, $year, $month, $pathwayId = null)
    {
        $query = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId);

        if ($pathwayId && $pathwayId !== 'all') {
            $query->where('clinical_pathways.id', $pathwayId);
        }

        $data = $query->select('clinical_pathways.name', 'clinical_pathways.standard_los')
            ->selectRaw('AVG(DATEDIFF(patient_cases.discharge_date, patient_cases.admission_date)) as avg_los')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name', 'clinical_pathways.standard_los')
            ->get();

        if ($data->isEmpty()) {
            \Log::info('No LOS data found', [
                'hospital_id' => $hospitalId,
                'year' => $year,
                'month' => $month,
                'pathway_id' => $pathwayId
            ]);
        }

        return [
            'labels' => $data->pluck('name')->toArray(),
            'losStandard' => $data->pluck('standard_los')->map(fn($v) => $v ?? 0)->toArray(),
            'losActual' => $data->pluck('avg_los')->map(fn($v) => round($v ?? 0, 1))->toArray()
        ];
    }

    private function getPathwaySummary($hospitalId, $year, $month, $pathwayId = null)
    {
        $query = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId);

        if ($pathwayId && $pathwayId !== 'all') {
            $query->where('clinical_pathways.id', $pathwayId);
        }

        return $query->select('clinical_pathways.name as pathway', 'clinical_pathways.standard_los')
            ->selectRaw('COUNT(*) as jumlahKasus')
            ->selectRaw('AVG(patient_cases.compliance_percentage) as compliance')
            ->selectRaw('AVG(DATEDIFF(patient_cases.discharge_date, patient_cases.admission_date)) as losActual')
            ->selectRaw('AVG(patient_cases.actual_total_cost) as avgCost')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name', 'clinical_pathways.standard_los')
            ->get()
            ->map(function($item) {
                $statusLos = $item->losActual > $item->standard_los ? 'Over' : 'On Target';
                return [
                    'pathway' => $item->pathway,
                    'jumlahKasus' => $item->jumlahKasus,
                    'compliance' => round($item->compliance, 2),
                    'losStandar' => $item->standard_los,
                    'losActual' => round($item->losActual, 1),
                    'avgCost' => round($item->avgCost, 0),
                    'statusLos' => $statusLos
                ];
            })
            ->toArray();
    }

    private function getNonCompliantSteps($hospitalId, $year, $month, $pathwayId = null)
    {
        // Get patient cases untuk periode ini
        $patientCasesQuery = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId);

        if ($pathwayId && $pathwayId !== 'all') {
            $patientCasesQuery->where('clinical_pathways.id', $pathwayId);
        }

        $patientCaseIds = $patientCasesQuery->pluck('patient_cases.id')->toArray();

        if (empty($patientCaseIds)) {
            return [];
        }

        // Get pathway steps untuk pathway yang digunakan
        $pathwayIds = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId)
            ->when($pathwayId && $pathwayId !== 'all', function($q) use ($pathwayId) {
                $q->where('clinical_pathways.id', $pathwayId);
            })
            ->pluck('clinical_pathways.id')
            ->unique()
            ->toArray();
        
        if (empty($pathwayIds)) {
            return [];
        }
        
        $pathwaySteps = PathwayStep::whereIn('clinical_pathway_id', $pathwayIds)
            ->where('hospital_id', $hospitalId)
            ->get();

        if ($pathwaySteps->isEmpty()) {
            return [];
        }

        $results = [];

        foreach ($pathwaySteps as $step) {
            // Get total cases yang menggunakan pathway ini
            $totalCases = PatientCase::where('patient_cases.hospital_id', $hospitalId)
                ->whereYear('patient_cases.admission_date', $year)
                ->whereMonth('patient_cases.admission_date', $month)
                ->where('patient_cases.clinical_pathway_id', $step->clinical_pathway_id)
                ->whereIn('patient_cases.id', $patientCaseIds)
                ->count();

            if ($totalCases === 0) {
                continue;
            }

            // Get cases yang TIDAK melakukan step ini
            // Case dianggap non-compliant jika:
            // 1. Tidak ada case_detail untuk step ini, ATAU
            // 2. Ada case_detail tapi performed = 0
            $compliantCaseIds = CaseDetail::whereIn('patient_case_id', $patientCaseIds)
                ->where('pathway_step_id', $step->id)
                ->where('performed', 1)
                ->pluck('patient_case_id')
                ->unique()
                ->toArray();

            $nonCompliantCases = $totalCases - count($compliantCaseIds);

            // Hitung persentase ketidakpatuhan
            $nonCompliancePercent = $totalCases > 0 ? ($nonCompliantCases / $totalCases) * 100 : 0;

            // Hanya ambil step yang memiliki ketidakpatuhan > 0
            if ($nonCompliancePercent > 0) {
                $pathway = ClinicalPathway::find($step->clinical_pathway_id);
                
                // Tentukan dampak berdasarkan persentase
                $dampak = 'Rendah';
                if ($nonCompliancePercent >= 50) {
                    $dampak = 'Tinggi';
                } elseif ($nonCompliancePercent >= 25) {
                    $dampak = 'Sedang';
                }

                $results[] = [
                    'pathway' => $pathway->name ?? 'Unknown',
                    'stepName' => $step->description ?? $step->service_item ?? 'Step ' . $step->step_order,
                    'nonCompliancePercent' => round($nonCompliancePercent, 2),
                    'dampak' => $dampak
                ];
            }
        }

        // Sort by non-compliance percent (highest first) dan ambil top 10
        usort($results, function($a, $b) {
            return $b['nonCompliancePercent'] <=> $a['nonCompliancePercent'];
        });

        return array_slice($results, 0, 10);
    }

    private function getVarianceDistribution($hospitalId, $year, $month, $varianceType)
    {
        $query = PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->whereNotNull('patient_cases.ina_cbg_code')
            ->selectRaw('((patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) / patient_cases.ina_cbg_tariff * 100) as variance_percent');

        $cases = $query->get();
        
        $buckets = [
            '< -20%' => 0,
            '-20% to 0' => 0,
            '0% to 20%' => 0,
            '20% to 50%' => 0,
            '> 50%' => 0
        ];

        foreach ($cases as $case) {
            $v = $case->variance_percent;
            if ($v < -20) $buckets['< -20%']++;
            elseif ($v < 0) $buckets['-20% to 0']++;
            elseif ($v <= 20) $buckets['0% to 20%']++;
            elseif ($v <= 50) $buckets['20% to 50%']++;
            else $buckets['> 50%']++;
        }

        return [
            'labels' => array_keys($buckets),
            'data' => array_values($buckets)
        ];
    }

    private function getTopDeficitCases($hospitalId, $year, $month, $limit = 10)
    {
        return PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->whereNotNull('patient_cases.ina_cbg_code')
            ->whereRaw('patient_cases.actual_total_cost > patient_cases.ina_cbg_tariff')
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId)
            ->select('patient_cases.id', 'patient_cases.medical_record_number as caseId')
            ->selectRaw('clinical_pathways.name as pathway')
            ->selectRaw('patient_cases.actual_total_cost as actualCost')
            ->selectRaw('patient_cases.ina_cbg_tariff as inaCbg')
            ->selectRaw('(patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) as selisih')
            ->selectRaw('((patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) / patient_cases.ina_cbg_tariff * 100) as selisihPercent')
            ->selectRaw('DATEDIFF(patient_cases.discharge_date, patient_cases.admission_date) as los')
            ->orderByRaw('(patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) DESC')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'caseId' => substr($item->caseId, 0, 8) . '***', // Anonymized
                'pathway' => $item->pathway,
                'kelas' => '-', // Would need kelas data
                'actualCost' => round($item->actualCost, 0),
                'inaCbg' => round($item->inaCbg, 0),
                'selisih' => round($item->selisih, 0),
                'selisihPercent' => round($item->selisihPercent, 2),
                'los' => round($item->los, 0)
            ])
            ->toArray();
    }

    private function getVarianceByPathway($hospitalId, $year, $month)
    {
        return PatientCase::where('patient_cases.hospital_id', $hospitalId)
            ->whereYear('patient_cases.admission_date', $year)
            ->whereMonth('patient_cases.admission_date', $month)
            ->whereNotNull('patient_cases.ina_cbg_code')
            ->join('clinical_pathways', 'patient_cases.clinical_pathway_id', '=', 'clinical_pathways.id')
            ->where('clinical_pathways.hospital_id', $hospitalId)
            ->select('clinical_pathways.name as pathway')
            ->selectRaw('COUNT(*) as jumlahKasus')
            ->selectRaw('AVG(patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) as avgVariance')
            ->selectRaw('AVG((patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) / patient_cases.ina_cbg_tariff * 100) as avgVariancePercent')
            ->selectRaw('SUM(CASE WHEN (patient_cases.actual_total_cost - patient_cases.ina_cbg_tariff) / patient_cases.ina_cbg_tariff * 100 > 20 THEN 1 ELSE 0 END) / COUNT(*) * 100 as percentAboveThreshold')
            ->groupBy('clinical_pathways.id', 'clinical_pathways.name')
            ->get()
            ->map(fn($item) => [
                'pathway' => $item->pathway,
                'jumlahKasus' => $item->jumlahKasus,
                'avgVariance' => round($item->avgVariance, 0),
                'avgVariancePercent' => round($item->avgVariancePercent, 2),
                'percentAboveThreshold' => round($item->percentAboveThreshold, 2)
            ])
            ->toArray();
    }

    private function getGlStatus($hospitalId)
    {
        $lastGl = GlExpense::where('hospital_id', $hospitalId)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->first();

        return [
            'lastImport' => $lastGl ? $lastGl->created_at->format('d M Y') : '-',
            'lastPeriod' => $lastGl ? $lastGl->period_year . '-' . str_pad($lastGl->period_month, 2, '0', STR_PAD_LEFT) : '-',
            'status' => $lastGl ? 'OK' : 'Warning'
        ];
    }

    private function getAllocationStatus($hospitalId)
    {
        $lastAllocation = AllocationResult::where('hospital_id', $hospitalId)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->first();

        return [
            'lastRun' => $lastAllocation ? $lastAllocation->created_at->format('d M Y') : '-',
            'completedPeriod' => $lastAllocation ? $lastAllocation->period_year . '-' . str_pad($lastAllocation->period_month, 2, '0', STR_PAD_LEFT) : '-',
            'driver' => '-', // Would need driver name
            'status' => $lastAllocation ? 'OK' : 'Warning'
        ];
    }

    private function getUnitCostStatus($hospitalId)
    {
        $lastUnitCost = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'activeVersion' => $lastUnitCost ? 'UC_' . $lastUnitCost->period_year . '_' . str_pad($lastUnitCost->period_month, 2, '0', STR_PAD_LEFT) : '-',
            'calculationDate' => $lastUnitCost ? $lastUnitCost->created_at->format('d M Y') : '-',
            'status' => $lastUnitCost ? 'OK' : 'Warning'
        ];
    }

    private function getTarifStatus($hospitalId)
    {
        $activeTariffs = CostReference::where('hospital_id', $hospitalId)
            ->whereNotNull('tariff')
            ->count();

        return [
            'lastUpdate' => '-', // Would need tariff update tracking
            'activeServices' => $activeTariffs,
            'status' => $activeTariffs > 0 ? 'OK' : 'Warning'
        ];
    }

    private function getDataQualityChecks($hospitalId)
    {
        // Placeholder - would need actual pre-allocation check data
        return [
            ['name' => 'Cost center tanpa driver', 'count' => 0, 'status' => 'OK'],
            ['name' => 'Layanan tanpa volume', 'count' => 0, 'status' => 'OK']
        ];
    }

    private function getProcessLogs($hospitalId, $limit = 20)
    {
        // Placeholder - would need actual process log table
        return [];
    }
}
