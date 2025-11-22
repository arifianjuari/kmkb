<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicalPathway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PathwayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $pathways = ClinicalPathway::with('creator')->latest()->get();
            return response()->json($pathways);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load clinical pathways: ' . $e->getMessage()], 500);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'diagnosis_code' => 'required|string|max:50',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $pathway = new ClinicalPathway();
            $pathway->name = $request->name;
            $pathway->description = $request->description;
            $pathway->diagnosis_code = $request->diagnosis_code;
            $pathway->version = $request->version;
            $pathway->effective_date = $request->effective_date;
            $pathway->status = $request->status;
            $pathway->created_by = Auth::id();
            $pathway->save();

            DB::commit();
            
            return response()->json($pathway, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create clinical pathway: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function show(ClinicalPathway $pathway)
    {
        try {
            $pathway->load(['steps', 'creator']);
            return response()->json($pathway);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load clinical pathway: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClinicalPathway $pathway)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'diagnosis_code' => 'required|string|max:50',
                'version' => 'required|string|max:20',
                'effective_date' => 'required|date',
                'status' => 'required|in:draft,active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $pathway->update($request->all());

            return response()->json($pathway);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update clinical pathway: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClinicalPathway $pathway)
    {
        try {
            $pathway->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete clinical pathway: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource for public access.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publicShow($id)
    {
        try {
            $pathway = ClinicalPathway::with('steps.costReference')->findOrFail($id);
            return response()->json($pathway);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load clinical pathway: ' . $e->getMessage()], 500);
        }
    }
}
