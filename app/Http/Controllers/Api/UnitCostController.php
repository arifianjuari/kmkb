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
}
