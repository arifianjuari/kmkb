<?php

namespace App\Http\Controllers;

use App\Models\CostReference;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CostReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = CostReference::where('hospital_id', hospital('id'));
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%")
                  ->orWhere('unit', 'LIKE', "%{$search}%")
                  ->orWhere('source', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CAST(standard_cost AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }
        
        $costReferences = $query->latest()->paginate(10)->appends(['search' => $search]);
        
        return view('cost-references.index', compact('costReferences', 'search'));
    }

    /**
     * Export cost references to Excel for the current hospital.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $search = $request->get('search');
        
        $query = CostReference::where('hospital_id', hospital('id'))
            ->orderBy('service_code');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%")
                  ->orWhere('unit', 'LIKE', "%{$search}%")
                  ->orWhere('source', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CAST(standard_cost AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }
        
        $data = $query->get(['service_code', 'service_description', 'standard_cost', 'unit', 'source']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Service Code', 'Description', 'Standard Cost', 'Unit', 'Source'];
        $sheet->fromArray($headers, null, 'A1');

        // Rows
        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->service_code,
                    $item->service_description,
                    (float) $item->standard_cost,
                    $item->unit,
                    $item->source,
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        // Formats and autosize
        $sheet->getStyle('C2:C' . max(2, $data->count() + 1))
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'cost_references_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cost-references.create');
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
            'service_code' => 'required|string|max:50',
            'service_description' => 'required|string',
            'standard_cost' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'source' => 'required|string|max:20',
        ]);

        $costReference = CostReference::create(array_merge($request->all(), ['hospital_id' => hospital('id')]));

        // If the request expects JSON (AJAX), return the created model instead of redirect
        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $costReference,
                'message' => 'Cost reference created successfully.'
            ], 201);
        }

        return redirect()->route('cost-references.index')
            ->with('success', 'Cost reference created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function show(CostReference $costReference)
    {
        // Ensure the cost reference belongs to the current hospital
        if ($costReference->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('cost-references.show', compact('costReference'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function edit(CostReference $costReference)
    {
        // Ensure the cost reference belongs to the current hospital
        if ($costReference->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('cost-references.edit', compact('costReference'));
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
        $request->validate([
            'service_code' => 'required|string|max:50',
            'service_description' => 'required|string',
            'standard_cost' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'source' => 'required|string|max:20',
        ]);

        $costReference->update($request->all());

        return redirect()->route('cost-references.index')
            ->with('success', 'Cost reference updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CostReference  $costReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostReference $costReference)
    {
        // Ensure the cost reference belongs to the current hospital before deleting
        if ($costReference->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $costReference->delete();

        return redirect()->route('cost-references.index')
            ->with('success', 'Cost reference deleted successfully.');
    }

    /**
     * Bulk delete selected cost references.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = $validated['ids'];

        // Only delete records that belong to the current hospital
        $deleted = CostReference::where('hospital_id', hospital('id'))
            ->whereIn('id', $ids)
            ->delete();

        return redirect()->route('cost-references.index')
            ->with('success', $deleted > 0 ? 'Selected cost references deleted successfully.' : 'No cost references were deleted.');
    }
}
