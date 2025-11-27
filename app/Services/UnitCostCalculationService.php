<?php

namespace App\Services;

use App\Models\GlExpense;
use App\Models\AllocationResult;
use App\Models\ServiceVolume;
use App\Models\CostReference;
use App\Models\CostCenter;
use App\Models\UnitCostCalculation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitCostCalculationService
{
    /**
     * Calculate unit cost for a specific period and version
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param string $versionLabel
     * @return array
     */
    public function calculateUnitCost($hospitalId, $year, $month, $versionLabel)
    {
        $results = [
            'success' => true,
            'processed' => 0,
            'errors' => [],
            'warnings' => [],
        ];

        DB::beginTransaction();
        try {
            // 1. Get all revenue cost centers
            $revenueCenters = CostCenter::where('hospital_id', $hospitalId)
                ->where('type', 'revenue')
                ->get();

            if ($revenueCenters->isEmpty()) {
                throw new \Exception('No revenue cost centers found for this hospital');
            }

            // 2. Get all cost references that belong to revenue centers
            $costReferences = CostReference::where('hospital_id', $hospitalId)
                ->whereNotNull('cost_center_id')
                ->whereIn('cost_center_id', $revenueCenters->pluck('id'))
                ->get();

            if ($costReferences->isEmpty()) {
                throw new \Exception('No cost references found for revenue centers');
            }

            // 3. Process each cost reference
            foreach ($costReferences as $costRef) {
                try {
                    $this->calculateForCostReference(
                        $hospitalId,
                        $year,
                        $month,
                        $versionLabel,
                        $costRef
                    );
                    $results['processed']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Error processing {$costRef->service_code}: " . $e->getMessage();
                    Log::error("Unit cost calculation error for cost reference {$costRef->id}: " . $e->getMessage());
                }
            }

            DB::commit();
            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['errors'][] = $e->getMessage();
            Log::error("Unit cost calculation failed: " . $e->getMessage());
            return $results;
        }
    }

    /**
     * Calculate unit cost for a specific cost reference
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param string $versionLabel
     * @param CostReference $costRef
     * @return void
     */
    private function calculateForCostReference($hospitalId, $year, $month, $versionLabel, $costRef)
    {
        $costCenterId = $costRef->cost_center_id;
        if (!$costCenterId) {
            throw new \Exception("Cost reference {$costRef->service_code} has no cost center");
        }

        // 1. Get Direct Cost Material (BHP Medis + BHP Non Medis)
        $directMaterial = $this->getDirectCostMaterial($hospitalId, $year, $month, $costCenterId);

        // 2. Get Direct Cost Labor (Gaji)
        $directLabor = $this->getDirectCostLabor($hospitalId, $year, $month, $costCenterId);

        // 3. Get Indirect Cost Overhead (from allocation results)
        $indirectOverhead = $this->getIndirectCostOverhead($hospitalId, $year, $month, $costCenterId);

        // 4. Get Service Volume
        $serviceVolume = $this->getServiceVolume($hospitalId, $year, $month, $costRef->id);

        if ($serviceVolume <= 0) {
            throw new \Exception("Service volume is zero or not found for {$costRef->service_code}");
        }

        // 5. Calculate unit costs
        $totalCost = $directMaterial + $directLabor + $indirectOverhead;
        $unitCostMaterial = $directMaterial / $serviceVolume;
        $unitCostLabor = $directLabor / $serviceVolume;
        $unitCostOverhead = $indirectOverhead / $serviceVolume;
        $totalUnitCost = $totalCost / $serviceVolume;

        // 6. Save or update unit cost calculation
        UnitCostCalculation::updateOrCreate(
            [
                'hospital_id' => $hospitalId,
                'period_year' => $year,
                'period_month' => $month,
                'cost_reference_id' => $costRef->id,
                'version_label' => $versionLabel,
            ],
            [
                'direct_cost_material' => round($unitCostMaterial, 2),
                'direct_cost_labor' => round($unitCostLabor, 2),
                'indirect_cost_overhead' => round($unitCostOverhead, 2),
                'total_unit_cost' => round($totalUnitCost, 2),
            ]
        );
    }

    /**
     * Get direct cost material (BHP Medis + BHP Non Medis)
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param int $costCenterId
     * @return float
     */
    private function getDirectCostMaterial($hospitalId, $year, $month, $costCenterId)
    {
        return GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('cost_center_id', $costCenterId)
            ->whereHas('expenseCategory', function($query) {
                $query->whereIn('allocation_category', ['bhp_medis', 'bhp_non_medis']);
            })
            ->sum('amount');
    }

    /**
     * Get direct cost labor (Gaji)
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param int $costCenterId
     * @return float
     */
    private function getDirectCostLabor($hospitalId, $year, $month, $costCenterId)
    {
        return GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('cost_center_id', $costCenterId)
            ->whereHas('expenseCategory', function($query) {
                $query->where('allocation_category', 'gaji');
            })
            ->sum('amount');
    }

    /**
     * Get indirect cost overhead (from allocation results)
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param int $costCenterId
     * @return float
     */
    private function getIndirectCostOverhead($hospitalId, $year, $month, $costCenterId)
    {
        return AllocationResult::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('target_cost_center_id', $costCenterId)
            ->sum('allocated_amount');
    }

    /**
     * Get service volume for a cost reference
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param int $costReferenceId
     * @return float
     */
    private function getServiceVolume($hospitalId, $year, $month, $costReferenceId)
    {
        $volume = ServiceVolume::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('cost_reference_id', $costReferenceId)
            ->sum('total_quantity');

        return (float) $volume;
    }
}


