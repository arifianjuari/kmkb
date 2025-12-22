<?php

namespace App\Http\Controllers;

use App\Models\RevenueSource;
use Illuminate\Http\Request;

class RevenueSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sources = RevenueSource::where('hospital_id', hospital('id'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('service-fees.revenue-sources.index', compact('sources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service-fees.revenue-sources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:revenue_sources,code,NULL,id,hospital_id,' . hospital('id'),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['hospital_id'] = hospital('id');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        RevenueSource::create($validated);

        return redirect()->route('service-fees.revenue-sources.index')
            ->with('success', 'Sumber pendapatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RevenueSource $revenueSource)
    {
        $this->authorizeHospital($revenueSource);
        return view('service-fees.revenue-sources.show', compact('revenueSource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RevenueSource $revenueSource)
    {
        $this->authorizeHospital($revenueSource);
        return view('service-fees.revenue-sources.edit', compact('revenueSource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RevenueSource $revenueSource)
    {
        $this->authorizeHospital($revenueSource);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:revenue_sources,code,' . $revenueSource->id . ',id,hospital_id,' . hospital('id'),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $revenueSource->update($validated);

        return redirect()->route('service-fees.revenue-sources.index')
            ->with('success', 'Sumber pendapatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RevenueSource $revenueSource)
    {
        $this->authorizeHospital($revenueSource);

        // Check if there are any revenue records
        if ($revenueSource->revenueRecords()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus sumber pendapatan yang memiliki data pendapatan.');
        }

        $revenueSource->delete();

        return redirect()->route('service-fees.revenue-sources.index')
            ->with('success', 'Sumber pendapatan berhasil dihapus.');
    }

    /**
     * Seed default revenue sources for the current hospital.
     */
    public function seedDefaults()
    {
        $hospitalId = hospital('id');
        $defaults = RevenueSource::getDefaultSources();
        $created = 0;

        foreach ($defaults as $source) {
            $exists = RevenueSource::where('hospital_id', $hospitalId)
                ->where('code', $source['code'])
                ->exists();

            if (!$exists) {
                RevenueSource::create(array_merge($source, [
                    'hospital_id' => $hospitalId,
                    'is_active' => true,
                ]));
                $created++;
            }
        }

        return redirect()->route('service-fees.revenue-sources.index')
            ->with('success', "Berhasil menambahkan {$created} sumber pendapatan default.");
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
