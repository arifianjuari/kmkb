<?php

namespace App\Services;

use App\Models\UnitCostCalculation;
use App\Models\CostReference;
use Illuminate\Support\Facades\DB;

class UnitCostService
{
    /**
     * Get unit cost for a service, optionally filtered by version and tariff class.
     *
     * @param int $serviceId Cost reference ID
     * @param string|null $version Version label (if null, get latest)
     * @param int|null $tariffClassId Tariff class ID (optional, for future use)
     * @param int|null $hospitalId Hospital ID (optional, defaults to current hospital)
     * @return array
     */
    public function getUnitCost($serviceId, $version = null, $tariffClassId = null, $hospitalId = null)
    {
        // Get hospital ID from context if not provided
        if ($hospitalId === null) {
            $hospitalId = session('hospital_id', auth()->user()->hospital_id ?? null);
        }

        // Try to get unit cost from unit_cost_calculations
        $query = UnitCostCalculation::where('cost_reference_id', $serviceId);
        
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        if ($version !== null) {
            $query->byVersion($version);
        } else {
            // Get latest version
            $query->latestForCostReference($serviceId);
        }

        $unitCost = $query->first();

        // Get standard cost as fallback
        $costReference = CostReference::find($serviceId);
        $standardCost = $costReference ? (float) $costReference->standard_cost : 0;

        if ($unitCost) {
            return [
                'unit_cost' => (float) $unitCost->total_unit_cost,
                'version_label' => $unitCost->version_label,
                'has_unit_cost' => true,
                'fallback_used' => false,
                'standard_cost' => $standardCost,
                'direct_cost_material' => (float) $unitCost->direct_cost_material,
                'direct_cost_labor' => (float) $unitCost->direct_cost_labor,
                'indirect_cost_overhead' => (float) $unitCost->indirect_cost_overhead,
            ];
        }

        // Fallback to standard cost
        return [
            'unit_cost' => $standardCost,
            'version_label' => null,
            'has_unit_cost' => false,
            'fallback_used' => true,
            'standard_cost' => $standardCost,
            'direct_cost_material' => null,
            'direct_cost_labor' => null,
            'indirect_cost_overhead' => null,
        ];
    }

    /**
     * Get list of available unit cost versions.
     *
     * @param int|null $hospitalId Hospital ID (optional, defaults to current hospital)
     * @return array
     */
    public function getAvailableVersions($hospitalId = null)
    {
        // Get hospital ID from context if not provided
        if ($hospitalId === null) {
            $hospitalId = session('hospital_id', auth()->user()->hospital_id ?? null);
        }

        $query = UnitCostCalculation::select('version_label')
            ->distinct()
            ->orderBy('version_label', 'desc');

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        return $query->pluck('version_label')->toArray();
    }

    /**
     * Get unit cost by version label for multiple services.
     *
     * @param array $serviceIds Array of cost reference IDs
     * @param string|null $version Version label (if null, get latest for each)
     * @param int|null $hospitalId Hospital ID
     * @return array Keyed by service ID
     */
    public function getUnitCostsForServices(array $serviceIds, $version = null, $hospitalId = null)
    {
        $results = [];
        
        foreach ($serviceIds as $serviceId) {
            $results[$serviceId] = $this->getUnitCost($serviceId, $version, null, $hospitalId);
        }

        return $results;
    }
}

