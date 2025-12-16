<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\Division;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DivisionController extends Controller
{
    use BlocksObserver;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $isActive = $request->get('is_active');
        $viewMode = $request->get('view_mode', 'tree'); // 'tree', 'flat', or 'diagram'

        if ($viewMode === 'diagram') {
            // Get all divisions for diagram view
            $allDivisions = Division::where('hospital_id', hospital('id'))
                ->with(['parent', 'children'])
                ->get();

            // Apply filters if provided
            if ($search || ($isActive !== null && $isActive !== '')) {
                $filteredIds = collect();
                
                $matchingDivisions = $allDivisions->filter(function($division) use ($search, $isActive) {
                    $matchesSearch = true;
                    $matchesActive = true;
                    
                    if ($search) {
                        $matchesSearch = stripos($division->name, $search) !== false 
                            || stripos($division->code ?? '', $search) !== false;
                    }
                    
                    if ($isActive !== null && $isActive !== '') {
                        $matchesActive = $division->is_active == $isActive;
                    }
                    
                    return $matchesSearch && $matchesActive;
                });
                
                $matchingDivisions->each(function($division) use (&$filteredIds, $allDivisions) {
                    $filteredIds->push($division->id);
                    $current = $division;
                    while ($current->parent_id) {
                        $filteredIds->push($current->parent_id);
                        $current = $allDivisions->firstWhere('id', $current->parent_id);
                        if (!$current) break;
                    }
                });
                
                $allDivisions = $allDivisions->filter(function($division) use ($filteredIds) {
                    return $filteredIds->contains($division->id);
                });
            }

            $rootDivisions = $allDivisions->whereNull('parent_id')->sortBy('name')->values();
            
            return view('divisions.index', compact('rootDivisions', 'allDivisions', 'search', 'isActive', 'viewMode'));
        } elseif ($viewMode === 'tree') {
            // Get all divisions for tree view
            $allDivisions = Division::where('hospital_id', hospital('id'))
                ->with(['parent', 'children'])
                ->get();

            // Apply filters if provided - include parents of matching children
            if ($search || ($isActive !== null && $isActive !== '')) {
                $filteredIds = collect();
                
                // First, find all divisions that match the filter
                $matchingDivisions = $allDivisions->filter(function($division) use ($search, $isActive) {
                    $matchesSearch = true;
                    $matchesActive = true;
                    
                    if ($search) {
                        $matchesSearch = stripos($division->name, $search) !== false 
                            || stripos($division->code ?? '', $search) !== false;
                    }
                    
                    if ($isActive !== null && $isActive !== '') {
                        $matchesActive = $division->is_active == $isActive;
                    }
                    
                    return $matchesSearch && $matchesActive;
                });
                
                // Collect IDs of matching divisions and their parents
                $matchingDivisions->each(function($division) use (&$filteredIds, $allDivisions) {
                    $filteredIds->push($division->id);
                    // Include all ancestors
                    $current = $division;
                    while ($current->parent_id) {
                        $filteredIds->push($current->parent_id);
                        $current = $allDivisions->firstWhere('id', $current->parent_id);
                        if (!$current) break;
                    }
                });
                
                // Filter allDivisions to include only matching divisions and their parents
                $allDivisions = $allDivisions->filter(function($division) use ($filteredIds) {
                    return $filteredIds->contains($division->id);
                });
            }

            // Get root divisions (no parent) and sort
            $rootDivisions = $allDivisions->whereNull('parent_id')->sortBy('name')->values();
            
            return view('divisions.index', compact('rootDivisions', 'allDivisions', 'search', 'isActive', 'viewMode'));
        } else {
            // Flat view with pagination
            $query = Division::where('hospital_id', hospital('id'))->with('parent');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            if ($isActive !== null && $isActive !== '') {
                $query->where('is_active', $isActive);
            }

            $divisions = $query->latest()->paginate(15)->appends($request->query());

            return view('divisions.index', compact('divisions', 'search', 'isActive', 'viewMode'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->blockObserver('membuat');
        $parentDivisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('divisions.create', compact('parentDivisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:divisions,id',
            'is_active' => 'boolean',
        ]);

        // Validate parent belongs to same hospital
        if ($request->filled('parent_id')) {
            $parent = Division::where('id', $request->parent_id)
                ->where('hospital_id', hospital('id'))
                ->first();
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent division tidak valid.'])->withInput();
            }
        }

        Division::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'parent_id' => $request->parent_id ?: null,
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('divisions.index')
            ->with('success', 'Division berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // This method was not part of the instruction, keeping it as is or removing if implied.
        // Based on the instruction, only index, create, store, edit, update, destroy are to be implemented.
        // Since it's not explicitly removed, I'll keep it as is.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        $this->blockObserver('mengubah');

        if ($division->hospital_id !== hospital('id')) {
            abort(404);
        }

        $parentDivisions = Division::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->where('id', '!=', $division->id) // Exclude current division from parent options
            ->orderBy('name')
            ->get();

        return view('divisions.edit', compact('division', 'parentDivisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        $this->blockObserver('mengubah');

        if ($division->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:divisions,id',
            'is_active' => 'boolean',
        ]);

        // Validate parent belongs to same hospital and not the division itself
        if ($request->filled('parent_id')) {
            if ($request->parent_id === $division->id) {
                return back()->withErrors(['parent_id' => 'Division tidak dapat menjadi parent dirinya sendiri.'])->withInput();
            }
            $parent = Division::where('id', $request->parent_id)
                ->where('hospital_id', hospital('id'))
                ->first();
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent division tidak valid.'])->withInput();
            }
        }

        $division->update(array_merge($validated, [
            'parent_id' => $request->parent_id ?: null,
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('divisions.index')
            ->with('success', 'Division berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        $this->blockObserver('menghapus');

        if ($division->hospital_id !== hospital('id')) {
            abort(404);
        }

        // Check usage if necessary (e.g. in Cost Centers)
        // For now, simple delete
        $division->delete();

        return redirect()->route('divisions.index')
            ->with('success', 'Division berhasil dihapus.');
    }

    /**
     * Export divisions to Excel.
     */
    public function export(Request $request)
    {
        $search = $request->get('search');
        $isActive = $request->get('is_active');

        $query = Division::where('hospital_id', hospital('id'))
            ->with('parent')
            ->orderBy('name');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Name', 'Code', 'Parent Name', 'Description', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->name,
                    $item->code ?? '',
                    $item->parent ? $item->parent->name : '',
                    $item->description ?? '',
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'divisions_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Download template for importing divisions.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Name', 'Code', 'Parent Name', 'Description', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Add sample data
        $sheet->setCellValue('A2', 'Direktorat Medis');
        $sheet->setCellValue('B2', 'DIR-MED');
        $sheet->setCellValue('C2', '');
        $sheet->setCellValue('D2', 'Direktorat yang membawahi pelayanan medis');
        $sheet->setCellValue('E2', 'Yes');

        $sheet->setCellValue('A3', 'Instalasi Rawat Jalan');
        $sheet->setCellValue('B3', 'IRJ');
        $sheet->setCellValue('C3', 'Direktorat Medis');
        $sheet->setCellValue('D3', 'Unit pelayanan rawat jalan');
        $sheet->setCellValue('E3', 'Yes');

        // Add notes
        $sheet->setCellValue('G1', 'PETUNJUK:');
        $sheet->setCellValue('G2', '- Name: Nama division (wajib)');
        $sheet->setCellValue('G3', '- Code: Kode division (opsional)');
        $sheet->setCellValue('G4', '- Parent Name: Nama parent division (opsional, harus sudah ada di sistem)');
        $sheet->setCellValue('G5', '- Description: Deskripsi (opsional)');
        $sheet->setCellValue('G6', '- Is Active: Yes/No (default: Yes)');

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'division_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import divisions from Excel.
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

            // First pass: collect all parent names and create/update divisions without parents
            $divisionsByName = [];
            
            foreach ($rows as $index => $row) {
                if (empty($row[0])) {
                    continue; // Skip empty rows
                }

                $rowNumber = $index + 2;

                try {
                    $name = trim($row[0]);
                    $code = !empty($row[1]) ? trim($row[1]) : null;
                    $parentName = !empty($row[2]) ? trim($row[2]) : null;
                    $description = !empty($row[3]) ? trim($row[3]) : null;
                    $isActive = empty($row[4]) || strtolower(trim($row[4])) === 'yes';

                    // Find parent if specified
                    $parentId = null;
                    if ($parentName) {
                        $parent = Division::where('hospital_id', hospital('id'))
                            ->where('name', $parentName)
                            ->first();
                        
                        if (!$parent) {
                            // Check if parent will be created in this import
                            if (!isset($divisionsByName[$parentName])) {
                                throw new \Exception("Parent '{$parentName}' tidak ditemukan");
                            }
                        } else {
                            $parentId = $parent->id;
                        }
                    }

                    // Check if division already exists by name
                    $division = Division::where('hospital_id', hospital('id'))
                        ->where('name', $name)
                        ->first();

                    if ($division) {
                        $division->update([
                            'code' => $code,
                            'parent_id' => $parentId,
                            'description' => $description,
                            'is_active' => $isActive,
                        ]);
                        $divisionsByName[$name] = $division;
                        $updatedCount++;
                    } else {
                        $newDivision = Division::create([
                            'hospital_id' => hospital('id'),
                            'name' => $name,
                            'code' => $code,
                            'parent_id' => $parentId,
                            'description' => $description,
                            'is_active' => $isActive,
                        ]);
                        $divisionsByName[$name] = $newDivision;
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            // Second pass: update parent_id for divisions that reference parents created in this import
            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[2])) {
                    continue;
                }

                $name = trim($row[0]);
                $parentName = trim($row[2]);

                if (isset($divisionsByName[$name]) && isset($divisionsByName[$parentName])) {
                    $division = $divisionsByName[$name];
                    $parent = $divisionsByName[$parentName];
                    
                    if ($division->parent_id !== $parent->id) {
                        $division->update(['parent_id' => $parent->id]);
                    }
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('divisions.index')
                    ->with('warning', "Import selesai dengan catatan. {$successCount} data baru, {$updatedCount} data diupdate. " . count($errors) . " baris gagal: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
            }

            return redirect()->route('divisions.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} data diupdate.");

        } catch (\Exception $e) {
            return redirect()->route('divisions.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
