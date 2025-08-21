<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CaseDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $caseDetails = CaseDetail::with(['patientCase', 'pathwayStep'])->get();
        return response()->json($caseDetails);
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
            'patient_case_id' => 'required|exists:patient_cases,id',
            'pathway_step_id' => 'required|exists:pathway_steps,id',
            'service_item' => 'required|string|max:255',
            'service_code' => 'required|string|max:50',
            'status' => 'required|in:pending,completed,skipped',
            'quantity' => 'required|integer|min:1',
            'actual_cost' => 'required|numeric|min:0',
            'service_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $caseDetail = CaseDetail::create($request->all());

        return response()->json($caseDetail, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CaseDetail  $caseDetail
     * @return \Illuminate\Http\Response
     */
    public function show(CaseDetail $caseDetail)
    {
        $caseDetail->load(['patientCase', 'pathwayStep']);
        return response()->json($caseDetail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CaseDetail  $caseDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CaseDetail $caseDetail)
    {
        $validator = Validator::make($request->all(), [
            'patient_case_id' => 'required|exists:patient_cases,id',
            'pathway_step_id' => 'required|exists:pathway_steps,id',
            'service_item' => 'required|string|max:255',
            'service_code' => 'required|string|max:50',
            'status' => 'required|in:pending,completed,skipped',
            'quantity' => 'required|integer|min:1',
            'actual_cost' => 'required|numeric|min:0',
            'service_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $caseDetail->update($request->all());

        return response()->json($caseDetail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CaseDetail  $caseDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(CaseDetail $caseDetail)
    {
        $caseDetail->delete();

        return response()->json(null, 204);
    }
}
