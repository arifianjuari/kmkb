<?php

namespace App\Http\Controllers;

use App\Models\ServiceFeeConfig;
use Illuminate\Http\Request;

class ServiceFeeConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = $request->get('year');

        $query = ServiceFeeConfig::where('hospital_id', hospital('id'))
            ->with(['creator', 'updater']);

        if ($year) {
            $query->where('period_year', $year);
        }

        $configs = $query->orderBy('period_year', 'desc')
            ->orderBy('name')
            ->paginate(20);

        $years = ServiceFeeConfig::where('hospital_id', hospital('id'))
            ->distinct()
            ->pluck('period_year')
            ->sort()
            ->reverse();

        return view('service-fees.configs.index', compact('configs', 'years', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service-fees.configs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'period_year' => 'required|integer|min:2020|max:2099',
            'jasa_pelayanan_pct' => 'required|numeric|min:0|max:100',
            'jasa_sarana_pct' => 'required|numeric|min:0|max:100',
            'pct_medis' => 'required|numeric|min:0|max:100',
            'pct_keperawatan' => 'required|numeric|min:0|max:100',
            'pct_penunjang' => 'required|numeric|min:0|max:100',
            'pct_manajemen' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Validate ratios sum to 100
        $mainRatio = $validated['jasa_pelayanan_pct'] + $validated['jasa_sarana_pct'];
        if (abs($mainRatio - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'jasa_pelayanan_pct' => 'Jasa Pelayanan + Jasa Sarana harus = 100%',
            ]);
        }

        $distributionSum = $validated['pct_medis'] + $validated['pct_keperawatan'] 
            + $validated['pct_penunjang'] + $validated['pct_manajemen'];
        if (abs($distributionSum - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'pct_medis' => 'Total distribusi (Medis + Keperawatan + Penunjang + Manajemen) harus = 100%',
            ]);
        }

        $validated['hospital_id'] = hospital('id');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        ServiceFeeConfig::create($validated);

        return redirect()->route('service-fees.configs.index')
            ->with('success', 'Konfigurasi jasa berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceFeeConfig $config)
    {
        $this->authorizeHospital($config);

        $config->load(['indexes', 'calculations']);

        return view('service-fees.configs.show', compact('config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceFeeConfig $config)
    {
        $this->authorizeHospital($config);

        return view('service-fees.configs.edit', compact('config'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceFeeConfig $config)
    {
        $this->authorizeHospital($config);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'period_year' => 'required|integer|min:2020|max:2099',
            'jasa_pelayanan_pct' => 'required|numeric|min:0|max:100',
            'jasa_sarana_pct' => 'required|numeric|min:0|max:100',
            'pct_medis' => 'required|numeric|min:0|max:100',
            'pct_keperawatan' => 'required|numeric|min:0|max:100',
            'pct_penunjang' => 'required|numeric|min:0|max:100',
            'pct_manajemen' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Validate ratios sum to 100
        $mainRatio = $validated['jasa_pelayanan_pct'] + $validated['jasa_sarana_pct'];
        if (abs($mainRatio - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'jasa_pelayanan_pct' => 'Jasa Pelayanan + Jasa Sarana harus = 100%',
            ]);
        }

        $distributionSum = $validated['pct_medis'] + $validated['pct_keperawatan'] 
            + $validated['pct_penunjang'] + $validated['pct_manajemen'];
        if (abs($distributionSum - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'pct_medis' => 'Total distribusi (Medis + Keperawatan + Penunjang + Manajemen) harus = 100%',
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['updated_by'] = auth()->id();

        $config->update($validated);

        return redirect()->route('service-fees.configs.index')
            ->with('success', 'Konfigurasi jasa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceFeeConfig $config)
    {
        $this->authorizeHospital($config);

        // Check if there are related indexes or calculations
        if ($config->indexes()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus konfigurasi yang memiliki indeks jasa.');
        }

        if ($config->calculations()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus konfigurasi yang sudah digunakan untuk perhitungan.');
        }

        $config->delete();

        return redirect()->route('service-fees.configs.index')
            ->with('success', 'Konfigurasi jasa berhasil dihapus.');
    }

    /**
     * Duplicate a configuration for another year.
     */
    public function duplicate(Request $request, ServiceFeeConfig $config)
    {
        $this->authorizeHospital($config);

        $validated = $request->validate([
            'new_year' => 'required|integer|min:2020|max:2099',
            'new_name' => 'required|string|max:255',
        ]);

        $newConfig = $config->replicate();
        $newConfig->name = $validated['new_name'];
        $newConfig->period_year = $validated['new_year'];
        $newConfig->is_active = true;
        $newConfig->created_by = auth()->id();
        $newConfig->save();

        // Duplicate indexes too
        foreach ($config->indexes as $index) {
            $newIndex = $index->replicate();
            $newIndex->service_fee_config_id = $newConfig->id;
            $newIndex->save();
        }

        return redirect()->route('service-fees.configs.show', $newConfig)
            ->with('success', 'Konfigurasi berhasil diduplikasi.');
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
