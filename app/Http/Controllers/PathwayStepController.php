<?php

namespace App\Http\Controllers;

use App\Models\PathwayStep;
use App\Models\CostReference;
use App\Models\ClinicalPathway;
use App\Services\UnitCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PathwayStepController extends Controller
{
    protected $unitCostService;

    public function __construct(UnitCostService $unitCostService)
    {
        $this->unitCostService = $unitCostService;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ClinicalPathway $pathway)
    {
        Log::info('PathwayStepController@store called', [
            'pathway_id' => $pathway->id,
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'day' => 'required|integer|min:1',
            'category' => 'required|string|in:Administrasi,Penilaian dan Pemantauan Medis,Penilaian dan Pemantauan Keperawatan,Pemeriksaan Penunjang Medik,Tindakan Medis,Tindakan Keperawatan,Medikasi,BHP,Nutrisi,Kegiatan,Konsultasi dan Komunikasi Tim,Konseling Psikososial,Pendidikan dan Komunikasi dengan Pasien/Keluarga,Kriteria KRS',
            'activity' => 'required|string',
            'description' => 'required|string',
            'standard_cost' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'criteria' => 'nullable|string',
            'cost_reference_id' => 'nullable|exists:cost_references,id',
        ]);

        $step = new PathwayStep();
        // Ensure multi-tenant scoping
        $step->hospital_id = $pathway->hospital_id;
        $step->clinical_pathway_id = $pathway->id;
        $step->step_order = $request->day;
        $step->display_order = $request->day; // keep initial display order aligned with day
        $step->category = $request->category;
        $step->service_code = $request->activity;
        $step->description = $request->description;
        $step->quantity = $request->input('quantity', 1);
        $step->criteria = $request->criteria ?? '';
        $step->cost_reference_id = $request->cost_reference_id;
        
        // Auto-fill unit cost if cost_reference_id is provided
        $this->fillUnitCostForStep($step, $pathway, $request->standard_cost);
        
        $step->save();
        
        Log::info('PathwayStep created', ['step_id' => $step->id]);

        // Return according to request expectations (AJAX vs normal form post)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Step added successfully',
                'step' => $step,
            ]);
        }

        return redirect()->back()->with('success', 'Step added successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClinicalPathway  $pathway
     * @param  \App\Models\PathwayStep  $step
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClinicalPathway $pathway, PathwayStep $step)
    {
        // Validate request data - making some fields optional to match frontend behavior
        $validatedData = $request->validate([
            'day' => 'required|integer|min:1',
            'category' => 'required|string|in:Administrasi,Penilaian dan Pemantauan Medis,Penilaian dan Pemantauan Keperawatan,Pemeriksaan Penunjang Medik,Tindakan Medis,Tindakan Keperawatan,Medikasi,BHP,Nutrisi,Kegiatan,Konsultasi dan Komunikasi Tim,Konseling Psikososial,Pendidikan dan Komunikasi dengan Pasien/Keluarga,Kriteria KRS',
            'activity' => 'required|string',
            'description' => 'required|string',
            'standard_cost' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'criteria' => 'nullable|string',
            'cost_reference_id' => 'nullable|exists:cost_references,id',
        ]);

        try {
            // Ensure tenant consistency on update as well
            $step->hospital_id = $pathway->hospital_id;
            $step->step_order = $validatedData['day'];
            // Preserve display_order when updating other fields
            // display_order is only changed via reorder functionality
            $step->category = $validatedData['category'];
            $step->service_code = $validatedData['activity'];
            $step->description = $validatedData['description'];
            $step->quantity = $validatedData['quantity'] ?? 1;
            $step->criteria = $validatedData['criteria'] ?? '';
            $step->cost_reference_id = $validatedData['cost_reference_id'] ?? null;
            
            // Auto-fill unit cost if cost_reference_id is provided
            $this->fillUnitCostForStep($step, $pathway, $validatedData['standard_cost']);
            
            $step->save();

            return response()->json([
                'success' => true,
                'message' => 'Step updated successfully',
                'data' => $step
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update step: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @param  \App\Models\PathwayStep  $step
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClinicalPathway $pathway, PathwayStep $step)
    {
        $step->delete();

        return redirect()->back()->with('success', 'Step deleted successfully');
    }

    /**
     * Reorder steps for a given pathway.
     * Expected payload: { order: [ {id: number, position: number}, ... ] }
     */
    public function reorder(Request $request, ClinicalPathway $pathway)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:pathway_steps,id',
            'order.*.position' => 'required|integer|min:1',
        ]);

        $ids = collect($data['order'])->pluck('id')->all();
        $steps = PathwayStep::where('clinical_pathway_id', $pathway->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        if ($steps->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more steps do not belong to this pathway.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($data['order'] as $item) {
                /** @var PathwayStep $s */
                $s = $steps[$item['id']];
                $s->display_order = (int) $item['position'];
                $s->save();
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to reorder steps (display_order)', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save order.'
            ], 500);
        }
    }

    /**
     * Download CSV template for bulk importing steps.
     */
    public function downloadTemplate(ClinicalPathway $pathway)
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            // Fallback to CSV if PhpSpreadsheet isn't available
            $filename = 'pathway_steps_template.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            $callback = function () {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['day', 'category', 'activity', 'description', 'criteria', 'standard_cost', 'quantity', 'cost_reference_id']);
                fputcsv($out, [1, 'Administrasi', 'Contoh tindakan', 'Deskripsi singkat', '', '0.00', '1', '']);
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Headers
        $sheet->setCellValue('A1', 'day');
        $sheet->setCellValue('B1', 'category');
        $sheet->setCellValue('C1', 'activity');
        $sheet->setCellValue('D1', 'description');
        $sheet->setCellValue('E1', 'criteria');
        $sheet->setCellValue('F1', 'standard_cost');
        $sheet->setCellValue('G1', 'quantity');
        $sheet->setCellValue('H1', 'cost_reference_id');
        // Example row
        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', 'Administrasi');
        $sheet->setCellValue('C2', 'Contoh tindakan');
        $sheet->setCellValue('D2', 'Deskripsi singkat');
        $sheet->setCellValue('E2', '');
        $sheet->setCellValue('F2', '0.00');
        $sheet->setCellValue('G2', '1');
        $sheet->setCellValue('H2', '');
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'pathway_steps_template.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import steps from uploaded CSV file.
     */
    public function import(Request $request, ClinicalPathway $pathway)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->getRealPath();
        if (!$path) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        $created = 0;
        $errors = [];
        $ext = strtolower($uploaded->getClientOriginalExtension() ?: '');

        // Helper to process one row into a step
        $processRow = function ($day, $category, $activity, $description, $criteria, $standardCostRaw, $quantityRaw, $costRefIdRaw) use ($pathway, &$errors, &$created) {
            $standardCost = (float) str_replace([','], ['',], (string) $standardCostRaw);
            $qStrRaw = ($quantityRaw === '' || $quantityRaw === null) ? '1' : (string) $quantityRaw;
            $qStr = str_replace(',', '', trim($qStrRaw));
            // validate integer quantity >= 1
            $isNumeric = is_numeric($qStr);
            $isInteger = $isNumeric && ((int)$qStr == (float)$qStr);
            if (!$isInteger || (int)$qStr < 1) {
                $errors[] = "Invalid quantity (must be integer >= 1) on row: day=$day, activity='" . (string)$activity . "'";
                return;
            }
            $quantity = (int) $qStr;
            $costRefId = ($costRefIdRaw !== '' && $costRefIdRaw !== null) ? (int) $costRefIdRaw : null;

            if ((int)$day < 1 || trim((string)$category) === '' || trim((string)$activity) === '' || trim((string)$description) === '' || $standardCost < 0) {
                $errors[] = "Invalid data on row: day=$day, category='" . (string)$category . "', activity='" . (string)$activity . "'";
                return;
            }

            if ($costRefId && !CostReference::where('id', $costRefId)->exists()) {
                $errors[] = "Unknown cost_reference_id '$costRefId' for activity '" . (string)$activity . "' (day $day)";
                return;
            }

            $step = new PathwayStep();
            // Set tenant from parent pathway for NOT NULL hospital_id
            $step->hospital_id = $pathway->hospital_id;
            $step->clinical_pathway_id = $pathway->id;
            $step->step_order = (int) $day;
            $step->display_order = (int) $day;
            $step->category = (string) $category;
            $step->service_code = (string) $activity;
            $step->description = (string) $description;
            $step->criteria = (string) ($criteria ?? '');
            $step->estimated_cost = (float) $standardCost;
            $step->quantity = (int) $quantity;
            $step->cost_reference_id = $costRefId;
            $step->save();

            $created++;
        };

        DB::beginTransaction();
        try {
            if (in_array($ext, ['xlsx', 'xls'])) {
                if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Excel import belum tersedia. Mohon instal paket phpoffice/phpspreadsheet lalu coba lagi.');
                }
                $spreadsheet = IOFactory::load($path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, true); // keyed by letters
                if (empty($rows)) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Empty or invalid Excel file.');
                }
                $header = array_shift($rows); // first row
                // Build map from lower(header) to column letter
                $colMap = [];
                foreach ($header as $letter => $value) {
                    $colMap[strtolower(trim((string)$value))] = $letter;
                }
                foreach ($rows as $r) {
                    // Skip empty rows
                    if (count(array_filter($r, fn($v) => trim((string)$v) !== '')) === 0) continue;
                    $day = $r[$colMap['day']] ?? 0;
                    $category = $r[$colMap['category']] ?? '';
                    $activity = $r[$colMap['activity']] ?? '';
                    $description = $r[$colMap['description']] ?? '';
                    $criteria = $colMap['criteria'] ?? null; $criteria = $criteria ? ($r[$criteria] ?? '') : '';
                    $standardCost = $r[$colMap['standard_cost']] ?? '0';
                    $quantity = $colMap['quantity'] ?? null; $quantity = $quantity ? ($r[$quantity] ?? '1') : '1';
                    $costRefId = $colMap['cost_reference_id'] ?? null; $costRefId = $costRefId ? ($r[$costRefId] ?? '') : '';
                    $processRow($day, $category, $activity, $description, $criteria, $standardCost, $quantity, $costRefId);
                }
            } else {
                if (($handle = fopen($path, 'r')) === false) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Unable to read the uploaded file.');
                }
                $header = fgetcsv($handle);
                if (!$header) {
                    fclose($handle);
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Empty or invalid CSV file.');
                }
                $map = [];
                foreach ($header as $idx => $col) {
                    $map[strtolower(trim((string)$col))] = $idx;
                }
                while (($row = fgetcsv($handle)) !== false) {
                    if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) continue;
                    $get = function ($key, $default = null) use ($map, $row) {
                        if (!isset($map[$key])) return $default;
                        $val = $row[$map[$key]] ?? $default;
                        return is_string($val) ? trim($val) : $val;
                    };
                    $day = (int) $get('day', 0);
                    $category = (string) $get('category', '');
                    $activity = (string) $get('activity', '');
                    $description = (string) $get('description', '');
                    $criteria = (string) $get('criteria', '');
                    $standardCost = (string) $get('standard_cost', '0');
                    $quantity = (string) $get('quantity', '1');
                    $costRefId = $get('cost_reference_id', '');
                    $processRow($day, $category, $activity, $description, $criteria, $standardCost, $quantity, $costRefId);
                }
                fclose($handle);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Import steps failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $msg = "$created steps imported.";
        if (!empty($errors)) {
            $msg .= ' Some rows were skipped: ' . implode(' | ', $errors);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Helper method to fill unit cost for a pathway step.
     *
     * @param PathwayStep $step
     * @param ClinicalPathway $pathway
     * @param float|null $fallbackCost Fallback cost if unit cost is not available
     * @return void
     */
    protected function fillUnitCostForStep(PathwayStep $step, ClinicalPathway $pathway, $fallbackCost = null)
    {
        if ($step->cost_reference_id) {
            // Get unit cost version from pathway, or use latest
            $version = $pathway->unit_cost_version;
            
            // Get unit cost from service
            $unitCostData = $this->unitCostService->getUnitCost(
                $step->cost_reference_id,
                $version
            );
            
            // Set unit cost applied and version
            $step->unit_cost_applied = $unitCostData['unit_cost'];
            $step->source_unit_cost_version = $unitCostData['version_label'];
            
            // Set estimated cost = unit_cost_applied * quantity
            $step->estimated_cost = $unitCostData['unit_cost'] * ($step->quantity ?? 1);
        } else {
            // No cost reference, use fallback cost if provided
            if ($fallbackCost !== null) {
                $step->estimated_cost = $fallbackCost;
            }
            $step->unit_cost_applied = null;
            $step->source_unit_cost_version = null;
        }
    }
}
