<?php

namespace App\Http\Controllers;

use App\Models\ServiceFeeAssignment;
use App\Models\ServiceFeeIndex;
use App\Models\CostReference;
use Illuminate\Http\Request;

class ServiceFeeAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $costReferenceId = $request->get('cost_reference_id');
        $search = $request->get('search');

        $query = ServiceFeeAssignment::where('hospital_id', hospital('id'))
            ->with(['costReference', 'serviceFeeIndex.config']);

        if ($costReferenceId) {
            $query->where('cost_reference_id', $costReferenceId);
        }

        if ($search) {
            $query->whereHas('costReference', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $assignments = $query->orderBy('cost_reference_id')
            ->paginate(50);

        return view('service-fees.assignments.index', compact('assignments', 'costReferenceId', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $costReferences = CostReference::where('hospital_id', hospital('id'))
            ->where('category', 'Tindakan')
            ->orderBy('name')
            ->get();

        $indexes = ServiceFeeIndex::where('hospital_id', hospital('id'))
            ->whereHas('config', function ($q) {
                $q->where('is_active', true);
            })
            ->active()
            ->with('config')
            ->orderBy('category')
            ->orderBy('role')
            ->get();

        $selectedCostReferenceId = $request->get('cost_reference_id');

        return view('service-fees.assignments.create', compact(
            'costReferences', 'indexes', 'selectedCostReferenceId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_reference_id' => 'required|exists:cost_references,id',
            'service_fee_index_id' => 'required|exists:service_fee_indexes,id',
            'participation_pct' => 'required|numeric|min:0|max:100',
            'headcount' => 'required|integer|min:1|max:99',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Verify cost reference belongs to current hospital
        $costRef = CostReference::findOrFail($validated['cost_reference_id']);
        if ($costRef->hospital_id !== hospital('id')) {
            abort(403);
        }

        // Verify index belongs to current hospital
        $index = ServiceFeeIndex::findOrFail($validated['service_fee_index_id']);
        if ($index->hospital_id !== hospital('id')) {
            abort(403);
        }

        $validated['hospital_id'] = hospital('id');
        $validated['is_active'] = $request->boolean('is_active', true);

        ServiceFeeAssignment::create($validated);

        return redirect()->route('service-fees.assignments.index', [
            'cost_reference_id' => $validated['cost_reference_id'],
        ])->with('success', 'Penugasan jasa berhasil ditambahkan.');
    }

    /**
     * Display assignments for a specific cost reference.
     */
    public function byService(CostReference $costReference)
    {
        if ($costReference->hospital_id !== hospital('id')) {
            abort(403);
        }

        $assignments = ServiceFeeAssignment::where('hospital_id', hospital('id'))
            ->where('cost_reference_id', $costReference->id)
            ->with('serviceFeeIndex.config')
            ->get();

        $totalPoints = $assignments->sum(fn($a) => $a->effective_points);

        $indexes = ServiceFeeIndex::where('hospital_id', hospital('id'))
            ->whereHas('config', fn($q) => $q->where('is_active', true))
            ->active()
            ->with('config')
            ->get();

        return view('service-fees.assignments.by-service', compact(
            'costReference', 'assignments', 'totalPoints', 'indexes'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceFeeAssignment $assignment)
    {
        $this->authorizeHospital($assignment);

        $indexes = ServiceFeeIndex::where('hospital_id', hospital('id'))
            ->whereHas('config', fn($q) => $q->where('is_active', true))
            ->with('config')
            ->orderBy('category')
            ->orderBy('role')
            ->get();

        return view('service-fees.assignments.edit', compact('assignment', 'indexes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceFeeAssignment $assignment)
    {
        $this->authorizeHospital($assignment);

        $validated = $request->validate([
            'service_fee_index_id' => 'required|exists:service_fee_indexes,id',
            'participation_pct' => 'required|numeric|min:0|max:100',
            'headcount' => 'required|integer|min:1|max:99',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $assignment->update($validated);

        return redirect()->route('service-fees.assignments.index', [
            'cost_reference_id' => $assignment->cost_reference_id,
        ])->with('success', 'Penugasan jasa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceFeeAssignment $assignment)
    {
        $this->authorizeHospital($assignment);

        $costReferenceId = $assignment->cost_reference_id;
        $assignment->delete();

        return redirect()->route('service-fees.assignments.index', [
            'cost_reference_id' => $costReferenceId,
        ])->with('success', 'Penugasan jasa berhasil dihapus.');
    }

    /**
     * Authorize that the model belongs to current hospital.
     */
    private function authorizeHospital($model)
    {
        if ($model->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }
}
