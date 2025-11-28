<?php

namespace App\Http\Controllers\Pathway;

use App\Http\Controllers\Controller;
use App\Models\ClinicalPathway;
use App\Models\PathwayStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PathwayTemplateController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = hospital('id');
        
        $search = $request->get('search');
        $status = $request->get('status', 'approved'); // approved, all
        
        // Get pathways that can be used as templates
        $query = ClinicalPathway::where('hospital_id', $hospitalId)
            ->with(['creator', 'steps'])
            ->withCount('steps');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('diagnosis_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status === 'approved') {
            $query->where('status', 'approved');
        }
        
        $pathways = $query->orderBy('name')
            ->orderBy('version', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Statistics
        $stats = [
            'total_templates' => ClinicalPathway::where('hospital_id', $hospitalId)
                ->where('status', 'approved')
                ->count(),
            'total_pathways' => ClinicalPathway::where('hospital_id', $hospitalId)->count(),
            'avg_steps' => ClinicalPathway::where('hospital_id', $hospitalId)
                ->where('status', 'approved')
                ->withCount('steps')
                ->get()
                ->avg('steps_count') ?? 0,
        ];
        
        return view('pathways.templates', compact(
            'pathways',
            'search',
            'status',
            'stats'
        ));
    }
    
    /**
     * Export a pathway as template (Excel format with pathway header + steps)
     */
    public function export(ClinicalPathway $pathway)
    {
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            return redirect()->back()->with('error', 'Excel export belum tersedia. Mohon instal paket phpoffice/phpspreadsheet.');
        }
        
        $pathway->load('steps.costReference');
        
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Pathway Header
        $headerSheet = $spreadsheet->getActiveSheet();
        $headerSheet->setTitle('Pathway Header');
        $headerSheet->setCellValue('A1', 'Field');
        $headerSheet->setCellValue('B1', 'Value');
        
        $headerData = [
            ['name', $pathway->name],
            ['description', $pathway->description ?? ''],
            ['diagnosis_code', $pathway->diagnosis_code ?? ''],
            ['version', $pathway->version ?? '1.0.0'],
            ['unit_cost_version', $pathway->unit_cost_version ?? ''],
            ['effective_date', $pathway->effective_date ? $pathway->effective_date->format('Y-m-d') : ''],
        ];
        
        $row = 2;
        foreach ($headerData as $data) {
            $headerSheet->setCellValue('A' . $row, $data[0]);
            $headerSheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }
        
        $headerSheet->getColumnDimension('A')->setWidth(20);
        $headerSheet->getColumnDimension('B')->setWidth(50);
        
        // Sheet 2: Pathway Steps
        $stepsSheet = $spreadsheet->createSheet();
        $stepsSheet->setTitle('Pathway Steps');
        
        $headers = ['day', 'category', 'activity', 'description', 'criteria', 'standard_cost', 'quantity', 'cost_reference_id', 'cost_reference_code'];
        $col = 'A';
        foreach ($headers as $header) {
            $stepsSheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        $row = 2;
        foreach ($pathway->steps as $step) {
            $stepsSheet->setCellValue('A' . $row, $step->day ?? '');
            $stepsSheet->setCellValue('B' . $row, $step->category ?? '');
            $stepsSheet->setCellValue('C' . $row, $step->activity ?? '');
            $stepsSheet->setCellValue('D' . $row, $step->description ?? '');
            $stepsSheet->setCellValue('E' . $row, $step->criteria ?? '');
            $stepsSheet->setCellValue('F' . $row, $step->estimated_cost ?? $step->standard_cost ?? 0);
            $stepsSheet->setCellValue('G' . $row, $step->quantity ?? 1);
            $stepsSheet->setCellValue('H' . $row, $step->cost_reference_id ?? '');
            $stepsSheet->setCellValue('I' . $row, $step->costReference->service_code ?? '');
            $row++;
        }
        
        foreach (range('A', 'I') as $col) {
            $stepsSheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'pathway_template_' . str_replace([' ', '/'], '_', $pathway->name) . '_' . date('Ymd') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'pathway_template_');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
    
    /**
     * Import pathway from template file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'name' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
        ]);
        
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            return redirect()->back()->with('error', 'Excel import belum tersedia. Mohon instal paket phpoffice/phpspreadsheet.');
        }
        
        $hospitalId = hospital('id');
        $file = $request->file('file');
        $path = $file->getRealPath();
        
        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($path);
            
            // Read pathway header
            $headerSheet = $spreadsheet->getSheetByName('Pathway Header');
            if (!$headerSheet) {
                $headerSheet = $spreadsheet->getSheet(0);
            }
            
            $headerData = [];
            $highestRow = $headerSheet->getHighestRow();
            for ($row = 2; $row <= $highestRow; $row++) {
                $field = $headerSheet->getCell('A' . $row)->getValue();
                $value = $headerSheet->getCell('B' . $row)->getValue();
                if ($field) {
                    $headerData[$field] = $value;
                }
            }
            
            // Create pathway
            $pathway = new ClinicalPathway();
            $pathway->hospital_id = $hospitalId;
            $pathway->name = $request->input('name');
            $pathway->description = $headerData['description'] ?? '';
            $pathway->diagnosis_code = $headerData['diagnosis_code'] ?? '';
            $pathway->version = $request->input('version') ?? $headerData['version'] ?? '1.0.0';
            $pathway->unit_cost_version = $headerData['unit_cost_version'] ?? null;
            $pathway->status = 'draft';
            $pathway->created_by = Auth::id();
            if (!empty($headerData['effective_date'])) {
                try {
                    $pathway->effective_date = \Carbon\Carbon::parse($headerData['effective_date']);
                } catch (\Exception $e) {
                    // Ignore invalid date
                }
            }
            $pathway->save();
            
            // Read steps
            $stepsSheet = $spreadsheet->getSheetByName('Pathway Steps');
            if (!$stepsSheet) {
                $stepsSheet = $spreadsheet->getSheet(1);
            }
            
            $rows = $stepsSheet->toArray(null, true, true, true);
            if (empty($rows)) {
                throw new \Exception('No steps found in template file.');
            }
            
            $header = array_shift($rows);
            $colMap = [];
            foreach ($header as $letter => $value) {
                $colMap[strtolower(trim((string)$value))] = $letter;
            }
            
            $createdSteps = 0;
            foreach ($rows as $r) {
                if (empty(array_filter($r, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }
                
                $get = function ($key, $default = null) use ($colMap, $r) {
                    if (!isset($colMap[$key])) return $default;
                    $val = $r[$colMap[$key]] ?? $default;
                    return is_string($val) ? trim($val) : $val;
                };
                
                $day = (int) $get('day', 0);
                $category = (string) $get('category', '');
                $activity = (string) $get('activity', '');
                $description = (string) $get('description', '');
                $criteria = (string) $get('criteria', '');
                $standardCost = (float) $get('standard_cost', 0);
                $quantity = (int) $get('quantity', 1);
                $costRefId = $get('cost_reference_id', '');
                
                if (empty($activity) && empty($description)) {
                    continue;
                }
                
                // Try to find cost reference by code if ID not provided
                if (empty($costRefId)) {
                    $costRefCode = $get('cost_reference_code', '');
                    if ($costRefCode) {
                        $costRef = \App\Models\CostReference::where('hospital_id', $hospitalId)
                            ->where('service_code', $costRefCode)
                            ->first();
                        if ($costRef) {
                            $costRefId = $costRef->id;
                        }
                    }
                }
                
                $step = new PathwayStep();
                $step->clinical_pathway_id = $pathway->id;
                $step->day = $day > 0 ? $day : 1;
                $step->category = $category ?: 'General';
                $step->activity = $activity;
                $step->description = $description;
                $step->criteria = $criteria;
                $step->estimated_cost = $standardCost;
                $step->quantity = $quantity > 0 ? $quantity : 1;
                $step->cost_reference_id = $costRefId ?: null;
                $step->save();
                
                $createdSteps++;
            }
            
            DB::commit();
            
            return redirect()->route('pathways.show', $pathway)
                ->with('success', "Pathway berhasil diimport dengan {$createdSteps} langkah.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengimport template: ' . $e->getMessage());
        }
    }
    
    /**
     * Download blank template
     */
    public function downloadTemplate()
    {
        if (!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            return redirect()->back()->with('error', 'Excel export belum tersedia.');
        }
        
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Pathway Header
        $headerSheet = $spreadsheet->getActiveSheet();
        $headerSheet->setTitle('Pathway Header');
        $headerSheet->setCellValue('A1', 'Field');
        $headerSheet->setCellValue('B1', 'Value');
        
        $headerData = [
            ['name', 'Contoh Pathway Name'],
            ['description', 'Deskripsi pathway'],
            ['diagnosis_code', 'A00.0'],
            ['version', '1.0.0'],
            ['unit_cost_version', 'UC-2025-01'],
            ['effective_date', date('Y-m-d')],
        ];
        
        $row = 2;
        foreach ($headerData as $data) {
            $headerSheet->setCellValue('A' . $row, $data[0]);
            $headerSheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }
        
        $headerSheet->getColumnDimension('A')->setWidth(20);
        $headerSheet->getColumnDimension('B')->setWidth(50);
        
        // Sheet 2: Pathway Steps
        $stepsSheet = $spreadsheet->createSheet();
        $stepsSheet->setTitle('Pathway Steps');
        
        $headers = ['day', 'category', 'activity', 'description', 'criteria', 'standard_cost', 'quantity', 'cost_reference_id', 'cost_reference_code'];
        $col = 'A';
        foreach ($headers as $header) {
            $stepsSheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Example rows
        $examples = [
            [1, 'Administrasi', 'Registrasi', 'Registrasi pasien masuk', '', '50000', 1, '', 'REG001'],
            [1, 'Konsultasi', 'Konsultasi Dokter', 'Konsultasi awal dengan dokter', '', '150000', 1, '', 'KON001'],
            [2, 'Pemeriksaan', 'Lab Darah', 'Pemeriksaan darah lengkap', '', '200000', 1, '', 'LAB001'],
        ];
        
        $row = 2;
        foreach ($examples as $example) {
            $col = 'A';
            foreach ($example as $value) {
                $stepsSheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        foreach (range('A', 'I') as $col) {
            $stepsSheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'pathway_template_blank.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'pathway_template_');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}

