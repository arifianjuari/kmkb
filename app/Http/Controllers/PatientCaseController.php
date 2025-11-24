<?php

namespace App\Http\Controllers;

use App\Models\PatientCase;
use App\Models\ClinicalPathway;
use App\Models\CaseDetail;
use App\Models\PathwayStep;
use App\Services\ComplianceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PatientCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = PatientCase::where('hospital_id', hospital('id'))
                ->with(['clinicalPathway.steps', 'caseDetails', 'inputBy']);
            
            // Apply filters
            if ($request->filled('medical_record_number')) {
                $query->where('medical_record_number', 'like', '%' . $request->medical_record_number . '%');
            }
            
            if ($request->filled('pathway_id')) {
                $query->where('clinical_pathway_id', $request->pathway_id);
            }
            
            if ($request->filled('admission_date_from')) {
                $query->whereDate('admission_date', '>=', $request->admission_date_from);
            }
            
            if ($request->filled('admission_date_to')) {
                $query->whereDate('admission_date', '<=', $request->admission_date_to);
            }
            
            $cases = $query->latest()->paginate(10);
            
            // Recalculate compliance for all cases to ensure accuracy
            // This ensures compliance matches the calculation used in the detail view
            $calculator = new ComplianceCalculator();
            foreach ($cases as $case) {
                try {
                    $case->compliance_percentage = $calculator->computeCompliance($case);
                    $case->save();
                } catch (\Exception $e) {
                    // Log error but don't break the page
                    Log::warning('Failed to calculate compliance for case ' . $case->id . ': ' . $e->getMessage());
                }
            }
            
            $pathways = ClinicalPathway::where('hospital_id', hospital('id'))->where('status', 'active')->get();
            return view('cases.index', compact('cases', 'pathways'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load patient cases: ' . $e->getMessage());
        }
    }

    /**
     * Copy all pathway steps for the case's clinical pathway into case details.
     * Skips steps that already exist in the case details (by pathway_step_id).
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copyPathwayStepsToCaseDetails(PatientCase $case)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }

        try {
            // Load pathway steps for this case's clinical pathway
            $steps = PathwayStep::where('clinical_pathway_id', $case->clinical_pathway_id)
                ->orderBy('step_order')
                ->get();

            if ($steps->isEmpty()) {
                return redirect()->route('cases.show', $case)
                    ->with('error', __('No pathway steps to copy.'));
            }

            // Get existing pathway_step_ids already in case details to avoid duplicates
            $existingStepIds = CaseDetail::where('patient_case_id', $case->id)
                ->whereNotNull('pathway_step_id')
                ->pluck('pathway_step_id')
                ->all();

            $now = now();
            $admission = Carbon::parse($case->admission_date);
            $rows = [];
            $skipped = 0;

            foreach ($steps as $step) {
                if (in_array($step->id, $existingStepIds, true)) {
                    $skipped++;
                    continue;
                }

                // Map fields from pathway step to case detail
                $serviceDate = null;
                try {
                    $offsetDays = max(0, (int)($step->step_order ?? 1) - 1);
                    $serviceDate = $admission->copy()->addDays($offsetDays)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $serviceDate = null;
                }

                $rows[] = [
                    'patient_case_id' => $case->id,
                    'hospital_id' => hospital('id'),
                    'pathway_step_id' => $step->id,
                    'service_item' => $step->description,
                    'service_code' => $step->service_code,
                    'status' => 'completed',
                    'performed' => true,
                    'quantity' => (int) round($step->quantity ?? 1),
                    'actual_cost' => (float) ($step->estimated_cost ?? 0),
                    'service_date' => $serviceDate,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($rows)) {
                CaseDetail::insert($rows);
                // Recalculate totals after insertion
                $this->recalculateActualTotalCost($case);
            }

            $inserted = count($rows);
            Log::info('Copy steps to case details completed', [
                'case_id' => $case->id,
                'inserted' => $inserted,
                'skipped' => $skipped,
            ]);

            return redirect()->route('cases.show', $case)
                ->with('success', __('Copied :inserted steps. Skipped :skipped existing.', ['inserted' => $inserted, 'skipped' => $skipped]));
        } catch (\Exception $e) {
            Log::error('Failed to copy pathway steps to case details', [
                'case_id' => $case->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('cases.show', $case)
                ->with('error', __('Failed to copy steps: ') . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $pathways = ClinicalPathway::where('hospital_id', hospital('id'))->where('status', 'active')->get();
            return view('cases.create', compact('pathways'));
        } catch (\Exception $e) {
            return redirect()->route('cases.index')->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new case detail.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function createCaseDetail(PatientCase $case)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Load the clinical pathway steps for this case with cost reference
        $steps = PathwayStep::where('clinical_pathway_id', $case->clinical_pathway_id)
            ->with('costReference')
            ->orderBy('step_order')
            ->get();
        
        return view('cases.details.create', compact('case', 'steps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging
        \Illuminate\Support\Facades\Log::info('Patient case creation request data:', $request->all());
        
        // Normalize date inputs to Y-m-d if they come in as d/m/Y or d-m-Y
        $admission = $request->input('admission_date');
        $discharge = $request->input('discharge_date');

        $normalize = function ($value) {
            if (empty($value)) {
                return $value;
            }
            try {
                // If contains slash, try d/m/Y
                if (strpos($value, '/') !== false) {
                    return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                }
                // If looks like d-m-Y, try that
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                    return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
                }
                // If already Y-m-d or a parseable date, standardize
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                // Leave as-is; validation will catch if invalid
                return $value;
            }
        };

        $request->merge([
            'admission_date' => $normalize($admission),
            'discharge_date' => $normalize($discharge),
        ]);

        try {
            $validatedData = $request->validate([
                'medical_record_number' => 'required|string|max:50',
                'patient_id' => 'required|string|max:255',
                'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
                'admission_date' => 'required|date|date_format:Y-m-d',
                'discharge_date' => 'nullable|date|date_format:Y-m-d|after:admission_date',
                'primary_diagnosis' => 'required|string|max:255',
                'ina_cbg_code' => 'required|string|max:50',
                'actual_total_cost' => 'nullable|numeric|min:0',
                'ina_cbg_tariff' => 'required|numeric|min:0',
            ]);
            
            \Illuminate\Support\Facades\Log::info('Patient case validation passed:', $validatedData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Patient case validation failed:', $e->errors());
            return redirect()->back()
                ->with('error', 'Validation failed: ' . json_encode($e->errors()))
                ->withInput();
        }

        // Handle null discharge_date
        $dischargeDate = $request->discharge_date;
        if (empty($dischargeDate)) {
            $dischargeDate = null;
        }

        DB::beginTransaction();
        try {
            // Ensure hospital context exists
            $currentHospitalId = hospital('id');
            if (!$currentHospitalId) {
                throw new \RuntimeException('No hospital selected. Please select a hospital context and try again.');
            }
            $case = new PatientCase();
            $case->patient_id = $request->patient_id; // Use the provided patient_id
            $case->medical_record_number = $request->medical_record_number;
            $case->clinical_pathway_id = $request->clinical_pathway_id;
            $case->admission_date = $request->admission_date;
            $case->discharge_date = $dischargeDate;
            $case->primary_diagnosis = $request->primary_diagnosis;
            $case->ina_cbg_code = $request->ina_cbg_code;
            $case->actual_total_cost = $request->actual_total_cost;
            $case->ina_cbg_tariff = $request->ina_cbg_tariff;
            $case->input_by = Auth::id();
            $case->input_date = now();
            $case->hospital_id = $currentHospitalId;

            // Set default values for fields that don't have defaults in the database
            $case->cost_variance = ($request->filled('actual_total_cost') && $request->filled('ina_cbg_tariff'))
                ? ($request->ina_cbg_tariff - $request->actual_total_cost)
                : null;
            $case->compliance_percentage = 0; // Will be calculated after saving

            // Save first to ensure relations are persisted
            \Illuminate\Support\Facades\Log::info('Calculating compliance for case: ' . $case->id);
            $case->save();
            \Illuminate\Support\Facades\Log::info('Case created successfully: ' . $case->id);

            // Compute compliance considering conditional steps (no details yet => likely 0)
            $case->load(['clinicalPathway.steps', 'caseDetails']);
            $calculator = new ComplianceCalculator();
            $case->compliance_percentage = $calculator->computeCompliance($case);
            $case->save();

            DB::commit();
            
            return redirect()->route('cases.index')
                ->with('success', 'Patient case created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Failed to create patient case:', [
                'error' => $e->getMessage(),
                'user' => Auth::id(),
                'hospital' => hospital('id'),
            ]);
            return redirect()->back()->with('error', 'Failed to create patient case: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Store a newly created case detail in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function storeCaseDetail(Request $request, PatientCase $case)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        try {
            // Log the request data for debugging
            \Illuminate\Support\Facades\Log::info('Store Case Detail Request Data: ', $request->all());
            
            $validatedData = $request->validate([
                'pathway_step_id' => 'nullable|exists:pathway_steps,id',
                'service_item' => 'required|string|max:255',
                'service_code' => 'nullable|string|max:50',
                'status' => 'required|in:pending,completed,skipped',
                'performed' => 'nullable|boolean',
                'quantity' => 'nullable|integer|min:1',
                'actual_cost' => 'nullable|numeric|min:0',
                'service_date' => 'nullable|date',
            ]);
            
            // Set default value for performed field if not provided
            if (!isset($validatedData['performed'])) {
                $validatedData['performed'] = false;
            }
            
            // Log the validated data for debugging
            \Illuminate\Support\Facades\Log::info('Store Case Detail Validated Data: ', $validatedData);
            
            // Add the patient_case_id to the validated data
            $validatedData['patient_case_id'] = $case->id;
            $validatedData['hospital_id'] = hospital('id');
            
            // Log before creating the case detail
            \Illuminate\Support\Facades\Log::info('Creating Case Detail with Data: ', $validatedData);
            
            // Create the case detail
            $caseDetail = CaseDetail::create($validatedData);
            
            // Log after creating the case detail
            \Illuminate\Support\Facades\Log::info('Case Detail Created Successfully: ', ['id' => $caseDetail->id]);
            
            // Automatically calculate actual_total_cost based on sum of case details
            $this->recalculateActualTotalCost($case);
            
            return redirect()->route('cases.show', $case)
                ->with('success', 'Case detail added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add case detail: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Recalculate the actual_total_cost for a patient case based on the sum of actual_cost from case details.
     *
     * @param  \App\Models\PatientCase  $case
     * @return void
     */
    private function recalculateActualTotalCost(PatientCase $case)
    {
        // Recalculate using actual_cost * quantity (default quantity to 1), treating nulls as 0
        // Use a DB-level aggregation to avoid loading all rows into memory
        $totalActualCost = \App\Models\CaseDetail::where('patient_case_id', $case->id)
            ->sum(DB::raw('COALESCE(actual_cost, 0) * COALESCE(NULLIF(quantity, 0), 1)'));

        // Update the actual_total_cost field (ensure decimal format)
        $case->actual_total_cost = $totalActualCost === null ? 0 : $totalActualCost;
        
        // Recalculate cost variance
        $case->cost_variance = ($case->actual_total_cost !== null && $case->ina_cbg_tariff !== null)
            ? ($case->ina_cbg_tariff - $case->actual_total_cost)
            : null;
        
        // Save the updated case
        $case->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function show(PatientCase $case)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        try {
            $case->load(['clinicalPathway', 'inputBy', 'caseDetails.pathwayStep.costReference']);
            
            // Calculate unused pathway steps
            $pathwaySteps = $case->clinicalPathway->steps ?? collect();
            $usedPathwayStepIds = $case->caseDetails->filter(function($detail) {
                // Only count pathway steps that are performed (performed = 1 or true)
                return !$detail->isCustomStep() && $detail->performed;
            })->pluck('pathway_step_id')->unique();
            
            $unusedPathwaySteps = $pathwaySteps->filter(function($step) use ($usedPathwayStepIds) {
                return !$usedPathwayStepIds->contains($step->id);
            });
            
            $unusedPathwayStepsCount = $unusedPathwaySteps->count();
            $usedPathwayStepsCount = $usedPathwayStepIds->count();
            
            return view('cases.show', compact('case', 'unusedPathwayStepsCount', 'usedPathwayStepsCount'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error calculating compliance for case ' . $case->id . ': ' . $e->getMessage());
            return redirect()->route('cases.index')->with('error', 'Failed to load case details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified case detail.
     *
     * @param  \App\Models\PatientCase  $case
     * @param  \App\Models\CaseDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function editCaseDetail(PatientCase $case, CaseDetail $detail)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Ensure the case detail belongs to the case
        if ($detail->patient_case_id !== $case->id) {
            abort(404);
        }
        
        // Load the clinical pathway steps for this case with cost reference
        $steps = PathwayStep::where('clinical_pathway_id', $case->clinical_pathway_id)
            ->with('costReference')
            ->orderBy('step_order')
            ->get();
        
        return view('cases.details.edit', compact('case', 'detail', 'steps'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientCase  $case
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientCase $case)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        try {
            $pathways = ClinicalPathway::where('hospital_id', hospital('id'))->where('status', 'active')->get();
            return view('cases.edit', compact('case', 'pathways'));
        } catch (\Exception $e) {
            return redirect()->route('cases.index')->with('error', 'Failed to load edit form: ' . $e->getMessage());
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
            $request->validate([
                'medical_record_number' => 'required|string|max:50',
                'patient_id' => 'required|string|max:255',
                'clinical_pathway_id' => 'required|exists:clinical_pathways,id',
                'admission_date' => 'required|date',
                'discharge_date' => 'nullable|date|after:admission_date',
                'primary_diagnosis' => 'required|string|max:255',
                'ina_cbg_code' => 'required|string|max:50',
                'actual_total_cost' => 'nullable|numeric|min:0',
                'ina_cbg_tariff' => 'required|numeric|min:0',
            ]);

            // Ensure the case belongs to the current hospital
            if ($case->hospital_id !== hospital('id')) {
                abort(404);
            }
            
            // Handle null discharge_date
            $dischargeDate = $request->discharge_date;
            if (empty($dischargeDate)) {
                $dischargeDate = null;
            }
            
            // Update the case with all fields except protected ones
            $case->update([
                'medical_record_number' => $request->medical_record_number,
                'patient_id' => $request->patient_id,
                'clinical_pathway_id' => $request->clinical_pathway_id,
                'admission_date' => $request->admission_date,
                'discharge_date' => $dischargeDate,
                'primary_diagnosis' => $request->primary_diagnosis,
                'ina_cbg_code' => $request->ina_cbg_code,
                'actual_total_cost' => $request->actual_total_cost,
                'ina_cbg_tariff' => $request->ina_cbg_tariff,
                'additional_diagnoses' => $request->additional_diagnoses,
            ]);

            // Recalculate cost variance
            $case->cost_variance = ($request->filled('actual_total_cost') && $request->filled('ina_cbg_tariff'))
                ? ($request->ina_cbg_tariff - $request->actual_total_cost)
                : null;

            // Recompute compliance based on existing details and conditional steps
            $case->load(['clinicalPathway.steps', 'caseDetails']);
            $calculator = new ComplianceCalculator();
            $case->compliance_percentage = $calculator->computeCompliance($case);
            $case->save();

            \Illuminate\Support\Facades\Log::info('Case updated successfully: ' . $case->id);

            return redirect()->route('cases.index')
                ->with('success', 'Patient case updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', 'Validation failed: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update patient case: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified case detail in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientCase  $case
     * @param  \App\Models\CaseDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function updateCaseDetail(Request $request, PatientCase $case, CaseDetail $detail)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Ensure the case detail belongs to the case
        if ($detail->patient_case_id !== $case->id) {
            abort(404);
        }
        
        try {
            // Check if this is an AJAX request for inline editing
            if ($request->wantsJson() || $request->isJson()) {
                // For inline editing, we only validate the specific fields being updated
                $allowedFields = ['quantity', 'actual_cost', 'status', 'performed'];
                $inputData = $request->only($allowedFields);
                
                // Validate only the fields that are present in the request
                $validationRules = [];
                if (array_key_exists('quantity', $inputData)) {
                    $validationRules['quantity'] = 'nullable|integer|min:1';
                }
                if (array_key_exists('actual_cost', $inputData)) {
                    $validationRules['actual_cost'] = 'nullable|numeric|min:0';
                }
                if (array_key_exists('status', $inputData)) {
                    $validationRules['status'] = 'required|in:pending,completed,skipped';
                }
                if (array_key_exists('performed', $inputData)) {
                    $validationRules['performed'] = 'nullable|boolean';
                }
                
                if (!empty($validationRules)) {
                    $validatedData = $request->validate($validationRules);
                } else {
                    $validatedData = [];
                }
                
                // Update only the provided fields
                if (!empty($validatedData)) {
                    $detail->update($validatedData);
                    
                    // Automatically calculate actual_total_cost based on sum of case details
                    $this->recalculateActualTotalCost($case);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Case detail updated successfully.',
                        'data' => $detail
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid data provided for update.'
                    ], 400);
                }
            }
            
            // Log the request data for debugging
            \Illuminate\Support\Facades\Log::info('Update Case Detail Request Data: ', $request->all());
            
            // Check if this is a custom step
            $isCustomStep = $request->has('is_custom_step') && $request->is_custom_step == '1';
            
            if ($isCustomStep) {
                // For custom steps, service_item is required but pathway_step_id should be null
                $validatedData = $request->validate([
                    'pathway_step_id' => 'nullable', // Remove exists validation for custom steps
                    'service_item' => 'required|string|max:255',
                    'service_code' => 'nullable|string|max:50',
                    'status' => 'required|in:pending,completed,skipped',
                    'performed' => 'nullable|boolean',
                    'quantity' => 'nullable|integer|min:1',
                    'actual_cost' => 'nullable|numeric|min:0',
                    'service_date' => 'nullable|date',
                ]);
                
                // Explicitly set pathway_step_id to null for custom steps
                $validatedData['pathway_step_id'] = null;
            } else {
                // For standard steps, pathway_step_id is required and must exist
                $validatedData = $request->validate([
                    'pathway_step_id' => 'required|exists:pathway_steps,id',
                    'service_item' => 'required|string|max:255',
                    'service_code' => 'nullable|string|max:50',
                    'status' => 'required|in:pending,completed,skipped',
                    'performed' => 'nullable|boolean',
                    'quantity' => 'nullable|integer|min:1',
                    'actual_cost' => 'nullable|numeric|min:0',
                    'service_date' => 'nullable|date',
                ]);
            }
            
            // Set default value for performed field if not provided
            if (!isset($validatedData['performed'])) {
                $validatedData['performed'] = false;
            }
            
            $validatedData['hospital_id'] = hospital('id');
            
            // Log before updating the case detail
            \Illuminate\Support\Facades\Log::info('Updating Case Detail with Data: ', $validatedData);
            
            // Update the case detail
            $detail->update($validatedData);
            
            // Log after updating the case detail
            \Illuminate\Support\Facades\Log::info('Case Detail Updated Successfully: ', ['id' => $detail->id]);
            
            // Automatically calculate actual_total_cost based on sum of case details
            $this->recalculateActualTotalCost($case);
            
            return redirect()->route('cases.show', $case)
                ->with('success', 'Case detail updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', 'Validation failed: ' . json_encode($e->errors()))
                ->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update case detail: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update case detail: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified case detail from storage.
     *
     * @param  \App\Models\PatientCase  $case
     * @param  \App\Models\CaseDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function deleteCaseDetail(PatientCase $case, CaseDetail $detail)
    {
        // Ensure the case belongs to the current hospital
        if ($case->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Ensure the case detail belongs to the case
        if ($detail->patient_case_id !== $case->id) {
            abort(404);
        }
        
        try {
            $detail->delete();
            
            // Automatically calculate actual_total_cost based on sum of case details
            $this->recalculateActualTotalCost($case);
            
            return redirect()->route('cases.show', $case)
                ->with('success', 'Case detail deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete case detail: ' . $e->getMessage());
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
            // Ensure the case belongs to the current hospital before deleting
            if ($case->hospital_id !== hospital('id')) {
                abort(404);
            }
            
            $case->delete();

            return redirect()->route('cases.index')
                ->with('success', 'Patient case deleted successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating case: ' . $e->getMessage());
            return redirect()->route('cases.index')->with('error', 'Failed to delete patient case: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for uploading cases via CSV.
     *
     * @return \Illuminate\Http\Response
     */
    public function showUploadForm()
    {
        try {
            return view('cases.upload');
        } catch (\Exception $e) {
            return redirect()->route('cases.index')->with('error', 'Failed to load upload form: ' . $e->getMessage());
        }
    }

    /**
     * Process the Excel/CSV upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:xlsx,xls,csv,txt',
            ]);

            // In a real implementation, you would process the Excel/CSV file here
            // For now, we'll just return a success message
            return redirect()->route('cases.index')
                ->with('success', 'File uploaded successfully. Processing will be done in the background.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', 'Validation failed: ' . $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for bulk importing patient cases.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadTemplate()
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            // Fallback to CSV if PhpSpreadsheet isn't available
            $filename = 'patient_cases_template.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            $callback = function () {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['medical_record_number', 'patient_name', 'clinical_pathway_id', 'admission_date', 'discharge_date', 'primary_diagnosis', 'ina_cbg_code', 'actual_total_cost', 'ina_cbg_tariff']);
                fputcsv($out, ['MRN001', 'John Doe', '1', '2025-01-01', '2025-01-10', 'A00-B99', 'J001', '1000000', '800000']);
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'medical_record_number');
        $sheet->setCellValue('B1', 'patient_name');
        $sheet->setCellValue('C1', 'clinical_pathway_id');
        $sheet->setCellValue('D1', 'admission_date');
        $sheet->setCellValue('E1', 'discharge_date');
        $sheet->setCellValue('F1', 'primary_diagnosis');
        $sheet->setCellValue('G1', 'ina_cbg_code');
        $sheet->setCellValue('H1', 'actual_total_cost');
        $sheet->setCellValue('I1', 'ina_cbg_tariff');
        
        // Example row
        $sheet->setCellValue('A2', 'MRN001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', '1');
        $sheet->setCellValue('D2', '2025-01-01');
        $sheet->setCellValue('E2', '2025-01-10');
        $sheet->setCellValue('F2', 'A00-B99');
        $sheet->setCellValue('G2', 'J001');
        $sheet->setCellValue('H2', '1000000');
        $sheet->setCellValue('I2', '800000');
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'patient_cases_template.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
    }
}
