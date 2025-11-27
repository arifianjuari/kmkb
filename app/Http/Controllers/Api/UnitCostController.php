<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UnitCostService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnitCostController extends Controller
{
    protected $unitCostService;

    public function __construct(UnitCostService $unitCostService)
    {
        $this->unitCostService = $unitCostService;
    }

    /**
     * Get unit cost for a service.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUnitCost(Request $request): JsonResponse
    {
        $request->validate([
            'serviceId' => 'required|exists:cost_references,id',
            'version' => 'nullable|string|max:100',
            'tariffClassId' => 'nullable|exists:tariff_classes,id',
        ]);

        $serviceId = $request->input('serviceId');
        $version = $request->input('version');
        $tariffClassId = $request->input('tariffClassId');

        $result = $this->unitCostService->getUnitCost($serviceId, $version, $tariffClassId);

        return response()->json($result);
    }

    /**
     * Get list of available unit cost versions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getVersions(Request $request): JsonResponse
    {
        $versions = $this->unitCostService->getAvailableVersions();

        return response()->json($versions);
    }

    /**
     * Get unit cost calculations by cost reference ID.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getByCostReference(Request $request): JsonResponse
    {
        $request->validate([
            'cost_reference_id' => 'required|exists:cost_references,id',
        ]);

        $costReferenceId = $request->input('cost_reference_id');
        $hospitalId = hospital('id');

        $unitCosts = \App\Models\UnitCostCalculation::where('hospital_id', $hospitalId)
            ->where('cost_reference_id', $costReferenceId)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'version_label' => $item->version_label,
                    'period_year' => $item->period_year,
                    'period_month' => $item->period_month,
                    'total_unit_cost' => (float) $item->total_unit_cost,
                ];
            });

        return response()->json($unitCosts);
    }

    /**
     * Get single unit cost calculation by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $hospitalId = hospital('id');

        $unitCost = \App\Models\UnitCostCalculation::where('hospital_id', $hospitalId)
            ->findOrFail($id);

        return response()->json([
            'id' => $unitCost->id,
            'version_label' => $unitCost->version_label,
            'period_year' => $unitCost->period_year,
            'period_month' => $unitCost->period_month,
            'cost_reference_id' => $unitCost->cost_reference_id,
            'total_unit_cost' => (float) $unitCost->total_unit_cost,
            'direct_cost_material' => (float) $unitCost->direct_cost_material,
            'direct_cost_labor' => (float) $unitCost->direct_cost_labor,
            'indirect_cost_overhead' => (float) $unitCost->indirect_cost_overhead,
        ]);
    }
}
