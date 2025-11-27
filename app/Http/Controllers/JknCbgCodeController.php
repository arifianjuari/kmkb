<?php

namespace App\Http\Controllers;

use App\Models\JknCbgCode;
use Illuminate\Http\Request;

class JknCbgCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cbgCodes = JknCbgCode::orderBy('code')->paginate(20);
        return view('jkn_cbg_codes.index', compact('cbgCodes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jkn_cbg_codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:jkn_cbg_codes',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'service_type' => 'nullable|in:Rawat Inap,Rawat Jalan',
                'severity_level' => 'nullable|integer|min:1|max:3',
                'grouping_version' => 'nullable|string|max:50',
                'tariff' => 'required|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $cbgCode = JknCbgCode::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'service_type' => $request->service_type,
                'severity_level' => $request->severity_level,
                'grouping_version' => $request->grouping_version,
                'tariff' => $request->tariff,
                'is_active' => filter_var($request->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);

            return redirect()->route('jkn-cbg-codes.index')
                ->with('success', 'CBG Code berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan CBG Code: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JknCbgCode $jknCbgCode)
    {
        return view('jkn_cbg_codes.edit', compact('jknCbgCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JknCbgCode $jknCbgCode)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:jkn_cbg_codes,code,'.$jknCbgCode->id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'service_type' => 'nullable|in:Rawat Inap,Rawat Jalan',
                'severity_level' => 'nullable|integer|min:1|max:3',
                'grouping_version' => 'nullable|string|max:50',
                'tariff' => 'required|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $jknCbgCode->update([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'service_type' => $request->service_type,
                'severity_level' => $request->severity_level,
                'grouping_version' => $request->grouping_version,
                'tariff' => $request->tariff,
                'is_active' => filter_var($request->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);

            return redirect()->route('jkn-cbg-codes.index')
                ->with('success', 'CBG Code berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui CBG Code: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JknCbgCode $jknCbgCode)
    {
        $jknCbgCode->delete();

        return redirect()->route('jkn-cbg-codes.index')
            ->with('success', 'CBG Code berhasil dihapus.');
    }

    /**
     * Search for CBG codes (for autocomplete in patient case form)
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $cbgCodes = JknCbgCode::active()
            ->where('code', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->orderBy('code')
            ->limit(20)
            ->get();

        return response()->json($cbgCodes);
    }

    /**
     * Get tariff for a specific CBG code (for auto-filling in patient case form)
     */
    public function getTariff(Request $request)
    {
        $code = $request->get('code');
        
        $cbgCode = JknCbgCode::active()
            ->where('code', $code)
            ->first();

        if ($cbgCode) {
            return response()->json([
                'tariff' => $cbgCode->tariff,
                'name' => $cbgCode->name
            ]);
        }

        return response()->json([
            'tariff' => null,
            'name' => null
        ]);
    }

    /**
     * Base Tariff Reference view
     */
    public function baseTariff()
    {
        return view('setup.jkn-cbg-codes.base-tariff', [
            'title' => 'Base Tariff Reference',
            'message' => 'Fitur untuk melihat base tariff reference dan perbandingan dengan internal tariff sedang dalam tahap pengembangan.'
        ]);
    }
}
