<?php

namespace App\Http\Controllers;

use App\Models\RvuValue;
use App\Models\CostReference;
use App\Models\CostCenter;
use App\Models\ServiceVolume;
use App\Http\Requests\StoreRvuValueRequest;
use App\Http\Requests\UpdateRvuValueRequest;
use App\Services\RvuCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RvuValueController extends Controller
{
    protected $rvuCalculationService;

    public function __construct(RvuCalculationService $rvuCalculationService)
    {
        $this->rvuCalculationService = $rvuCalculationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hospitalId = hospital('id');
        $search = $request->get('search');
        $costReferenceId = $request->get('cost_reference_id');
        $costCenterId = $request->get('cost_center_id');
        $periodYear = $request->get('period_year', date('Y'));
        $periodMonth = $request->get('period_month');
        
        $query = RvuValue::where('hospital_id', $hospitalId)
            ->with(['costReference', 'costCenter', 'costReference.expenseCategory']);
        
        // Filter by cost reference
        if ($costReferenceId) {
            $query->where('cost_reference_id', $costReferenceId);
        }
        
        // Filter by cost center
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        // Filter by period
        if ($periodYear) {
            $query->where('period_year', $periodYear);
        }
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        
        // Search
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        $rvuValues = $query->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());
        
        // Get cost references for filter dropdown
        $costReferences = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_code')
            ->get();
        
        // Get cost centers for filter dropdown
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get service volumes for current period to show "Jumlah Tindakan"
        $serviceVolumes = [];
        if ($periodYear) {
            $volumeQuery = ServiceVolume::where('hospital_id', $hospitalId)
                ->where('period_year', $periodYear);
            if ($periodMonth) {
                $volumeQuery->where('period_month', $periodMonth);
            }
            $volumes = $volumeQuery->get();
            foreach ($volumes as $volume) {
                $serviceVolumes[$volume->cost_reference_id] = $volume->total_quantity;
            }
        }
        
        return view('rvu-values.index', compact('rvuValues', 'costReferences', 'costCenters', 'serviceVolumes', 'search', 'costReferenceId', 'costCenterId', 'periodYear', 'periodMonth'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hospitalId = hospital('id');
        $costReferences = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_code')
            ->get();
        
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get current year and month as default
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        return view('rvu-values.create', compact('costReferences', 'costCenters', 'currentYear', 'currentMonth'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRvuValueRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $hospitalId = hospital('id');
            $userId = auth()->id();
            
            // Check for duplicate (same cost_reference, cost_center, period_year, period_month)
            $exists = RvuValue::where('hospital_id', $hospitalId)
                ->where('cost_reference_id', $request->cost_reference_id)
                ->where('cost_center_id', $request->cost_center_id)
                ->where('period_year', $request->period_year)
                ->where(function($q) use ($request) {
                    if ($request->period_month) {
                        $q->where('period_month', $request->period_month);
                    } else {
                        $q->whereNull('period_month');
                    }
                })
                ->exists();
            
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'RVU untuk Cost Reference, Cost Center, dan periode ini sudah ada. Silakan edit yang sudah ada atau pilih periode lain.');
            }
            
            // Calculate RVU value
            $rvuValue = $this->rvuCalculationService->calculateRvuValue(
                $request->time_factor,
                $request->professionalism_factor,
                $request->difficulty_factor,
                $request->normalization_factor ?? 1.0
            );
            
            $rvu = RvuValue::create([
                'hospital_id' => $hospitalId,
                'cost_reference_id' => $request->cost_reference_id,
                'cost_center_id' => $request->cost_center_id,
                'period_year' => $request->period_year,
                'period_month' => $request->period_month,
                'time_factor' => $request->time_factor,
                'professionalism_factor' => $request->professionalism_factor,
                'difficulty_factor' => $request->difficulty_factor,
                'normalization_factor' => $request->normalization_factor ?? 1.0,
                'rvu_value' => $rvuValue,
                'notes' => $request->notes,
                'is_active' => $request->is_active ?? true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
            
            DB::commit();
            
            return redirect()->route('rvu-values.index')
                ->with('success', 'RVU berhasil dibuat.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating RVU: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan RVU: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RvuValue $rvuValue)
    {
        // Ensure the RVU belongs to the current hospital
        if ($rvuValue->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $rvuValue->load(['costReference', 'costCenter', 'costReference.expenseCategory', 'creator', 'updater']);
        
        return view('rvu-values.show', compact('rvuValue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RvuValue $rvuValue)
    {
        // Ensure the RVU belongs to the current hospital
        if ($rvuValue->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $hospitalId = hospital('id');
        $costReferences = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_code')
            ->get();
        
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $rvuValue->load('costCenter');
        
        return view('rvu-values.edit', compact('rvuValue', 'costReferences', 'costCenters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRvuValueRequest $request, RvuValue $rvuValue)
    {
        // Ensure the RVU belongs to the current hospital
        if ($rvuValue->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        try {
            DB::beginTransaction();
            
            $userId = auth()->id();
            
            // Check for duplicate (excluding current record)
            $exists = RvuValue::where('hospital_id', hospital('id'))
                ->where('id', '!=', $rvuValue->id)
                ->where('cost_reference_id', $request->cost_reference_id)
                ->where('cost_center_id', $request->cost_center_id)
                ->where('period_year', $request->period_year)
                ->where(function($q) use ($request) {
                    if ($request->period_month) {
                        $q->where('period_month', $request->period_month);
                    } else {
                        $q->whereNull('period_month');
                    }
                })
                ->exists();
            
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'RVU untuk Cost Reference, Cost Center, dan periode ini sudah ada. Silakan pilih periode lain.');
            }
            
            // Calculate RVU value
            $calculatedRvuValue = $this->rvuCalculationService->calculateRvuValue(
                $request->time_factor,
                $request->professionalism_factor,
                $request->difficulty_factor,
                $request->normalization_factor ?? 1.0
            );
            
            $rvuValue->update([
                'cost_reference_id' => $request->cost_reference_id,
                'cost_center_id' => $request->cost_center_id,
                'period_year' => $request->period_year,
                'period_month' => $request->period_month,
                'time_factor' => $request->time_factor,
                'professionalism_factor' => $request->professionalism_factor,
                'difficulty_factor' => $request->difficulty_factor,
                'normalization_factor' => $request->normalization_factor ?? 1.0,
                'rvu_value' => $calculatedRvuValue,
                'notes' => $request->notes,
                'is_active' => $request->is_active ?? true,
                'updated_by' => $userId,
            ]);
            
            DB::commit();
            
            return redirect()->route('rvu-values.index')
                ->with('success', 'RVU berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating RVU: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui RVU: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RvuValue $rvuValue)
    {
        // Ensure the RVU belongs to the current hospital
        if ($rvuValue->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        try {
            $rvuValue->delete();
            
            return redirect()->route('rvu-values.index')
                ->with('success', 'RVU berhasil dihapus.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting RVU: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus RVU: ' . $e->getMessage());
        }
    }

    /**
     * Get RVU by cost reference (API endpoint).
     */
    public function getByCostReference($costReferenceId)
    {
        $hospitalId = hospital('id');
        
        $rvuValues = RvuValue::where('hospital_id', $hospitalId)
            ->where('cost_reference_id', $costReferenceId)
            ->where('is_active', true)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
        
        return response()->json($rvuValues);
    }
}
