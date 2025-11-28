<?php

namespace App\Http\Controllers\CostingProcess;

use App\Http\Controllers\Controller;
use App\Models\AllocationResult;
use App\Models\CostCenter;
use App\Models\CostReference;
use App\Models\DriverStatistic;
use App\Models\GlExpense;
use App\Models\ServiceVolume;
use App\Models\UnitCostCalculation;
use App\Services\UnitCostCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitCostController extends Controller
{
    protected UnitCostCalculationService $unitCostCalculationService;

    public function __construct(UnitCostCalculationService $unitCostCalculationService)
    {
        $this->unitCostCalculationService = $unitCostCalculationService;
    }

    public function calculate(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $month = (int) $request->get('month', date('m'));
        $hospitalId = hospital('id');

        $revenueCentersCount = CostCenter::where('hospital_id', $hospitalId)
            ->where('type', 'revenue')
            ->where('is_active', true)
            ->count();

        $serviceCatalogCount = CostReference::where('hospital_id', $hospitalId)
            ->whereNotNull('cost_center_id')
            ->count();

        $glExpenseCount = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->count();

        $allocationResultCount = AllocationResult::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->count();

        $driverStatisticCount = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->count();

        $serviceVolumeCount = ServiceVolume::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->count();

        $readiness = [
            [
                'label' => 'GL Expenses',
                'count' => $glExpenseCount,
                'status' => $glExpenseCount > 0,
                'description' => 'Jumlah baris GL yang siap dipakai sebagai direct cost.',
            ],
            [
                'label' => 'Allocation Results',
                'count' => $allocationResultCount,
                'status' => $allocationResultCount > 0,
                'description' => 'Hasil alokasi overhead untuk periode ini.',
            ],
            [
                'label' => 'Driver Statistics',
                'count' => $driverStatisticCount,
                'status' => $driverStatisticCount > 0,
                'description' => 'Data driver yang menjadi dasar proporsi biaya.',
            ],
            [
                'label' => 'Service Volumes',
                'count' => $serviceVolumeCount,
                'status' => $serviceVolumeCount > 0,
                'description' => 'Volume layanan sebagai penyebut unit cost.',
            ],
        ];

        $existingVersions = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->select(
                'version_label',
                'period_year',
                'period_month',
                DB::raw('COUNT(*) as services'),
                DB::raw('MAX(updated_at) as last_run_at')
            )
            ->groupBy('version_label', 'period_year', 'period_month')
            ->orderByDesc('last_run_at')
            ->limit(10)
            ->get();

        $suggestedVersion = 'UC-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT);

        return view('costing-process.unit-cost.calculate', compact(
            'year',
            'month',
            'readiness',
            'existingVersions',
            'revenueCentersCount',
            'serviceCatalogCount',
            'serviceVolumeCount',
            'allocationResultCount',
            'suggestedVersion'
        ));
    }

    public function runCalculation(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|between:2000,2100',
            'month' => 'required|integer|between:1,12',
            'version_label' => 'required|string|max:100',
        ]);

        $hospitalId = hospital('id');
        $result = $this->unitCostCalculationService->calculateUnitCost(
            $hospitalId,
            (int) $validated['year'],
            (int) $validated['month'],
            $validated['version_label']
        );

        $redirect = redirect()
            ->route('costing-process.unit-cost.calculate', [
                'year' => $validated['year'],
                'month' => $validated['month'],
            ]);

        if ($result['success']) {
            $redirect->with('success', "Unit cost versi {$validated['version_label']} berhasil dihitung untuk {$result['processed']} layanan.");
        } else {
            $redirect->with('error', 'Perhitungan unit cost gagal dijalankan.');
        }

        if (!empty($result['errors'])) {
            $redirect->with('unit_cost_errors', $result['errors']);
        }

        if (!empty($result['warnings'])) {
            $redirect->with('unit_cost_warnings', $result['warnings']);
        }

        return $redirect;
    }

    public function results(Request $request)
    {
        $hospitalId = hospital('id');
        
        $search = $request->get('search');
        $versionLabel = $request->get('version_label');
        $periodYear = $request->get('period_year');
        $periodMonth = $request->get('period_month');
        $costCenterId = $request->get('cost_center_id');
        
        $query = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->with(['costReference.costCenter', 'costReference.expenseCategory']);
        
        if ($versionLabel) {
            $query->where('version_label', $versionLabel);
        }
        
        if ($periodYear) {
            $query->where('period_year', $periodYear);
        }
        
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        
        if ($costCenterId) {
            $query->whereHas('costReference', function($q) use ($costCenterId) {
                $q->where('cost_center_id', $costCenterId);
            });
        }
        
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        $unitCosts = $query->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('version_label', 'desc')
            ->orderBy('total_unit_cost', 'desc')
            ->paginate(50)
            ->appends($request->query());
        
        // Get available versions for filter
        $availableVersions = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->select('version_label')
            ->distinct()
            ->orderBy('version_label', 'desc')
            ->pluck('version_label');
        
        // Get available periods
        $availablePeriods = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
        
        // Get cost centers for filter
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('type', 'revenue')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Summary statistics
        $summaryStats = [
            'total_records' => $unitCosts->total(),
            'total_services' => UnitCostCalculation::where('hospital_id', $hospitalId)
                ->when($versionLabel, fn($q) => $q->where('version_label', $versionLabel))
                ->when($periodYear, fn($q) => $q->where('period_year', $periodYear))
                ->when($periodMonth, fn($q) => $q->where('period_month', $periodMonth))
                ->distinct('cost_reference_id')
                ->count('cost_reference_id'),
            'avg_unit_cost' => UnitCostCalculation::where('hospital_id', $hospitalId)
                ->when($versionLabel, fn($q) => $q->where('version_label', $versionLabel))
                ->when($periodYear, fn($q) => $q->where('period_year', $periodYear))
                ->when($periodMonth, fn($q) => $q->where('period_month', $periodMonth))
                ->avg('total_unit_cost'),
        ];
        
        return view('costing-process.unit-cost.results', compact(
            'unitCosts',
            'search',
            'versionLabel',
            'periodYear',
            'periodMonth',
            'costCenterId',
            'availableVersions',
            'availablePeriods',
            'costCenters',
            'summaryStats'
        ));
    }

    public function compare(Request $request)
    {
        $hospitalId = hospital('id');
        
        $version1 = $request->get('version1');
        $version2 = $request->get('version2');
        $periodYear = $request->get('period_year');
        $periodMonth = $request->get('period_month');
        $costCenterId = $request->get('cost_center_id');
        $search = $request->get('search');
        
        // Get available versions
        $availableVersions = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->select('version_label')
            ->distinct()
            ->orderBy('version_label', 'desc')
            ->pluck('version_label');
        
        // Get cost centers for filter
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('type', 'revenue')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $comparisonData = [];
        $summaryStats = [
            'version1_count' => 0,
            'version2_count' => 0,
            'common_services' => 0,
            'only_in_version1' => 0,
            'only_in_version2' => 0,
            'avg_variance' => 0,
        ];
        
        if ($version1 && $version2 && $version1 !== $version2) {
            // Get data for version 1
            $query1 = UnitCostCalculation::where('hospital_id', $hospitalId)
                ->where('version_label', $version1)
                ->with(['costReference.costCenter', 'costReference.expenseCategory']);
            
            if ($periodYear) {
                $query1->where('period_year', $periodYear);
            }
            
            if ($periodMonth) {
                $query1->where('period_month', $periodMonth);
            }
            
            if ($costCenterId) {
                $query1->whereHas('costReference', function($q) use ($costCenterId) {
                    $q->where('cost_center_id', $costCenterId);
                });
            }
            
            if ($search) {
                $query1->whereHas('costReference', function($q) use ($search) {
                    $q->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            }
            
            $data1 = $query1->get()->keyBy('cost_reference_id');
            
            // Get data for version 2
            $query2 = UnitCostCalculation::where('hospital_id', $hospitalId)
                ->where('version_label', $version2)
                ->with(['costReference.costCenter', 'costReference.expenseCategory']);
            
            if ($periodYear) {
                $query2->where('period_year', $periodYear);
            }
            
            if ($periodMonth) {
                $query2->where('period_month', $periodMonth);
            }
            
            if ($costCenterId) {
                $query2->whereHas('costReference', function($q) use ($costCenterId) {
                    $q->where('cost_center_id', $costCenterId);
                });
            }
            
            if ($search) {
                $query2->whereHas('costReference', function($q) use ($search) {
                    $q->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            }
            
            $data2 = $query2->get()->keyBy('cost_reference_id');
            
            // Combine and compare
            $allServiceIds = $data1->keys()->merge($data2->keys())->unique();
            
            $variances = [];
            foreach ($allServiceIds as $serviceId) {
                $v1 = $data1->get($serviceId);
                $v2 = $data2->get($serviceId);
                
                $comparisonData[] = [
                    'service_id' => $serviceId,
                    'service_code' => $v1 ? ($v1->costReference->service_code ?? '-') : ($v2->costReference->service_code ?? '-'),
                    'service_description' => $v1 ? ($v1->costReference->service_description ?? '-') : ($v2->costReference->service_description ?? '-'),
                    'cost_center' => $v1 ? ($v1->costReference->costCenter->name ?? '-') : ($v2->costReference->costCenter->name ?? '-'),
                    'version1' => $v1 ? [
                        'direct_material' => $v1->direct_cost_material,
                        'direct_labor' => $v1->direct_cost_labor,
                        'overhead' => $v1->indirect_cost_overhead,
                        'total' => $v1->total_unit_cost,
                        'period' => str_pad($v1->period_month, 2, '0', STR_PAD_LEFT) . '/' . $v1->period_year,
                    ] : null,
                    'version2' => $v2 ? [
                        'direct_material' => $v2->direct_cost_material,
                        'direct_labor' => $v2->direct_cost_labor,
                        'overhead' => $v2->indirect_cost_overhead,
                        'total' => $v2->total_unit_cost,
                        'period' => str_pad($v2->period_month, 2, '0', STR_PAD_LEFT) . '/' . $v2->period_year,
                    ] : null,
                ];
                
                if ($v1 && $v2) {
                    $variance = $v2->total_unit_cost - $v1->total_unit_cost;
                    $variancePercent = $v1->total_unit_cost > 0 
                        ? ($variance / $v1->total_unit_cost) * 100 
                        : 0;
                    
                    $comparisonData[count($comparisonData) - 1]['variance'] = [
                        'amount' => $variance,
                        'percent' => $variancePercent,
                    ];
                    
                    $variances[] = abs($variancePercent);
                }
            }
            
            // Calculate summary stats
            $summaryStats['version1_count'] = $data1->count();
            $summaryStats['version2_count'] = $data2->count();
            $summaryStats['common_services'] = $data1->keys()->intersect($data2->keys())->count();
            $summaryStats['only_in_version1'] = $data1->keys()->diff($data2->keys())->count();
            $summaryStats['only_in_version2'] = $data2->keys()->diff($data1->keys())->count();
            $summaryStats['avg_variance'] = count($variances) > 0 ? array_sum($variances) / count($variances) : 0;
        }
        
        // Get available periods
        $availablePeriods = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
        
        return view('costing-process.unit-cost.compare', compact(
            'version1',
            'version2',
            'periodYear',
            'periodMonth',
            'costCenterId',
            'search',
            'availableVersions',
            'costCenters',
            'availablePeriods',
            'comparisonData',
            'summaryStats'
        ));
    }
}
