<?php

namespace App\Http\Controllers\CostingProcess;

use App\Http\Controllers\Controller;
use App\Models\GlExpense;
use App\Models\CostCenter;
use App\Models\ExpenseCategory;
use App\Models\DriverStatistic;
use App\Models\AllocationDriver;
use App\Models\ServiceVolume;
use App\Models\CostReference;
use App\Models\TariffClass;
use App\Models\AllocationMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreAllocationCheckController extends Controller
{
    public function glCompleteness(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $hospitalId = hospital('id');
        
        // Get all active cost centers and expense categories
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $expenseCategories = ExpenseCategory::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('account_name')
            ->get();
        
        // Get existing GL expenses for the period
        $existingGlExpenses = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->get()
            ->map(function ($expense) {
                return $expense->cost_center_id . '-' . $expense->expense_category_id;
            })
            ->toArray();
        
        // Find missing combinations
        $missingEntries = [];
        $totalExpected = 0;
        $totalFound = count($existingGlExpenses);
        
        foreach ($costCenters as $costCenter) {
            foreach ($expenseCategories as $expenseCategory) {
                $totalExpected++;
                $key = $costCenter->id . '-' . $expenseCategory->id;
                
                if (!in_array($key, $existingGlExpenses)) {
                    $missingEntries[] = [
                        'cost_center' => $costCenter,
                        'expense_category' => $expenseCategory,
                    ];
                }
            }
        }
        
        // Calculate statistics
        $completenessPercentage = $totalExpected > 0 
            ? round(($totalFound / $totalExpected) * 100, 2) 
            : 0;
        
        // Get summary by cost center
        $summaryByCostCenter = DB::table('gl_expenses')
            ->join('cost_centers', 'gl_expenses.cost_center_id', '=', 'cost_centers.id')
            ->where('gl_expenses.hospital_id', $hospitalId)
            ->where('gl_expenses.period_year', $year)
            ->where('gl_expenses.period_month', $month)
            ->select(
                'cost_centers.id',
                'cost_centers.code',
                'cost_centers.name',
                'cost_centers.type',
                DB::raw('COUNT(DISTINCT gl_expenses.expense_category_id) as expense_category_count'),
                DB::raw('SUM(gl_expenses.amount) as total_amount')
            )
            ->groupBy('cost_centers.id', 'cost_centers.code', 'cost_centers.name', 'cost_centers.type')
            ->orderBy('cost_centers.name')
            ->get();
        
        // Get summary by expense category
        $summaryByExpenseCategory = DB::table('gl_expenses')
            ->join('expense_categories', 'gl_expenses.expense_category_id', '=', 'expense_categories.id')
            ->where('gl_expenses.hospital_id', $hospitalId)
            ->where('gl_expenses.period_year', $year)
            ->where('gl_expenses.period_month', $month)
            ->select(
                'expense_categories.id',
                'expense_categories.account_code',
                'expense_categories.account_name',
                DB::raw('COUNT(DISTINCT gl_expenses.cost_center_id) as cost_center_count'),
                DB::raw('SUM(gl_expenses.amount) as total_amount')
            )
            ->groupBy('expense_categories.id', 'expense_categories.account_code', 'expense_categories.account_name')
            ->orderBy('expense_categories.account_name')
            ->get();
        
        // Get available periods
        $availablePeriods = GlExpense::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
        
        return view('costing-process.pre-allocation-check.gl-completeness', compact(
            'year',
            'month',
            'costCenters',
            'expenseCategories',
            'missingEntries',
            'totalExpected',
            'totalFound',
            'completenessPercentage',
            'summaryByCostCenter',
            'summaryByExpenseCategory',
            'availablePeriods'
        ));
    }

    public function driverCompleteness(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $hospitalId = hospital('id');

        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $allocationDrivers = AllocationDriver::where('hospital_id', $hospitalId)
            ->orderBy('name')
            ->get();

        $existingPairs = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->get(['cost_center_id', 'allocation_driver_id'])
            ->map(function ($stat) {
                return $stat->cost_center_id . '-' . $stat->allocation_driver_id;
            })
            ->unique()
            ->toArray();

        $existingLookup = array_flip($existingPairs);

        $missingEntries = [];
        $totalExpected = max($costCenters->count() * $allocationDrivers->count(), 0);
        $totalFound = count($existingLookup);

        if ($totalExpected > 0) {
            foreach ($costCenters as $costCenter) {
                foreach ($allocationDrivers as $driver) {
                    $key = $costCenter->id . '-' . $driver->id;
                    if (!isset($existingLookup[$key])) {
                        $missingEntries[] = [
                            'cost_center' => $costCenter,
                            'allocation_driver' => $driver,
                        ];
                    }
                }
            }
        }

        $completenessPercentage = $totalExpected > 0
            ? round(($totalFound / $totalExpected) * 100, 2)
            : 0;

        $summaryByCostCenter = DB::table('driver_statistics')
            ->join('cost_centers', 'driver_statistics.cost_center_id', '=', 'cost_centers.id')
            ->where('driver_statistics.hospital_id', $hospitalId)
            ->where('driver_statistics.period_year', $year)
            ->where('driver_statistics.period_month', $month)
            ->select(
                'cost_centers.id',
                'cost_centers.code',
                'cost_centers.name',
                'cost_centers.type',
                DB::raw('COUNT(DISTINCT driver_statistics.allocation_driver_id) as driver_count'),
                DB::raw('SUM(driver_statistics.value) as total_value')
            )
            ->groupBy('cost_centers.id', 'cost_centers.code', 'cost_centers.name', 'cost_centers.type')
            ->orderBy('cost_centers.name')
            ->get();

        $summaryByAllocationDriver = DB::table('driver_statistics')
            ->join('allocation_drivers', 'driver_statistics.allocation_driver_id', '=', 'allocation_drivers.id')
            ->where('driver_statistics.hospital_id', $hospitalId)
            ->where('driver_statistics.period_year', $year)
            ->where('driver_statistics.period_month', $month)
            ->select(
                'allocation_drivers.id',
                'allocation_drivers.name',
                'allocation_drivers.unit_measurement',
                DB::raw('COUNT(DISTINCT driver_statistics.cost_center_id) as cost_center_count'),
                DB::raw('SUM(driver_statistics.value) as total_value')
            )
            ->groupBy('allocation_drivers.id', 'allocation_drivers.name', 'allocation_drivers.unit_measurement')
            ->orderBy('allocation_drivers.name')
            ->get();

        $availablePeriods = DriverStatistic::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        return view('costing-process.pre-allocation-check.driver-completeness', compact(
            'year',
            'month',
            'costCenters',
            'allocationDrivers',
            'missingEntries',
            'totalExpected',
            'totalFound',
            'completenessPercentage',
            'summaryByCostCenter',
            'summaryByAllocationDriver',
            'availablePeriods'
        ));
    }

    public function serviceVolumeCompleteness(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $hospitalId = hospital('id');

        $costReferences = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_code')
            ->get();

        $tariffClasses = TariffClass::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $hasTariffClasses = $tariffClasses->count() > 0;
        $expectedTariffTargets = $hasTariffClasses ? $tariffClasses : collect([null]);
        $expectedTariffCount = $hasTariffClasses ? $tariffClasses->count() : 1;

        $existingPairs = ServiceVolume::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->get(['cost_reference_id', 'tariff_class_id'])
            ->map(function ($volume) use ($hasTariffClasses) {
                if ($hasTariffClasses) {
                    if (!$volume->tariff_class_id) {
                        return null;
                    }

                    return $volume->cost_reference_id . '-' . $volume->tariff_class_id;
                }

                return $volume->cost_reference_id . '-default';
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $existingLookup = array_flip($existingPairs);

        $missingEntries = [];
        $totalExpected = $costReferences->count() * $expectedTariffCount;
        $totalFound = count($existingPairs);

        if ($totalExpected > 0) {
            foreach ($costReferences as $costReference) {
                if (!$hasTariffClasses) {
                    $key = $costReference->id . '-default';

                    if (!isset($existingLookup[$key])) {
                        $missingEntries[] = [
                            'cost_reference' => $costReference,
                            'tariff_class' => null,
                        ];
                    }

                    continue;
                }

                foreach ($expectedTariffTargets as $tariffClass) {
                    $key = $costReference->id . '-' . $tariffClass->id;

                    if (!isset($existingLookup[$key])) {
                        $missingEntries[] = [
                            'cost_reference' => $costReference,
                            'tariff_class' => $tariffClass,
                        ];
                    }
                }
            }
        }

        $completenessPercentage = $totalExpected > 0
            ? round(($totalFound / $totalExpected) * 100, 2)
            : 0;

        $summaryByService = DB::table('service_volumes')
            ->join('cost_references', 'service_volumes.cost_reference_id', '=', 'cost_references.id')
            ->where('service_volumes.hospital_id', $hospitalId)
            ->where('service_volumes.period_year', $year)
            ->where('service_volumes.period_month', $month)
            ->select(
                'cost_references.id',
                'cost_references.service_code',
                'cost_references.service_description',
                DB::raw('COUNT(DISTINCT COALESCE(service_volumes.tariff_class_id, 0)) as tariff_class_count'),
                DB::raw('SUM(service_volumes.total_quantity) as total_quantity')
            )
            ->groupBy('cost_references.id', 'cost_references.service_code', 'cost_references.service_description')
            ->orderBy('cost_references.service_code')
            ->get();

        $summaryByTariffClass = DB::table('service_volumes')
            ->leftJoin('tariff_classes', 'service_volumes.tariff_class_id', '=', 'tariff_classes.id')
            ->where('service_volumes.hospital_id', $hospitalId)
            ->where('service_volumes.period_year', $year)
            ->where('service_volumes.period_month', $month)
            ->select(
                DB::raw('COALESCE(tariff_classes.id, 0) as class_id'),
                DB::raw('COALESCE(tariff_classes.name, \'Tanpa Kelas / General\') as class_name'),
                DB::raw('COUNT(DISTINCT service_volumes.cost_reference_id) as service_count'),
                DB::raw('SUM(service_volumes.total_quantity) as total_quantity')
            )
            ->groupBy('class_id', 'class_name')
            ->orderBy('class_name')
            ->get();

        $availablePeriods = ServiceVolume::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        return view('costing-process.pre-allocation-check.service-volume-completeness', compact(
            'year',
            'month',
            'costReferences',
            'tariffClasses',
            'missingEntries',
            'totalExpected',
            'totalFound',
            'completenessPercentage',
            'summaryByService',
            'summaryByTariffClass',
            'availablePeriods',
            'hasTariffClasses',
            'expectedTariffCount'
        ));
    }

    public function mappingValidation(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $hospitalId = hospital('id');

        $supportCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->where('type', 'support')
            ->orderBy('name')
            ->get();

        $revenueCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->where('type', 'revenue')
            ->orderBy('name')
            ->get();

        $mappedSupportIds = AllocationMap::where('hospital_id', $hospitalId)
            ->pluck('source_cost_center_id')
            ->unique();

        $supportCentersWithoutAllocation = $supportCenters
            ->filter(fn ($center) => !$mappedSupportIds->contains($center->id))
            ->values();

        $costReferenceCounts = CostReference::where('hospital_id', $hospitalId)
            ->select('cost_center_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('cost_center_id')
            ->groupBy('cost_center_id')
            ->pluck('total', 'cost_center_id');

        $revenueCentersWithoutServices = $revenueCenters
            ->filter(fn ($center) => ($costReferenceCounts[$center->id] ?? 0) === 0)
            ->values();

        $unmappedCostReferences = CostReference::where('hospital_id', $hospitalId)
            ->where(function ($query) {
                $query->whereNull('cost_center_id')
                    ->orWhereNull('expense_category_id');
            })
            ->with(['costCenter', 'expenseCategory'])
            ->orderBy('service_code')
            ->get();

        $allocationDrivers = AllocationDriver::where('hospital_id', $hospitalId)
            ->orderBy('name')
            ->get();

        $driverUsageCounts = AllocationMap::where('hospital_id', $hospitalId)
            ->select('allocation_driver_id', DB::raw('COUNT(*) as total'))
            ->groupBy('allocation_driver_id')
            ->pluck('total', 'allocation_driver_id');

        $unusedDrivers = $allocationDrivers
            ->filter(fn ($driver) => ($driverUsageCounts[$driver->id] ?? 0) === 0)
            ->values();

        $allocationMaps = AllocationMap::where('hospital_id', $hospitalId)
            ->with(['sourceCostCenter', 'allocationDriver'])
            ->orderBy('step_sequence')
            ->get();

        $driverStatsCounts = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->select('allocation_driver_id', DB::raw('COUNT(*) as total'))
            ->groupBy('allocation_driver_id')
            ->pluck('total', 'allocation_driver_id');

        $mapsMissingDriverStats = $allocationMaps
            ->filter(fn ($map) => ($driverStatsCounts[$map->allocation_driver_id] ?? 0) === 0)
            ->values();

        $availableDriverPeriods = DriverStatistic::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        $summaryStats = [
            'support_without_allocation' => $supportCentersWithoutAllocation->count(),
            'revenue_without_services' => $revenueCentersWithoutServices->count(),
            'unmapped_cost_references' => $unmappedCostReferences->count(),
            'allocation_driver_issues' => $unusedDrivers->count() + $mapsMissingDriverStats->count(),
        ];

        return view('costing-process.pre-allocation-check.mapping-validation', compact(
            'year',
            'month',
            'supportCentersWithoutAllocation',
            'revenueCentersWithoutServices',
            'unmappedCostReferences',
            'unusedDrivers',
            'mapsMissingDriverStats',
            'availableDriverPeriods',
            'summaryStats'
        ));
    }
}

