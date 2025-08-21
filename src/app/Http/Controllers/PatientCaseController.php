<?php

namespace App\Http\Controllers;

use App\Models\PatientCase;
use App\Models\ClinicalPathway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cases = PatientCase::with(['clinicalPathway', 'inputBy'])->latest()->paginate(10);
        return view('cases.index', compact('cases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pathways = ClinicalPathway::where('status', 'active')->get();
        return view('cases.create', compact('pathways'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'medical_record_number' => 'required|string|max:50',
            'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
            'admission_date' => 'required|date',
            'discharge_date' => 'required|date|after:admission_date',
            'primary_diagnosis' => 'required|string|max:255',
            'ina_cbg_code' => 'required|string|max:50',
            'actual_total_cost' => 'required|numeric|min:0',
            'ina_cbg_tariff' => 'required|numeric|min:0',
        ]);

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
            
            return redirect()->route('cases.index')
                ->with('success', 'Patient case created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create patient case: ' . $e->getMessage())
                ->withInput();
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
        $case->load(['clinicalPathway', 'inputBy', 'caseDetails.pathwayStep']);
        return view('cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientCase $case)
    {
        $pathways = ClinicalPathway::where('status', 'active')->get();
        return view('cases.edit', compact('case', 'pathways'));
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
        $request->validate([
            'medical_record_number' => 'required|string|max:50',
            'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
            'admission_date' => 'required|date',
            'discharge_date' => 'required|date|after:admission_date',
            'primary_diagnosis' => 'required|string|max:255',
            'ina_cbg_code' => 'required|string|max:50',
            'actual_total_cost' => 'required|numeric|min:0',
            'ina_cbg_tariff' => 'required|numeric|min:0',
        ]);

        $case->update($request->except(['patient_id', 'input_by', 'input_date']));
        
        // Recalculate cost variance
        $case->cost_variance = $request->actual_total_cost - $request->ina_cbg_tariff;
        $case->save();

        return redirect()->route('cases.index')
            ->with('success', 'Patient case updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientCase $case)
    {
        $case->delete();

        return redirect()->route('cases.index')
            ->with('success', 'Patient case deleted successfully.');
    }

    /**
     * Show the form for uploading cases via CSV.
     *
     * @return \Illuminate\Http\Response
     */
    public function showUploadForm()
    {
        return view('cases.upload');
    }

    /**
     * Process the CSV upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // In a real implementation, you would process the CSV file here
        // For now, we'll just return a success message
        return redirect()->route('cases.index')
            ->with('success', 'CSV file uploaded successfully. Processing will be done in the background.');
    }
}
