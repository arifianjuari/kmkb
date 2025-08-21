<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PathwayStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PathwayStepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $steps = PathwayStep::with(['clinicalPathway', 'costReference'])->get();
        return response()->json($steps);
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
            'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
            'step_order' => 'required|integer|min:1',
            'description' => 'required|string',
            'action' => 'required|string',
            'criteria' => 'required|string',
            'estimated_duration' => 'required|integer|min:0',
            'estimated_cost' => 'required|numeric|min:0',
            'cost_reference_id' => 'nullable|exists:cost_references,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $step = PathwayStep::create($request->all());

        return response()->json($step, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PathwayStep  $step
     * @return \Illuminate\Http\Response
     */
    public function show(PathwayStep $step)
    {
        $step->load(['clinicalPathway', 'costReference', 'caseDetails']);
        return response()->json($step);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PathwayStep  $step
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PathwayStep $step)
    {
        $validator = Validator::make($request->all(), [
            'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
            'step_order' => 'required|integer|min:1',
            'description' => 'required|string',
            'action' => 'required|string',
            'criteria' => 'required|string',
            'estimated_duration' => 'required|integer|min:0',
            'estimated_cost' => 'required|numeric|min:0',
            'cost_reference_id' => 'nullable|exists:cost_references,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $step->update($request->all());

        return response()->json($step);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PathwayStep  $step
     * @return \Illuminate\Http\Response
     */
    public function destroy(PathwayStep $step)
    {
        $step->delete();

        return response()->json(null, 204);
    }
}
