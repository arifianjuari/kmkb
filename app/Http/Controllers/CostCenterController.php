<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\CostCenter;
use App\Models\Division;
use App\Models\TariffClass;
use App\Services\SimrsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CostCenterController extends Controller
{
    use BlocksObserver;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');
        $division = $request->get('division');
        $isActive = $request->get('is_active');
        $viewMode = $request->get('view_mode', 'tree'); // 'tree' or 'flat'

        $baseQuery = CostCenter::where('hospital_id', hospital('id'));

        // Get all divisions for tabs
        $divisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build query for type tab counts (menghormati filter lain, tapi tidak mengunci type)
        $typeCountQuery = clone $baseQuery;
        if ($search) {
            $typeCountQuery->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('building_name', 'LIKE', "%{$search}%");
            });
        }
        if ($division) {
            $typeCountQuery->where('division', $division);
        }
        if ($isActive !== null && $isActive !== '') {
            $typeCountQuery->where('is_active', $isActive);
        }

        $typeCounts = [
            'all' => $typeCountQuery->count(),
            'support' => (clone $typeCountQuery)->where('type', 'support')->count(),
            'revenue' => (clone $typeCountQuery)->where('type', 'revenue')->count(),
        ];

        // Build division counts
        $divisionCounts = ['all' => $typeCountQuery->count()];
        foreach ($divisions as $div) {
            $divisionCountQuery = clone $baseQuery;
            if ($search) {
                $divisionCountQuery->where(function($q) use ($search) {
                    $q->where('code', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%")
                      ->orWhere('building_name', 'LIKE', "%{$search}%");
                });
            }
            if ($type) {
                $divisionCountQuery->where('type', $type);
            }
            if ($isActive !== null && $isActive !== '') {
                $divisionCountQuery->where('is_active', $isActive);
            }
            $divisionCounts[$div->name] = $divisionCountQuery->where('division', $div->name)->count();
        }
        
        if ($viewMode === 'tree') {
            // Get all cost centers for tree view
            $allCostCenters = $baseQuery
                ->with(['parent', 'children', 'tariffClass'])
                ->get();

            // Apply filters if provided
            if ($search || $type || $division || ($isActive !== null && $isActive !== '')) {
                $allCostCenters = $allCostCenters->filter(function($costCenter) use ($search, $type, $division, $isActive) {
                    $matchesSearch = true;
                    $matchesType = true;
                    $matchesDivision = true;
                    $matchesActive = true;
                    
                    if ($search) {
                        $matchesSearch = stripos($costCenter->code, $search) !== false 
                            || stripos($costCenter->name, $search) !== false
                            || stripos($costCenter->building_name ?? '', $search) !== false;
                    }
                    
                    if ($type) {
                        $matchesType = $costCenter->type === $type;
                    }
                    
                    if ($division) {
                        $matchesDivision = $costCenter->division === $division;
                    }
                    
                    if ($isActive !== null && $isActive !== '') {
                        $matchesActive = $costCenter->is_active == $isActive;
                    }
                    
                    return $matchesSearch && $matchesType && $matchesDivision && $matchesActive;
                });
            }

            // Group cost centers by division
            $groupedByDivision = $allCostCenters->groupBy(function($item) {
                return $item->division ?: 'No Division';
            })->sortKeys();
            
            return view('cost-centers.index', compact('groupedByDivision', 'allCostCenters', 'search', 'type', 'division', 'isActive', 'viewMode', 'typeCounts', 'divisions', 'divisionCounts'));
        } else {
            // Flat view with pagination
            $query = clone $baseQuery;
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%")
                      ->orWhere('building_name', 'LIKE', "%{$search}%");
                });
            }
            
            if ($type) {
                $query->where('type', $type);
            }
            
            if ($division) {
                $query->where('division', $division);
            }
            
            if ($isActive !== null && $isActive !== '') {
                $query->where('is_active', $isActive);
            }
            
            $costCenters = $query->with(['parent', 'tariffClass'])->latest()->paginate(15)->appends($request->query());
            
            return view('cost-centers.index', compact('costCenters', 'search', 'type', 'division', 'isActive', 'viewMode', 'typeCounts', 'divisions', 'divisionCounts'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create(SimrsService $simrsService)
    {
        $this->blockObserver('membuat');
        
        $parents = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $divisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $candidates = $simrsService->getCostCenterCandidates();
        
        return view('cost-centers.create', compact('parents', 'tariffClasses', 'candidates', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'division' => 'nullable|string|max:150',
            'building_name' => 'nullable|string|max:150',
            'floor' => 'nullable|integer|min:0|max:255',
            'tariff_class_id' => 'nullable|exists:tariff_classes,id',
            'type' => 'required|in:support,revenue',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'is_active' => 'boolean',
        ]);

        // Ensure parent belongs to same hospital
        if ($validated['parent_id']) {
            $parent = CostCenter::where('id', $validated['parent_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent cost center tidak valid.'])->withInput();
            }
        }

        // Ensure tariff class belongs to same hospital
        if ($validated['tariff_class_id']) {
            $tariffClass = TariffClass::where('id', $validated['tariff_class_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$tariffClass) {
                return back()->withErrors(['tariff_class_id' => 'Tariff class tidak valid.'])->withInput();
            }
        }

        CostCenter::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('cost-centers.index')
            ->with('success', 'Cost center berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CostCenter $costCenter)
    {
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $costCenter->load(['parent', 'children', 'costReferences', 'glExpenses', 'tariffClass']);
        
        return view('cost-centers.show', compact('costCenter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CostCenter $costCenter, SimrsService $simrsService)
    {
        $this->blockObserver('mengubah');
        
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $parents = CostCenter::where('hospital_id', hospital('id'))
            ->where('id', '!=', $costCenter->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $divisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $candidates = $simrsService->getCostCenterCandidates();
        
        return view('cost-centers.edit', compact('costCenter', 'parents', 'tariffClasses', 'divisions', 'candidates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CostCenter $costCenter)
    {
        $this->blockObserver('mengubah');
        
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'division' => 'nullable|string|max:150',
            'building_name' => 'nullable|string|max:150',
            'floor' => 'nullable|integer|min:0|max:255',
            'tariff_class_id' => 'nullable|exists:tariff_classes,id',
            'type' => 'required|in:support,revenue',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $costCenter->id) {
            return back()->withErrors(['parent_id' => 'Cost center tidak dapat menjadi parent dari dirinya sendiri.'])->withInput();
        }

        // Ensure parent belongs to same hospital
        if ($validated['parent_id']) {
            $parent = CostCenter::where('id', $validated['parent_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent cost center tidak valid.'])->withInput();
            }
        }

        // Ensure tariff class belongs to same hospital
        if ($validated['tariff_class_id']) {
            $tariffClass = TariffClass::where('id', $validated['tariff_class_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$tariffClass) {
                return back()->withErrors(['tariff_class_id' => 'Tariff class tidak valid.'])->withInput();
            }
        }

        $costCenter->update(array_merge($validated, [
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('cost-centers.index')
            ->with('success', 'Cost center berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostCenter $costCenter)
    {
        $this->blockObserver('menghapus');
        
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        try {
            // Check if cost center is being used
            if ($costCenter->costReferences()->count() > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan di Cost References.');
            }
            
            if ($costCenter->glExpenses()->count() > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan di GL Expenses.');
            }
            
            if ($costCenter->children()->count() > 0) {
                $count = $costCenter->children()->count();
                $samples = $costCenter->children()->limit(3)->pluck('name')->implode(', ');
                $suffix = $count > 3 ? " dan " . ($count - 3) . " lainnya" : "";
                
                return redirect()->route('cost-centers.index')
                    ->with('error', "Cost center tidak dapat dihapus karena masih memiliki {$count} child cost centers: {$samples}{$suffix}.");
            }
            
            if ($costCenter->driverStatistics()->count() > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan di Driver Statistics.');
            }
            
            if ($costCenter->allocationMapsAsSource()->count() > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan sebagai source di Allocation Maps.');
            }
            
            if ($costCenter->allocationResultsAsSource()->count() > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan sebagai source di Allocation Results.');
            }
            
            if ($costCenter->allocationResultsAsTarget()->count() > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan sebagai target di Allocation Results.');
            }
            
            $costCenter->delete();

            return redirect()->route('cost-centers.index')
                ->with('success', 'Cost center berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting cost center: ' . $e->getMessage(), [
                'cost_center_id' => $costCenter->id,
                'exception' => $e
            ]);
            
            return redirect()->route('cost-centers.index')
                ->with('error', 'Terjadi kesalahan saat menghapus cost center: ' . $e->getMessage());
        }
    }

    /**
     * Export cost centers to Excel.
     */
    public function export(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');
        $isActive = $request->get('is_active');
        
        $query = CostCenter::where('hospital_id', hospital('id'))
            ->with(['parent', 'tariffClass'])
            ->orderBy('code');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('building_name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Code', 'Name', 'Building Name', 'Floor', 'Class', 'Type', 'Parent', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Rows
        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->code,
                    $item->name,
                    $item->building_name ?? '-',
                    $item->floor ?? '-',
                    $item->tariffClass ? $item->tariffClass->name : '-',
                    $item->type == 'support' ? 'Support' : 'Revenue',
                    $item->parent ? $item->parent->name : '-',
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        // Autosize
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'cost_centers_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Download template for importing cost centers.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Code', 'Name', 'Division', 'Building Name', 'Floor', 'Class Code', 'Type', 'Parent Code', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        // Add sample data
        $sheet->setCellValue('A2', 'CC-001');
        $sheet->setCellValue('B2', 'Instalasi Rawat Jalan');
        $sheet->setCellValue('C2', 'Direktorat Medis');
        $sheet->setCellValue('D2', 'Gedung A');
        $sheet->setCellValue('E2', '1');
        $sheet->setCellValue('F2', 'KELAS-1');
        $sheet->setCellValue('G2', 'Revenue');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', 'Yes');

        $sheet->setCellValue('A3', 'CC-002');
        $sheet->setCellValue('B3', 'Poli Umum');
        $sheet->setCellValue('C3', 'Direktorat Medis');
        $sheet->setCellValue('D3', 'Gedung A');
        $sheet->setCellValue('E3', '1');
        $sheet->setCellValue('F3', 'KELAS-1');
        $sheet->setCellValue('G3', 'Revenue');
        $sheet->setCellValue('H3', 'CC-001');
        $sheet->setCellValue('I3', 'Yes');

        // Add notes
        $sheet->setCellValue('K1', 'PETUNJUK:');
        $sheet->setCellValue('K2', '- Code: Kode cost center (wajib, unik)');
        $sheet->setCellValue('K3', '- Name: Nama cost center (wajib)');
        $sheet->setCellValue('K4', '- Division: Nama division (opsional)');
        $sheet->setCellValue('K5', '- Building Name: Nama gedung (opsional)');
        $sheet->setCellValue('K6', '- Floor: Lantai (opsional, angka)');
        $sheet->setCellValue('K7', '- Class Code: Kode tariff class (opsional)');
        $sheet->setCellValue('K8', '- Type: Support atau Revenue (wajib)');
        $sheet->setCellValue('K9', '- Parent Code: Kode parent cost center (opsional)');
        $sheet->setCellValue('K10', '- Is Active: Yes/No (default: Yes)');

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'cost_center_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import cost centers from Excel.
     */
    public function import(Request $request)
    {
        $this->blockObserver('membuat');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header
            array_shift($rows);

            $successCount = 0;
            $updatedCount = 0;
            $errors = [];

            // Get tariff classes for lookup
            $tariffClasses = TariffClass::where('hospital_id', hospital('id'))->get()->keyBy('code');

            // First pass: create/update cost centers
            $costCentersByCode = [];
            
            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) {
                    continue; // Skip empty rows
                }

                $rowNumber = $index + 2;

                try {
                    $code = trim($row[0]);
                    $name = trim($row[1]);
                    $division = !empty($row[2]) ? trim($row[2]) : null;
                    $buildingName = !empty($row[3]) ? trim($row[3]) : null;
                    $floor = !empty($row[4]) ? (int)$row[4] : null;
                    $classCode = !empty($row[5]) ? trim($row[5]) : null;
                    $type = !empty($row[6]) ? strtolower(trim($row[6])) : 'revenue';
                    $parentCode = !empty($row[7]) ? trim($row[7]) : null;
                    $isActive = empty($row[8]) || strtolower(trim($row[8])) === 'yes';

                    // Validate type
                    if (!in_array($type, ['support', 'revenue'])) {
                        throw new \Exception("Type harus 'Support' atau 'Revenue'");
                    }

                    // Find tariff class
                    $tariffClassId = null;
                    if ($classCode && isset($tariffClasses[$classCode])) {
                        $tariffClassId = $tariffClasses[$classCode]->id;
                    }

                    // Check if cost center already exists by code
                    $costCenter = CostCenter::where('hospital_id', hospital('id'))
                        ->where('code', $code)
                        ->first();

                    if ($costCenter) {
                        $costCenter->update([
                            'name' => $name,
                            'division' => $division,
                            'building_name' => $buildingName,
                            'floor' => $floor,
                            'tariff_class_id' => $tariffClassId,
                            'type' => $type,
                            'is_active' => $isActive,
                        ]);
                        $costCentersByCode[$code] = $costCenter;
                        $updatedCount++;
                    } else {
                        $newCostCenter = CostCenter::create([
                            'hospital_id' => hospital('id'),
                            'code' => $code,
                            'name' => $name,
                            'division' => $division,
                            'building_name' => $buildingName,
                            'floor' => $floor,
                            'tariff_class_id' => $tariffClassId,
                            'type' => $type,
                            'is_active' => $isActive,
                        ]);
                        $costCentersByCode[$code] = $newCostCenter;
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            // Second pass: update parent_id
            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[7])) {
                    continue;
                }

                $code = trim($row[0]);
                $parentCode = trim($row[7]);

                if (isset($costCentersByCode[$code])) {
                    // Find parent
                    $parent = CostCenter::where('hospital_id', hospital('id'))
                        ->where('code', $parentCode)
                        ->first();
                    
                    if ($parent) {
                        $costCentersByCode[$code]->update(['parent_id' => $parent->id]);
                    }
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('cost-centers.index')
                    ->with('warning', "Import selesai dengan catatan. {$successCount} data baru, {$updatedCount} data diupdate. " . count($errors) . " baris gagal: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
            }

            return redirect()->route('cost-centers.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} data diupdate.");

        } catch (\Exception $e) {
            return redirect()->route('cost-centers.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
