<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PatientCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $cases = PatientCase::with(['clinicalPathway', 'inputBy'])->latest()->get();
            return response()->json($cases);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load patient cases: ' . $e->getMessage()], 500);
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
            'medical_record_number' => 'required|string|max:50',
            'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
            'admission_date' => 'required|date',
            'discharge_date' => 'required|date|after:admission_date',
            'primary_diagnosis' => 'required|string|max:255',
            'ina_cbg_code' => 'required|string|max:50',
            'actual_total_cost' => 'required|numeric|min:0',
            'ina_cbg_tariff' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $case = new PatientCase();
            $case->patient_id = uniqid(); // In a real app, this would come from a patient management system
            $case->medical_record_number = $request->medical_record_number;
            $case->clinical_pathway_id = $request->clinical_pathway_id;
            $case->admission_date = $request->admission_date;
            $case->discharge_date = $request->discharge_date;
            $case->primary_diagnosis = $request->primary_diagnosis;
            $case->ina_cbg_code = $request->ina_cbg_code;
            $case->actual_total_cost = $request->actual_total_cost;
            $case->ina_cbg_tariff = $request->ina_cbg_tariff;
            $case->input_by = Auth::id();
            $case->input_date = now();
            
            // Calculate compliance percentage and cost variance
            $case->compliance_percentage = 0; // This would be calculated based on case details
            $case->cost_variance = $request->actual_total_cost - $request->ina_cbg_tariff;
            
            $case->save();

            DB::commit();
            
            return response()->json($case, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create patient case: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function show(PatientCase $case)
    {
        try {
            $case->load(['clinicalPathway', 'inputBy', 'caseDetails.pathwayStep']);
            return response()->json($case);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load patient case: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientCase $case)
    {
        try {
            $validator = Validator::make($request->all(), [
                'medical_record_number' => 'required|string|max:50',
                'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
                'admission_date' => 'required|date',
                'discharge_date' => 'required|date|after:admission_date',
                'primary_diagnosis' => 'required|string|max:255',
                'ina_cbg_code' => 'required|string|max:50',
                'actual_total_cost' => 'required|numeric|min:0',
                'ina_cbg_tariff' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $case->update($request->except(['patient_id', 'input_by', 'input_date']));
            
            // Recalculate cost variance
            $case->cost_variance = $request->actual_total_cost - $request->ina_cbg_tariff;
            $case->save();

            return response()->json($case);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update patient case: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientCase $case)
    {
        try {
            $case->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete patient case: ' . $e->getMessage()], 500);
        }
    }
}
