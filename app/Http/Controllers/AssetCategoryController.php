<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetCategory::where('hospital_id', hospital('id'));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('name')->paginate(25)->withQueryString();
        $typeOptions = AssetCategory::getTypeOptions();

        return view('asset-categories.index', compact('categories', 'typeOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $typeOptions = AssetCategory::getTypeOptions();
        return view('asset-categories.create', compact('typeOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:alkes,sarpras,kendaraan,bangunan,it,lainnya',
            'default_useful_life_years' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['hospital_id'] = hospital('id');
        $validated['is_active'] = $request->boolean('is_active', true);

        AssetCategory::create($validated);

        return redirect()->route('asset-categories.index')
            ->with('success', 'Kategori aset berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetCategory $assetCategory)
    {
        $this->authorizeHospital($assetCategory);
        $typeOptions = AssetCategory::getTypeOptions();

        return view('asset-categories.edit', compact('assetCategory', 'typeOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetCategory $assetCategory)
    {
        $this->authorizeHospital($assetCategory);

        $validated = $request->validate([
            'code' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:alkes,sarpras,kendaraan,bangunan,it,lainnya',
            'default_useful_life_years' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $assetCategory->update($validated);

        return redirect()->route('asset-categories.index')
            ->with('success', 'Kategori aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetCategory $assetCategory)
    {
        $this->authorizeHospital($assetCategory);

        if ($assetCategory->fixedAssets()->exists()) {
            return redirect()->route('asset-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki aset terkait.');
        }

        $assetCategory->delete();

        return redirect()->route('asset-categories.index')
            ->with('success', 'Kategori aset berhasil dihapus.');
    }

    private function authorizeHospital(AssetCategory $category)
    {
        if ($category->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }
}
