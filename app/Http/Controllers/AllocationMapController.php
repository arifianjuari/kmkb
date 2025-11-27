<?php

namespace App\Http\Controllers;

use App\Models\AllocationMap;
use App\Models\CostCenter;
use App\Models\AllocationDriver;
use Illuminate\Http\Request;

class AllocationMapController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stepSequence = $request->get('step_sequence');
        
        $query = AllocationMap::where('hospital_id', hospital('id'))
            ->with(['sourceCostCenter', 'allocationDriver']);
        
        if ($search) {
            $query->whereHas('sourceCostCenter', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            })->orWhereHas('allocationDriver', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($stepSequence) {
            $query->where('step_sequence', $stepSequence);
        }
        
        $allocationMaps = $query->orderBy('step_sequence')->orderBy('source_cost_center_id')->paginate(15)->appends($request->query());
        
        return view('allocation-maps.index', compact('allocationMaps', 'search', 'stepSequence'));
    }

    public function create()
    {
        // Get support cost centers
        $supportCostCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('type', 'support')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get allocation drivers
        $allocationDrivers = AllocationDriver::where('hospital_id', hospital('id'))
            ->orderBy('name')
            ->get();
        
        // Get existing step sequences
        $existingSteps = AllocationMap::where('hospital_id', hospital('id'))
            ->pluck('step_sequence')
            ->toArray();
        
        $nextStep = !empty($existingSteps) ? max($existingSteps) + 1 : 1;
        
        return view('allocation-maps.create', compact('supportCostCenters', 'allocationDrivers', 'nextStep'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_cost_center_id' => 'required|exists:cost_centers,id',
            'allocation_driver_id' => 'required|exists:allocation_drivers,id',
            'step_sequence' => 'required|integer|min:1',
        ]);

        // Validasi: Pastikan source cost center adalah support center
        $sourceCostCenter = CostCenter::findOrFail($validated['source_cost_center_id']);
        if ($sourceCostCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if ($sourceCostCenter->type !== 'support') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Source cost center harus bertipe support.');
        }

        // Validasi: Pastikan step_sequence tidak duplikat untuk source cost center yang sama
        $existingMap = AllocationMap::where('hospital_id', hospital('id'))
            ->where('source_cost_center_id', $validated['source_cost_center_id'])
            ->where('step_sequence', $validated['step_sequence'])
            ->first();

        if ($existingMap) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Step sequence sudah digunakan untuk cost center ini.');
        }

        AllocationMap::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
        ]));

        return redirect()->route('allocation-maps.index')
            ->with('success', 'Allocation map berhasil dibuat.');
    }

    public function show(AllocationMap $allocationMap)
    {
        if ($allocationMap->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $allocationMap->load(['sourceCostCenter', 'allocationDriver']);
        
        return view('allocation-maps.show', compact('allocationMap'));
    }

    public function edit(AllocationMap $allocationMap)
    {
        if ($allocationMap->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Get support cost centers
        $supportCostCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('type', 'support')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get allocation drivers
        $allocationDrivers = AllocationDriver::where('hospital_id', hospital('id'))
            ->orderBy('name')
            ->get();
        
        $allocationMap->load(['sourceCostCenter', 'allocationDriver']);
        
        return view('allocation-maps.edit', compact('allocationMap', 'supportCostCenters', 'allocationDrivers'));
    }

    public function update(Request $request, AllocationMap $allocationMap)
    {
        if ($allocationMap->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'source_cost_center_id' => 'required|exists:cost_centers,id',
            'allocation_driver_id' => 'required|exists:allocation_drivers,id',
            'step_sequence' => 'required|integer|min:1',
        ]);

        // Validasi: Pastikan source cost center adalah support center
        $sourceCostCenter = CostCenter::findOrFail($validated['source_cost_center_id']);
        if ($sourceCostCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if ($sourceCostCenter->type !== 'support') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Source cost center harus bertipe support.');
        }

        // Validasi: Pastikan step_sequence tidak duplikat untuk source cost center yang sama (kecuali untuk record ini)
        $existingMap = AllocationMap::where('hospital_id', hospital('id'))
            ->where('source_cost_center_id', $validated['source_cost_center_id'])
            ->where('step_sequence', $validated['step_sequence'])
            ->where('id', '!=', $allocationMap->id)
            ->first();

        if ($existingMap) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Step sequence sudah digunakan untuk cost center ini.');
        }

        $allocationMap->update($validated);

        return redirect()->route('allocation-maps.index')
            ->with('success', 'Allocation map berhasil diperbarui.');
    }

    public function destroy(AllocationMap $allocationMap)
    {
        if ($allocationMap->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Check if this allocation map has been used in allocation results
        // Note: Kita tidak perlu check karena allocation results bisa dihapus dan di-recalculate
        // Tapi kita bisa memberikan warning jika ada
        
        $allocationMap->delete();

        return redirect()->route('allocation-maps.index')
            ->with('success', 'Allocation map berhasil dihapus.');
    }
}

