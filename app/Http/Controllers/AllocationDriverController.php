<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\AllocationDriver;
use App\Models\UnitOfMeasurement;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AllocationDriverController extends Controller
{
    use BlocksObserver;
    public function index(Request $request)
    {
        $search = $request->get('search');
        $isStatic = $request->get('is_static');
        
        $baseQuery = AllocationDriver::where('hospital_id', hospital('id'));
        
        // Build query for filtering
        $query = clone $baseQuery;
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('unit_measurement', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($isStatic !== null && $isStatic !== '') {
            $query->where('is_static', $isStatic);
        }
        
        $allocationDrivers = $query->latest()->paginate(15)->appends($request->query());
        
        // Calculate counts for static type tabs (considering other filters but not is_static)
        $staticCountQuery = clone $baseQuery;
        if ($search) {
            $staticCountQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('unit_measurement', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        $staticTypeCounts = [
            'all' => $staticCountQuery->count(),
            'static' => (clone $staticCountQuery)->where('is_static', true)->count(),
            'dynamic' => (clone $staticCountQuery)->where('is_static', false)->count(),
        ];
        
        return view('allocation-drivers.index', compact('allocationDrivers', 'search', 'isStatic', 'staticTypeCounts'));
    }

    public function create()
    {
        $this->blockObserver('membuat');
        $uoms = UnitOfMeasurement::where('hospital_id', hospital('id'))
            ->forAllocation()
            ->active()
            ->orderBy('name')
            ->get();
        
        return view('allocation-drivers.create', compact('uoms'));
    }

    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        
        // Check if using new UoM system or legacy
        $hasUoms = UnitOfMeasurement::where('hospital_id', hospital('id'))->exists();
        
        $rules = [
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_static' => 'boolean',
        ];
        
        if ($hasUoms && $request->has('unit_of_measurement_id')) {
            $rules['unit_of_measurement_id'] = 'required|exists:units_of_measurement,id';
        } else {
            $rules['unit_measurement'] = 'required|string|max:50';
        }
        
        $validated = $request->validate($rules);
        
        // If using UoM, also set the legacy field for backward compatibility
        if (isset($validated['unit_of_measurement_id'])) {
            $uom = UnitOfMeasurement::find($validated['unit_of_measurement_id']);
            if ($uom) {
                $validated['unit_measurement'] = $uom->symbol ?? $uom->name;
            }
        }

        AllocationDriver::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'is_static' => $request->boolean('is_static'),
        ]));

        return redirect()->route('allocation-drivers.index')
            ->with('success', 'Allocation driver berhasil dibuat.');
    }

    public function show(AllocationDriver $allocationDriver)
    {
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $allocationDriver->load(['driverStatistics', 'allocationMaps']);
        
        return view('allocation-drivers.show', compact('allocationDriver'));
    }

    public function edit(AllocationDriver $allocationDriver)
    {
        $this->blockObserver('mengubah');
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $uoms = UnitOfMeasurement::where('hospital_id', hospital('id'))
            ->forAllocation()
            ->active()
            ->orderBy('name')
            ->get();
        
        return view('allocation-drivers.edit', compact('allocationDriver', 'uoms'));
    }

    public function update(Request $request, AllocationDriver $allocationDriver)
    {
        $this->blockObserver('mengubah');
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Check if using new UoM system or legacy
        $hasUoms = UnitOfMeasurement::where('hospital_id', hospital('id'))->exists();
        
        $rules = [
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_static' => 'boolean',
        ];
        
        if ($hasUoms && $request->has('unit_of_measurement_id')) {
            $rules['unit_of_measurement_id'] = 'required|exists:units_of_measurement,id';
        } else {
            $rules['unit_measurement'] = 'required|string|max:50';
        }
        
        $validated = $request->validate($rules);
        
        // If using UoM, also set the legacy field for backward compatibility
        if (isset($validated['unit_of_measurement_id'])) {
            $uom = UnitOfMeasurement::find($validated['unit_of_measurement_id']);
            if ($uom) {
                $validated['unit_measurement'] = $uom->symbol ?? $uom->name;
            }
        }

        $validated['is_static'] = $request->boolean('is_static');
        $allocationDriver->update($validated);

        return redirect()->route('allocation-drivers.index')
            ->with('success', 'Allocation driver berhasil diperbarui.');
    }

    public function destroy(AllocationDriver $allocationDriver)
    {
        $this->blockObserver('menghapus');
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if ($allocationDriver->driverStatistics()->count() > 0) {
            return redirect()->route('allocation-drivers.index')
                ->with('error', 'Allocation driver tidak dapat dihapus karena masih digunakan di Driver Statistics.');
        }
        
        if ($allocationDriver->allocationMaps()->count() > 0) {
            return redirect()->route('allocation-drivers.index')
                ->with('error', 'Allocation driver tidak dapat dihapus karena masih digunakan di Allocation Maps.');
        }
        
        $allocationDriver->delete();

        return redirect()->route('allocation-drivers.index')
            ->with('success', 'Allocation driver berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $isStatic = $request->get('is_static');
        
        $query = AllocationDriver::where('hospital_id', hospital('id'))
            ->orderBy('name');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('unit_measurement', 'LIKE', "%{$search}%");
            });
        }
        
        if ($isStatic !== null && $isStatic !== '') {
            $query->where('is_static', $isStatic);
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Name', 'Unit Measurement', 'Static', 'Description'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->name,
                    $item->unit_measurement,
                    $item->is_static ? 'Yes' : 'No',
                    $item->description ?? '-',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'allocation_drivers_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Download template for importing allocation drivers.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Name', 'Unit Measurement', 'Static', 'Description'];
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data
        $sheet->setCellValue('A2', 'Luas Area');
        $sheet->setCellValue('B2', 'm²');
        $sheet->setCellValue('C2', 'Yes');
        $sheet->setCellValue('D2', 'Luas area dalam meter persegi');

        // Add second sample
        $sheet->setCellValue('A3', 'Jumlah Pasien');
        $sheet->setCellValue('B3', 'orang');
        $sheet->setCellValue('C3', 'No');
        $sheet->setCellValue('D3', 'Jumlah pasien per bulan');

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'allocation_driver_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import allocation drivers from Excel.
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

            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) {
                    continue; // Skip empty rows
                }

                $rowNumber = $index + 2; // +2 because header is 1 and index starts at 0

                try {
                    $name = trim($row[0]);
                    $unitMeasurement = trim($row[1]);
                    $isStatic = strtolower(trim($row[2] ?? 'no')) === 'yes';
                    $description = !empty($row[3]) && $row[3] !== '-' ? trim($row[3]) : null;

                    // Check if allocation driver with same name exists
                    $allocationDriver = AllocationDriver::where('hospital_id', hospital('id'))
                        ->where('name', $name)
                        ->first();

                    if ($allocationDriver) {
                        $allocationDriver->update([
                            'unit_measurement' => $unitMeasurement,
                            'is_static' => $isStatic,
                            'description' => $description,
                        ]);
                        $updatedCount++;
                    } else {
                        AllocationDriver::create([
                            'hospital_id' => hospital('id'),
                            'name' => $name,
                            'unit_measurement' => $unitMeasurement,
                            'is_static' => $isStatic,
                            'description' => $description,
                        ]);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('allocation-drivers.index')
                    ->with('warning', "Import selesai dengan catatan. {$successCount} data baru, {$updatedCount} data diupdate. " . count($errors) . " baris gagal: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
            }

            return redirect()->route('allocation-drivers.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} data diupdate.");

        } catch (\Exception $e) {
            return redirect()->route('allocation-drivers.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}



