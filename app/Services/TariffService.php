<?php

namespace App\Services;

use App\Models\FinalTariff;
use App\Models\UnitCostCalculation;
use App\Models\CostReference;
use App\Models\JknCbgCode;
use Illuminate\Support\Facades\DB;

class TariffService
{
    /**
     * Calculate final tariff price from base unit cost and margin
     *
     * @param float $baseUnitCost
     * @param float $marginPercentage
     * @param float $jasaSarana
     * @param float $jasaPelayanan
     * @return float
     */
    public function calculateTariff($baseUnitCost, $marginPercentage, $jasaSarana = 0, $jasaPelayanan = 0)
    {
        $marginAmount = $baseUnitCost * $marginPercentage;
        $finalPrice = $baseUnitCost + $marginAmount + $jasaSarana + $jasaPelayanan;
        
        return round($finalPrice, 2);
    }

    /**
     * Get active tariff for a specific service
     *
     * @param int $costReferenceId
     * @param int|null $tariffClassId
     * @param string|null $date
     * @param int|null $hospitalId
     * @return FinalTariff|null
     */
    public function getActiveTariff($costReferenceId, $tariffClassId = null, $date = null, $hospitalId = null)
    {
        $hospitalId = $hospitalId ?? hospital('id');
        $date = $date ?? now();

        $query = FinalTariff::where('hospital_id', $hospitalId)
            ->where('cost_reference_id', $costReferenceId)
            ->where('effective_date', '<=', $date)
            ->where(function($q) use ($date) {
                $q->whereNull('expired_date')
                  ->orWhere('expired_date', '>=', $date);
            });

        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }

        return $query->orderBy('effective_date', 'desc')->first();
    }

    /**
     * Get tariff history for a specific service
     *
     * @param int $costReferenceId
     * @param int|null $tariffClassId
     * @param int|null $hospitalId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTariffHistory($costReferenceId, $tariffClassId = null, $hospitalId = null)
    {
        $hospitalId = $hospitalId ?? hospital('id');

        $query = FinalTariff::where('hospital_id', $hospitalId)
            ->where('cost_reference_id', $costReferenceId)
            ->with(['costReference', 'tariffClass', 'unitCostCalculation']);

        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }

        return $query->orderBy('effective_date', 'desc')->get();
    }

    /**
     * Compare tariff with INA-CBG (if available)
     *
     * @param int $costReferenceId
     * @param FinalTariff $finalTariff
     * @return array
     */
    public function compareWithInaCbg($costReferenceId, $finalTariff)
    {
        $costReference = CostReference::find($costReferenceId);
        
        if (!$costReference) {
            return [
                'has_ina_cbg' => false,
                'difference' => 0,
                'difference_percentage' => 0,
            ];
        }

        // Try to find INA-CBG code linked to this cost reference
        // Search by service code or description
        $jknCbgCode = JknCbgCode::where(function($query) use ($costReference) {
                $query->where('code', $costReference->service_code)
                      ->orWhere('description', 'LIKE', '%' . $costReference->service_description . '%');
            })
            ->where('is_active', true)
            ->first();

        if (!$jknCbgCode || !$jknCbgCode->tariff || $jknCbgCode->tariff <= 0) {
            return [
                'has_ina_cbg' => false,
                'difference' => 0,
                'difference_percentage' => 0,
            ];
        }

        $inaCbgTariff = (float) $jknCbgCode->tariff;
        $internalTariff = (float) $finalTariff->final_tariff_price;
        $difference = $internalTariff - $inaCbgTariff;
        $differencePercentage = $inaCbgTariff > 0 ? ($difference / $inaCbgTariff) * 100 : 0;

        return [
            'has_ina_cbg' => true,
            'ina_cbg_tariff' => $inaCbgTariff,
            'internal_tariff' => $internalTariff,
            'difference' => $difference,
            'difference_percentage' => round($differencePercentage, 2),
        ];
    }

    /**
     * Simulate tariffs for unit cost calculations
     *
     * @param string $versionLabel
     * @param float $globalMargin
     * @param array $serviceMargins Array of [cost_reference_id => margin_percentage]
     * @param float $jasaSarana
     * @param float $jasaPelayanan
     * @param int|null $hospitalId
     * @return array
     */
    public function simulateTariffs($versionLabel, $globalMargin, $serviceMargins = [], $jasaSarana = 0, $jasaPelayanan = 0, $hospitalId = null)
    {
        $hospitalId = $hospitalId ?? hospital('id');

        // Get all unit cost calculations for this version
        $unitCosts = UnitCostCalculation::where('hospital_id', $hospitalId)
            ->where('version_label', $versionLabel)
            ->with('costReference')
            ->get();

        $results = [];

        foreach ($unitCosts as $unitCost) {
            $baseUnitCost = (float) $unitCost->total_unit_cost;
            
            // Use service-specific margin if available, otherwise use global margin
            $marginPercentage = $serviceMargins[$unitCost->cost_reference_id] ?? $globalMargin;
            
            $finalPrice = $this->calculateTariff($baseUnitCost, $marginPercentage, $jasaSarana, $jasaPelayanan);
            $marginAmount = $baseUnitCost * $marginPercentage;

            $results[] = [
                'cost_reference_id' => $unitCost->cost_reference_id,
                'service_code' => $unitCost->costReference->service_code ?? '',
                'service_description' => $unitCost->costReference->service_description ?? '',
                'base_unit_cost' => $baseUnitCost,
                'margin_percentage' => $marginPercentage,
                'margin_amount' => $marginAmount,
                'jasa_sarana' => $jasaSarana,
                'jasa_pelayanan' => $jasaPelayanan,
                'final_tariff_price' => $finalPrice,
                'unit_cost_calculation_id' => $unitCost->id,
            ];
        }

        return $results;
    }

    /**
     * Check if effective date overlaps with existing tariff
     *
     * @param int $costReferenceId
     * @param string $effectiveDate
     * @param string|null $expiredDate
     * @param int|null $excludeTariffId
     * @param int|null $hospitalId
     * @return bool
     */
    public function hasOverlappingTariff($costReferenceId, $effectiveDate, $expiredDate = null, $excludeTariffId = null, $hospitalId = null)
    {
        $hospitalId = $hospitalId ?? hospital('id');

        $query = FinalTariff::where('hospital_id', $hospitalId)
            ->where('cost_reference_id', $costReferenceId)
            ->where(function($q) use ($effectiveDate, $expiredDate) {
                // Check if new effective date falls within existing tariff period
                $q->where(function($subQ) use ($effectiveDate) {
                    $subQ->where('effective_date', '<=', $effectiveDate)
                         ->where(function($expQ) use ($effectiveDate) {
                             $expQ->whereNull('expired_date')
                                  ->orWhere('expired_date', '>=', $effectiveDate);
                         });
                });

                // Check if new expired date (if exists) overlaps with existing tariff
                if ($expiredDate) {
                    $q->orWhere(function($subQ) use ($expiredDate) {
                        $subQ->where('effective_date', '<=', $expiredDate)
                             ->where(function($expQ) use ($expiredDate) {
                                 $expQ->whereNull('expired_date')
                                      ->orWhere('expired_date', '>=', $expiredDate);
                             });
                    });
                }
            });

        if ($excludeTariffId) {
            $query->where('id', '!=', $excludeTariffId);
        }

        return $query->exists();
    }

    /**
     * Get available unit cost versions
     *
     * @param int|null $hospitalId
     * @return array
     */
    public function getAvailableVersions($hospitalId = null)
    {
        $hospitalId = $hospitalId ?? hospital('id');

        return UnitCostCalculation::where('hospital_id', $hospitalId)
            ->select('version_label', DB::raw('MAX(created_at) as latest_date'))
            ->groupBy('version_label')
            ->orderBy('latest_date', 'desc')
            ->pluck('version_label')
            ->toArray();
    }
}

