<?php

namespace App\Http\Controllers;

use App\Models\CostReference;
use App\Models\ServiceVolume;
use App\Models\UnitCostCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
     * Search cost references for autocomplete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        $costReferences = CostReference::where('hospital_id', hospital('id'))
            ->where(function($q) use ($query) {
                $q->where('service_code', 'LIKE', "%{$query}%")
                  ->orWhere('service_description', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get(['id', 'service_code', 'service_description', 'standard_cost']);
        
        return response()->json($costReferences);
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

        // Only consider records that belong to the current hospital
        $costReferences = CostReference::where('hospital_id', hospital('id'))
            ->whereIn('id', $ids)
            ->get();

        $deleted = 0;
        $failedServiceVolumes = [];
        $failedUnitCostCalculations = [];
        $failedNames = [];

        foreach ($costReferences as $reference) {
            // Check if this cost reference is used in service volumes
            $inUseInServiceVolumes = ServiceVolume::where('hospital_id', hospital('id'))
                ->where('cost_reference_id', $reference->id)
                ->exists();

            if ($inUseInServiceVolumes) {
                $failedServiceVolumes[] = $reference;
                $failedNames[] = $reference->service_description ?: $reference->service_code;
                continue;
            }

            // Check if this cost reference is used in unit cost calculations
            $inUseInUnitCost = UnitCostCalculation::where('hospital_id', hospital('id'))
                ->where('cost_reference_id', $reference->id)
                ->exists();

            if ($inUseInUnitCost) {
                $failedUnitCostCalculations[] = $reference;
                $failedNames[] = $reference->service_description ?: $reference->service_code;
                continue;
            }

            // Safe to delete
            $reference->delete();
            $deleted++;
        }

        $totalFailed = count($failedServiceVolumes) + count($failedUnitCostCalculations);

        // Build user-friendly message
        $messages = [];

        if ($deleted > 0) {
            $messages[] = "{$deleted} cost reference" . ($deleted > 1 ? 's' : '') . " berhasil dihapus.";
        }

        if ($totalFailed > 0) {
            $failedMsg = "{$totalFailed} cost reference" . ($totalFailed > 1 ? 's' : '') . " tidak dapat dihapus";

            $reasons = [];
            if (count($failedServiceVolumes) > 0) {
                $reasons[] = count($failedServiceVolumes) . " digunakan di Service Volumes";
            }
            if (count($failedUnitCostCalculations) > 0) {
                $reasons[] = count($failedUnitCostCalculations) . " digunakan di Unit Cost Calculations";
            }

            if (count($reasons) > 0) {
                $failedMsg .= " karena " . implode(' dan ', $reasons) . ".";
            } else {
                $failedMsg .= ".";
            }

            if (count($failedNames) > 0) {
                $sampleNames = array_slice($failedNames, 0, 3);
                $failedMsg .= " Contoh: " . implode(', ', $sampleNames);
                if (count($failedNames) > 3) {
                    $failedMsg .= " dan " . (count($failedNames) - 3) . " lainnya";
                }
                $failedMsg .= '.';
            }

            $messages[] = $failedMsg;
        }

        $message = implode(' ', $messages);

        if ($deleted === 0 && $totalFailed > 0) {
            return redirect()->route('cost-references.index')
                ->with('error', $message ?: 'Tidak ada cost reference yang dapat dihapus.');
        }

        if ($deleted > 0 && $totalFailed > 0) {
            return redirect()->route('cost-references.index')
                ->with('warning', $message);
        }

        return redirect()->route('cost-references.index')
            ->with($deleted > 0 ? 'success' : 'error', $message ?: 'Tidak ada cost reference yang dipilih untuk dihapus.');
    }

    /**
     * Download template Excel file for importing cost references.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Service Code', 'Service Description', 'Standard Cost', 'Unit', 'Source'];
        $sheet->fromArray($headers, null, 'A1');

        // Example row
        $exampleRow = [
            'SRV001',
            'Contoh Layanan',
            '100000',
            'Kali',
            'Manual'
        ];
        $sheet->fromArray([$exampleRow], null, 'A2');

        // Format standard cost column
        $sheet->getStyle('C2:C100')
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $filename = 'cost_references_template.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }

    /**
     * Import cost references from Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row
            array_shift($rows);

            $imported = 0;
            $updated = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Expected format: Service Code, Service Description, Standard Cost, Unit, Source
                    $serviceCode = trim($row[0] ?? '');
                    $serviceDescription = trim($row[1] ?? '');
                    $standardCost = isset($row[2]) ? (float) str_replace([','], [''], (string) $row[2]) : 0;
                    $unit = trim($row[3] ?? '');
                    $source = trim($row[4] ?? 'Manual');

                    // Validation
                    if (empty($serviceCode) || empty($serviceDescription)) {
                        $errors[] = "Baris " . ($index + 2) . ": Service Code dan Service Description wajib diisi";
                        continue;
                    }

                    if ($standardCost < 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Standard Cost tidak boleh negatif";
                        continue;
                    }

                    if (empty($unit)) {
                        $errors[] = "Baris " . ($index + 2) . ": Unit wajib diisi";
                        continue;
                    }

                    // Check if cost reference exists (by service_code and hospital_id)
                    $existing = CostReference::where('hospital_id', hospital('id'))
                        ->where('service_code', $serviceCode)
                        ->first();

                    $data = [
                        'service_code' => $serviceCode,
                        'service_description' => $serviceDescription,
                        'standard_cost' => $standardCost,
                        'unit' => $unit,
                        'source' => $source,
                        'hospital_id' => hospital('id'),
                    ];

                    if ($existing) {
                        // Update existing
                        $existing->update($data);
                        $updated++;
                    } else {
                        // Create new
                        CostReference::create($data);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    continue;
                }
            }

            DB::commit();

            $message = "Import berhasil: {$imported} data baru ditambahkan";
            if ($updated > 0) {
                $message .= ", {$updated} data diperbarui";
            }
            if (!empty($errors)) {
                $message .= ". Terjadi " . count($errors) . " error: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " dan " . (count($errors) - 5) . " error lainnya";
                }
            }

            return redirect()->route('cost-references.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cost reference import failed', ['error' => $e->getMessage()]);
            return redirect()->route('cost-references.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
