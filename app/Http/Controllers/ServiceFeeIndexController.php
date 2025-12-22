<?php

namespace App\Http\Controllers;

use App\Models\ServiceFeeIndex;
use App\Models\ServiceFeeConfig;
use App\Models\Employee;
use Illuminate\Http\Request;

class ServiceFeeIndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $configId = $request->get('config_id');
        $category = $request->get('category');

        $query = ServiceFeeIndex::where('hospital_id', hospital('id'))
            ->with('config');

        if ($configId) {
            $query->where('service_fee_config_id', $configId);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $indexes = $query->orderBy('category')
            ->orderBy('professional_category')
            ->orderBy('role')
            ->paginate(50);

        $configs = ServiceFeeConfig::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('period_year', 'desc')
            ->get();

        $categories = ServiceFeeIndex::getCategories();

        return view('service-fees.indexes.index', compact('indexes', 'configs', 'categories', 'configId', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $configs = ServiceFeeConfig::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('period_year', 'desc')
            ->get();

        $categories = ServiceFeeIndex::getCategories();
        $roles = ServiceFeeIndex::getRoles();
        $professionalCategories = Employee::getProfessionalCategories();

        $selectedConfigId = $request->get('config_id');

        return view('service-fees.indexes.create', compact(
            'configs', 'categories', 'roles', 'professionalCategories', 'selectedConfigId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_fee_config_id' => 'required|exists:service_fee_configs,id',
            'category' => 'required|string|in:medis,keperawatan,penunjang,manajemen',
            'professional_category' => 'required|string',
            'role' => 'required|string',
            'base_index' => 'required|numeric|min:0|max:100',
            'education_factor' => 'required|numeric|min:0.1|max:10',
            'risk_factor' => 'required|numeric|min:0.1|max:10',
            'emergency_factor' => 'required|numeric|min:0.1|max:10',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Verify config belongs to current hospital
        $config = ServiceFeeConfig::findOrFail($validated['service_fee_config_id']);
        if ($config->hospital_id !== hospital('id')) {
            abort(403);
        }

        $validated['hospital_id'] = hospital('id');
        $validated['is_active'] = $request->boolean('is_active', true);

        ServiceFeeIndex::create($validated);

        return redirect()->route('service-fees.indexes.index', [
            'config_id' => $validated['service_fee_config_id'],
        ])->with('success', 'Indeks jasa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceFeeIndex $index)
    {
        $this->authorizeHospital($index);

        $configs = ServiceFeeConfig::where('hospital_id', hospital('id'))
            ->orderBy('period_year', 'desc')
            ->get();

        $categories = ServiceFeeIndex::getCategories();
        $roles = ServiceFeeIndex::getRoles();
        $professionalCategories = Employee::getProfessionalCategories();

        return view('service-fees.indexes.edit', compact(
            'index', 'configs', 'categories', 'roles', 'professionalCategories'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceFeeIndex $index)
    {
        $this->authorizeHospital($index);

        $validated = $request->validate([
            'service_fee_config_id' => 'required|exists:service_fee_configs,id',
            'category' => 'required|string|in:medis,keperawatan,penunjang,manajemen',
            'professional_category' => 'required|string',
            'role' => 'required|string',
            'base_index' => 'required|numeric|min:0|max:100',
            'education_factor' => 'required|numeric|min:0.1|max:10',
            'risk_factor' => 'required|numeric|min:0.1|max:10',
            'emergency_factor' => 'required|numeric|min:0.1|max:10',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $index->update($validated);

        return redirect()->route('service-fees.indexes.index', [
            'config_id' => $validated['service_fee_config_id'],
        ])->with('success', 'Indeks jasa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceFeeIndex $index)
    {
        $this->authorizeHospital($index);

        // Check if there are assignments using this index
        if ($index->assignments()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus indeks yang sudah digunakan dalam penugasan.');
        }

        $configId = $index->service_fee_config_id;
        $index->delete();

        return redirect()->route('service-fees.indexes.index', [
            'config_id' => $configId,
        ])->with('success', 'Indeks jasa berhasil dihapus.');
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
