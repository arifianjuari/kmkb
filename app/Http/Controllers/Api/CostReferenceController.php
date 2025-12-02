<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CostReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $costReferences = CostReference::all();
            return response()->json($costReferences);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load cost references: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_code' => 'required|string|max:50|unique:cost_references',
                'service_description' => 'required|string|max:255',
                'standard_cost' => 'required|numeric|min:0',
                'unit' => 'required|string|max:50',
                'source' => 'required|string|max:255',
                'category' => 'nullable|string|in:barang,tindakan_rj,tindakan_ri,laboratorium,radiologi,operasi,kamar',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $costReference = CostReference::create($request->all());

            return response()->json($costReference, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create cost reference: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function show(CostReference $costReference)
    {
        try {
            return response()->json($costReference);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load cost reference: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CostReference $costReference)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_code' => 'required|string|max:50|unique:cost_references,service_code,' . $costReference->id,
                'service_description' => 'required|string|max:255',
                'standard_cost' => 'required|numeric|min:0',
                'unit' => 'required|string|max:50',
                'source' => 'required|string|max:255',
                'category' => 'nullable|string|in:barang,tindakan_rj,tindakan_ri,laboratorium,radiologi,operasi,kamar',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $costReference->update($request->all());

            return response()->json($costReference);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update cost reference: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostReference $costReference)
    {
        try {
            $costReference->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete cost reference: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all cost references that are services (tindakan/pemeriksaan).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getServices(Request $request)
    {
        try {
            $hospitalId = hospital('id');
            $search = $request->get('search');
            
            $query = CostReference::where('hospital_id', $hospitalId)
                ->where('item_type', 'service');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            }
            
            $services = $query->orderBy('service_description')->get();
            
            return response()->json([
                'success' => true,
                'data' => $services,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load services: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all cost references that are BMHP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBMHP(Request $request)
    {
        try {
            $hospitalId = hospital('id');
            $search = $request->get('search');
            
            $query = CostReference::where('hospital_id', $hospitalId)
                ->where('item_type', 'bmhp');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            }
            
            $bmhp = $query->orderBy('service_description')->get();
            
            return response()->json([
                'success' => true,
                'data' => $bmhp,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load BMHP: ' . $e->getMessage()
            ], 500);
        }
    }
}
