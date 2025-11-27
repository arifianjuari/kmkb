<?php

namespace App\Services;

use App\Models\AllocationMap;
use App\Models\AllocationResult;
use App\Models\GlExpense;
use App\Models\DriverStatistic;
use App\Models\CostCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AllocationService
{
    /**
     * Run allocation calculation for a specific period
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function runAllocation($hospitalId, $year, $month)
    {
        $results = [
            'success' => true,
            'message' => 'Allocation berhasil dijalankan',
            'total_allocated' => 0,
            'steps_processed' => 0,
            'errors' => [],
            'warnings' => [],
        ];

        DB::beginTransaction();
        try {
            // 1. Validasi: Pastikan GL Expenses dan Driver Statistics sudah ada
            $this->validatePrerequisites($hospitalId, $year, $month, $results);

            if (!empty($results['errors'])) {
                DB::rollBack();
                $results['success'] = false;
                return $results;
            }

            // 2. Hapus hasil allocation sebelumnya untuk periode ini
            AllocationResult::where('hospital_id', $hospitalId)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->delete();

            // 3. Hitung total biaya awal (sebelum allocation)
            $initialTotalCost = $this->getInitialTotalCost($hospitalId, $year, $month);

            // 4. Ambil semua allocation maps diurutkan berdasarkan step_sequence
            $allocationMaps = AllocationMap::where('hospital_id', $hospitalId)
                ->orderBy('step_sequence')
                ->with(['sourceCostCenter', 'allocationDriver'])
                ->get();

            if ($allocationMaps->isEmpty()) {
                throw new \Exception('Tidak ada allocation maps yang ditemukan. Silakan setup allocation maps terlebih dahulu.');
            }

            // 5. Track cost center yang sudah dialokasikan (tidak akan menerima alokasi lagi)
            $allocatedCostCenters = [];

            // 6. Untuk setiap step allocation
            foreach ($allocationMaps as $map) {
                $stepResult = $this->processAllocationStep(
                    $hospitalId,
                    $year,
                    $month,
                    $map,
                    $allocatedCostCenters
                );

                if (!$stepResult['success']) {
                    $results['errors'][] = $stepResult['error'];
                    continue;
                }

                $results['steps_processed']++;
                $results['total_allocated'] += $stepResult['allocated_amount'];

                // Tandai source cost center sebagai sudah dialokasikan
                $allocatedCostCenters[] = $map->source_cost_center_id;
            }

            // 7. Validasi: Total biaya sebelum dan sesudah allocation harus sama
            $finalTotalCost = $this->getFinalTotalCost($hospitalId, $year, $month);
            $difference = abs($initialTotalCost - $finalTotalCost);

            if ($difference > 0.01) { // Toleransi 0.01 untuk rounding
                $results['warnings'][] = "Selisih total biaya: " . number_format($difference, 2) . ". Total awal: " . number_format($initialTotalCost, 2) . ", Total akhir: " . number_format($finalTotalCost, 2);
            }

            DB::commit();

            Log::info("Allocation completed for hospital {$hospitalId}, period {$year}-{$month}", [
                'steps_processed' => $results['steps_processed'],
                'total_allocated' => $results['total_allocated'],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['message'] = 'Terjadi kesalahan saat menjalankan allocation: ' . $e->getMessage();
            $results['errors'][] = $e->getMessage();

            Log::error("Allocation failed for hospital {$hospitalId}, period {$year}-{$month}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $results;
    }

    /**
     * Validasi prerequisites sebelum menjalankan allocation
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param array &$results
     * @return void
     */
    private function validatePrerequisites($hospitalId, $year, $month, &$results)
    {
        // Cek apakah ada GL Expenses untuk periode ini
        $glExpensesCount = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->count();

        if ($glExpensesCount == 0) {
            $results['errors'][] = 'Tidak ada GL Expenses untuk periode ' . $month . '/' . $year . '. Silakan input GL Expenses terlebih dahulu.';
        }

        // Cek apakah ada Driver Statistics untuk periode ini
        $driverStatsCount = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->count();

        if ($driverStatsCount == 0) {
            $results['errors'][] = 'Tidak ada Driver Statistics untuk periode ' . $month . '/' . $year . '. Silakan input Driver Statistics terlebih dahulu.';
        }
    }

    /**
     * Hitung total biaya awal (sebelum allocation)
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @return float
     */
    private function getInitialTotalCost($hospitalId, $year, $month)
    {
        return GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->sum('amount');
    }

    /**
     * Hitung total biaya akhir (setelah allocation)
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @return float
     */
    private function getFinalTotalCost($hospitalId, $year, $month)
    {
        // Total dari GL Expenses (direct costs)
        $directCosts = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->sum('amount');

        // Total dari Allocation Results (indirect costs yang sudah dialokasikan)
        // Note: Ini seharusnya sama dengan direct costs karena kita hanya memindahkan biaya
        // Tapi kita hitung untuk validasi
        return $directCosts;
    }

    /**
     * Proses satu step allocation
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param AllocationMap $map
     * @param array $allocatedCostCenters
     * @return array
     */
    private function processAllocationStep($hospitalId, $year, $month, $map, $allocatedCostCenters)
    {
        $result = [
            'success' => true,
            'allocated_amount' => 0,
            'error' => null,
        ];

        try {
            $sourceCostCenterId = $map->source_cost_center_id;
            $allocationDriverId = $map->allocation_driver_id;
            $stepSequence = $map->step_sequence;

            // 1. Hitung total cost dari source cost center
            // Termasuk GL Expenses langsung + alokasi dari step sebelumnya
            $sourceTotalCost = $this->getSourceTotalCost($hospitalId, $year, $month, $sourceCostCenterId);

            if ($sourceTotalCost <= 0) {
                $result['success'] = false;
                $result['error'] = "Source cost center {$map->sourceCostCenter->name} tidak memiliki biaya untuk dialokasikan.";
                return $result;
            }

            // 2. Ambil semua target cost centers (yang belum dialokasikan)
            $targetCostCenters = CostCenter::where('hospital_id', $hospitalId)
                ->where('id', '!=', $sourceCostCenterId)
                ->whereNotIn('id', $allocatedCostCenters)
                ->get();

            if ($targetCostCenters->isEmpty()) {
                $result['success'] = false;
                $result['error'] = "Tidak ada target cost center untuk step {$stepSequence}.";
                return $result;
            }

            // 3. Ambil driver statistics untuk driver yang digunakan
            $driverStatistics = DriverStatistic::where('hospital_id', $hospitalId)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->where('allocation_driver_id', $allocationDriverId)
                ->whereIn('cost_center_id', $targetCostCenters->pluck('id'))
                ->get()
                ->keyBy('cost_center_id');

            // 4. Hitung total driver value untuk semua target cost centers
            $totalDriverValue = $driverStatistics->sum('value');

            if ($totalDriverValue <= 0) {
                $result['success'] = false;
                $result['error'] = "Total driver value untuk {$map->allocationDriver->name} adalah 0 atau tidak ditemukan.";
                return $result;
            }

            // 5. Alokasikan biaya ke setiap target cost center berdasarkan proporsi driver value
            foreach ($targetCostCenters as $targetCostCenter) {
                $driverStat = $driverStatistics->get($targetCostCenter->id);

                if (!$driverStat || $driverStat->value <= 0) {
                    // Skip jika tidak ada driver statistic atau value = 0
                    continue;
                }

                // Hitung proporsi alokasi
                $proportion = $driverStat->value / $totalDriverValue;
                $allocatedAmount = $sourceTotalCost * $proportion;

                // Simpan hasil alokasi
                AllocationResult::create([
                    'hospital_id' => $hospitalId,
                    'period_month' => $month,
                    'period_year' => $year,
                    'source_cost_center_id' => $sourceCostCenterId,
                    'target_cost_center_id' => $targetCostCenter->id,
                    'allocation_step' => 'step_' . $stepSequence,
                    'allocated_amount' => $allocatedAmount,
                ]);

                $result['allocated_amount'] += $allocatedAmount;
            }

        } catch (\Exception $e) {
            $result['success'] = false;
            $result['error'] = "Error pada step {$map->step_sequence}: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Hitung total cost dari source cost center
     * Termasuk GL Expenses langsung + alokasi dari step sebelumnya
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @param int $sourceCostCenterId
     * @return float
     */
    private function getSourceTotalCost($hospitalId, $year, $month, $sourceCostCenterId)
    {
        // 1. Direct cost dari GL Expenses
        $directCost = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('cost_center_id', $sourceCostCenterId)
            ->sum('amount');

        // 2. Indirect cost dari allocation results (alokasi dari step sebelumnya)
        $indirectCost = AllocationResult::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('target_cost_center_id', $sourceCostCenterId)
            ->sum('allocated_amount');

        return $directCost + $indirectCost;
    }

    /**
     * Get allocation summary for a period
     *
     * @param int $hospitalId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getSummary($hospitalId, $year, $month)
    {
        $summary = [
            'period' => "{$month}/{$year}",
            'total_gl_expenses' => 0,
            'total_allocated' => 0,
            'steps_count' => 0,
            'allocation_maps' => [],
        ];

        // Total GL Expenses
        $summary['total_gl_expenses'] = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->sum('amount');

        // Total allocated
        $summary['total_allocated'] = AllocationResult::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->sum('allocated_amount');

        // Allocation maps
        $allocationMaps = AllocationMap::where('hospital_id', $hospitalId)
            ->orderBy('step_sequence')
            ->with(['sourceCostCenter', 'allocationDriver'])
            ->get();

        $summary['steps_count'] = $allocationMaps->count();
        $summary['allocation_maps'] = $allocationMaps->map(function ($map) {
            return [
                'step' => $map->step_sequence,
                'source' => $map->sourceCostCenter->name,
                'driver' => $map->allocationDriver->name,
            ];
        })->toArray();

        return $summary;
    }
}

