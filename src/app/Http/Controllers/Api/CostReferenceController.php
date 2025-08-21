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
        $costReferences = CostReference::all();
        return response()->json($costReferences);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_code' => 'required|string|max:50|unique:cost_references',
            'service_description' => 'required|string|max:255',
            'standard_cost' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'source' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $costReference = CostReference::create($request->all());

        return response()->json($costReference, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function show(CostReference $costReference)
    {
        return response()->json($costReference);
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
        $validator = Validator::make($request->all(), [
            'service_code' => 'required|string|max:50|unique:cost_references,service_code,' . $costReference->id,
            'service_description' => 'required|string|max:255',
            'standard_cost' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'source' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $costReference->update($request->all());

        return response()->json($costReference);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostReference $costReference)
    {
        $costReference->delete();

        return response()->json(null, 204);
    }
}
