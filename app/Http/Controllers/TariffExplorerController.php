<?php

namespace App\Http\Controllers;

use App\Models\FinalTariff;
use App\Models\CostReference;
use App\Models\TariffClass;
use App\Services\TariffService;
use Illuminate\Http\Request;

class TariffExplorerController extends Controller
{
    protected $tariffService;

    public function __construct(TariffService $tariffService)
    {
        $this->tariffService = $tariffService;
    }

    /**
     * Search and list tariffs
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tariffClassId = $request->get('tariff_class_id');
        $effectiveDate = $request->get('effective_date', date('Y-m-d'));
        $showExpired = $request->get('show_expired', false);
        
        $query = FinalTariff::where('hospital_id', hospital('id'))
            ->with(['costReference', 'tariffClass', 'unitCostCalculation']);
        
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }
        
        // Filter by effective date
        if ($effectiveDate) {
            $query->where('effective_date', '<=', $effectiveDate);
            if (!$showExpired) {
                $query->where(function($q) use ($effectiveDate) {
                    $q->whereNull('expired_date')
                      ->orWhere('expired_date', '>=', $effectiveDate);
                });
            }
        } else {
            if (!$showExpired) {
                $query->active();
            }
        }
        
        $tariffs = $query->orderBy('cost_reference_id')
            ->orderBy('effective_date', 'desc')
            ->paginate(30)
            ->appends($request->query());
        
        // Get tariff classes for filter
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('tariff-explorer.index', compact(
            'tariffs',
            'search',
            'tariffClassId',
            'effectiveDate',
            'showExpired',
            'tariffClasses'
        ));
    }

    /**
     * Show tariff detail with history
     */
    public function show(FinalTariff $finalTariff)
    {
        if ($finalTariff->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $finalTariff->load(['costReference', 'tariffClass', 'unitCostCalculation']);
        
        // Get tariff history for this service
        $history = $this->tariffService->getTariffHistory(
            $finalTariff->cost_reference_id,
            $finalTariff->tariff_class_id
        );
        
        // Compare with INA-CBG if available
        $inaCbgComparison = $this->tariffService->compareWithInaCbg(
            $finalTariff->cost_reference_id,
            $finalTariff
        );
        
        return view('tariff-explorer.show', compact(
            'finalTariff',
            'history',
            'inaCbgComparison'
        ));
    }

    /**
     * Compare tariff with INA-CBG or previous versions
     */
    public function compare(Request $request, $serviceId)
    {
        $costReference = CostReference::findOrFail($serviceId);
        
        if ($costReference->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $tariffClassId = $request->get('tariff_class_id');
        
        // Get current active tariff
        $currentTariff = $this->tariffService->getActiveTariff(
            $serviceId,
            $tariffClassId
        );
        
        // Get tariff history
        $history = $this->tariffService->getTariffHistory(
            $serviceId,
            $tariffClassId
        );
        
        // Compare with INA-CBG
        $inaCbgComparison = null;
        if ($currentTariff) {
            $inaCbgComparison = $this->tariffService->compareWithInaCbg(
                $serviceId,
                $currentTariff
            );
        }
        
        // Get tariff classes for filter
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('tariff-explorer.compare', compact(
            'costReference',
            'currentTariff',
            'history',
            'inaCbgComparison',
            'tariffClasses',
            'tariffClassId'
        ));
    }
}

