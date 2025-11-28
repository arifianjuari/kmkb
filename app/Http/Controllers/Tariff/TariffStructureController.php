<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use App\Models\FinalTariff;
use App\Models\TariffClass;
use App\Models\CostReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TariffStructureController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = hospital('id');
        
        $tariffClassId = $request->get('tariff_class_id');
        $search = $request->get('search');
        $status = $request->get('status', 'active'); // active, expired, all
        
        // Build query
        $query = FinalTariff::where('hospital_id', $hospitalId)
            ->with(['costReference.costCenter', 'costReference.expenseCategory', 'tariffClass', 'unitCostCalculation']);
        
        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }
        
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'expired') {
            $query->where(function($q) {
                $q->whereNotNull('expired_date')
                  ->where('expired_date', '<', now());
            });
        }
        
        $tariffs = $query->orderBy('effective_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
        
        // Get tariff classes for filter
        $tariffClasses = TariffClass::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Summary statistics
        $summaryStats = [
            'total_tariffs' => FinalTariff::where('hospital_id', $hospitalId)->count(),
            'active_tariffs' => FinalTariff::where('hospital_id', $hospitalId)->active()->count(),
            'expired_tariffs' => FinalTariff::where('hospital_id', $hospitalId)
                ->whereNotNull('expired_date')
                ->where('expired_date', '<', now())
                ->count(),
            'avg_margin' => FinalTariff::where('hospital_id', $hospitalId)
                ->active()
                ->avg('margin_percentage'),
            'total_jasa_sarana' => FinalTariff::where('hospital_id', $hospitalId)
                ->active()
                ->sum('jasa_sarana'),
            'total_jasa_pelayanan' => FinalTariff::where('hospital_id', $hospitalId)
                ->active()
                ->sum('jasa_pelayanan'),
        ];
        
        // Component breakdown statistics
        $componentStats = DB::table('final_tariffs')
            ->where('hospital_id', $hospitalId)
            ->where(function($q) {
                $q->whereNull('expired_date')
                  ->orWhere('expired_date', '>=', now());
            })
            ->select(
                DB::raw('AVG(base_unit_cost) as avg_base_unit_cost'),
                DB::raw('AVG(margin_percentage) * 100 as avg_margin_percent'),
                DB::raw('AVG(jasa_sarana) as avg_jasa_sarana'),
                DB::raw('AVG(jasa_pelayanan) as avg_jasa_pelayanan'),
                DB::raw('AVG(final_tariff_price) as avg_final_price'),
                DB::raw('SUM(jasa_sarana) as total_jasa_sarana'),
                DB::raw('SUM(jasa_pelayanan) as total_jasa_pelayanan'),
                DB::raw('COUNT(*) as count')
            )
            ->first();
        
        // Breakdown by tariff class
        $breakdownByClass = DB::table('final_tariffs')
            ->join('tariff_classes', 'final_tariffs.tariff_class_id', '=', 'tariff_classes.id')
            ->where('final_tariffs.hospital_id', $hospitalId)
            ->where(function($q) {
                $q->whereNull('final_tariffs.expired_date')
                  ->orWhere('final_tariffs.expired_date', '>=', now());
            })
            ->select(
                'tariff_classes.id',
                'tariff_classes.name',
                'tariff_classes.code',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(final_tariffs.margin_percentage) * 100 as avg_margin'),
                DB::raw('AVG(final_tariffs.jasa_sarana) as avg_jasa_sarana'),
                DB::raw('AVG(final_tariffs.jasa_pelayanan) as avg_jasa_pelayanan')
            )
            ->groupBy('tariff_classes.id', 'tariff_classes.name', 'tariff_classes.code')
            ->orderBy('tariff_classes.name')
            ->get();
        
        return view('tariffs.structure', compact(
            'tariffs',
            'tariffClasses',
            'tariffClassId',
            'search',
            'status',
            'summaryStats',
            'componentStats',
            'breakdownByClass'
        ));
    }
}

