<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\UnitOfMeasurement;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnitOfMeasurementController extends Controller
{
    use BlocksObserver;

    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $context = $request->get('context');
        
        $baseQuery = UnitOfMeasurement::where('hospital_id', hospital('id'));
        
        $query = clone $baseQuery;
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('symbol', 'LIKE', "%{$search}%");
            });
        }
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($context) {
            $query->where('context', $context);
        }
        
        $units = $query->orderBy('category')->orderBy('name')->paginate(20)->appends($request->query());
        
        // Calculate counts for context tabs
        $contextCountQuery = clone $baseQuery;
        if ($search) {
            $contextCountQuery->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('symbol', 'LIKE', "%{$search}%");
            });
        }
        if ($category) {
            $contextCountQuery->where('category', $category);
        }
        
        $contextCounts = [
            'all' => (clone $contextCountQuery)->count(),
            'allocation' => (clone $contextCountQuery)->where('context', 'allocation')->count(),
            'service' => (clone $contextCountQuery)->where('context', 'service')->count(),
            'both' => (clone $contextCountQuery)->where('context', 'both')->count(),
        ];
        
        $categories = UnitOfMeasurement::CATEGORIES;
        $contexts = UnitOfMeasurement::CONTEXTS;
        
        return view('units-of-measurement.index', compact(
            'units', 
            'search', 
            'category', 
            'context', 
            'categories', 
            'contexts',
            'contextCounts'
        ));
    }

    public function create()
    {
        $this->blockObserver('membuat');
        $categories = UnitOfMeasurement::CATEGORIES;
        $contexts = UnitOfMeasurement::CONTEXTS;
        
        return view('units-of-measurement.create', compact('categories', 'contexts'));
    }

    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:units_of_measurement,code,NULL,id,hospital_id,' . hospital('id'),
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'category' => 'required|in:area,weight,count,time,volume,service,other',
            'context' => 'required|in:allocation,service,both',
            'is_active' => 'boolean',
        ]);

        UnitOfMeasurement::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('units-of-measurement.index')
            ->with('success', 'Unit of Measurement berhasil dibuat.');
    }

    public function show(UnitOfMeasurement $units_of_measurement)
    {
        $unit = $units_of_measurement;
        
        if ($unit->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $unit->load(['allocationDrivers', 'costReferences']);
        
        return view('units-of-measurement.show', compact('unit'));
    }

    public function edit(UnitOfMeasurement $units_of_measurement)
    {
        $this->blockObserver('mengubah');
        $unit = $units_of_measurement;
        
        if ($unit->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $categories = UnitOfMeasurement::CATEGORIES;
        $contexts = UnitOfMeasurement::CONTEXTS;
        
        return view('units-of-measurement.edit', compact('unit', 'categories', 'contexts'));
    }

    public function update(Request $request, UnitOfMeasurement $units_of_measurement)
    {
        $this->blockObserver('mengubah');
        $unit = $units_of_measurement;
        
        if ($unit->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:units_of_measurement,code,' . $unit->id . ',id,hospital_id,' . hospital('id'),
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'category' => 'required|in:area,weight,count,time,volume,service,other',
            'context' => 'required|in:allocation,service,both',
            'is_active' => 'boolean',
        ]);

        $unit->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('units-of-measurement.index')
            ->with('success', 'Unit of Measurement berhasil diperbarui.');
    }

    public function destroy(UnitOfMeasurement $units_of_measurement)
    {
        $this->blockObserver('menghapus');
        $unit = $units_of_measurement;
        
        if ($unit->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Check if used by allocation drivers
        if ($unit->allocationDrivers()->count() > 0) {
            return redirect()->route('units-of-measurement.index')
                ->with('error', 'Unit of Measurement tidak dapat dihapus karena masih digunakan di Allocation Drivers.');
        }
        
        // Check if used by cost references
        if ($unit->costReferences()->count() > 0) {
            return redirect()->route('units-of-measurement.index')
                ->with('error', 'Unit of Measurement tidak dapat dihapus karena masih digunakan di Cost References.');
        }
        
        $unit->delete();

        return redirect()->route('units-of-measurement.index')
            ->with('success', 'Unit of Measurement berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $context = $request->get('context');
        
        $query = UnitOfMeasurement::where('hospital_id', hospital('id'))
            ->orderBy('category')
            ->orderBy('name');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('symbol', 'LIKE', "%{$search}%");
            });
        }
        
        if ($category) {
            $query->where('category', $category);
        }
        
        if ($context) {
            $query->where('context', $context);
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Code', 'Name', 'Symbol', 'Category', 'Context', 'Active'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->code,
                    $item->name,
                    $item->symbol ?? '',
                    $item->category,
                    $item->context,
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'units_of_measurement_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Code', 'Name', 'Symbol', 'Category', 'Context', 'Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data
        $samples = [
            ['m2', 'Meter Persegi', 'm²', 'area', 'allocation', 'Yes'],
            ['kg', 'Kilogram', 'kg', 'weight', 'both', 'Yes'],
            ['tablet', 'Tablet', 'tab', 'count', 'service', 'Yes'],
        ];
        $sheet->fromArray($samples, null, 'A2');

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instructions');
        $instructionSheet->setCellValue('A1', 'Instructions for importing Units of Measurement');
        $instructionSheet->setCellValue('A3', 'Category Options:');
        $instructionSheet->setCellValue('A4', 'area, weight, count, time, volume, service, other');
        $instructionSheet->setCellValue('A6', 'Context Options:');
        $instructionSheet->setCellValue('A7', 'allocation (for Allocation Drivers), service (for Cost References), both');
        $instructionSheet->setCellValue('A9', 'Active Options:');
        $instructionSheet->setCellValue('A10', 'Yes or No');

        $spreadsheet->setActiveSheetIndex(0);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'units_of_measurement_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

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

            $validCategories = array_keys(UnitOfMeasurement::CATEGORIES);
            $validContexts = array_keys(UnitOfMeasurement::CONTEXTS);

            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) {
                    continue; // Skip empty rows
                }

                $rowNumber = $index + 2;

                try {
                    $code = strtolower(trim($row[0]));
                    $name = trim($row[1]);
                    $symbol = !empty($row[2]) ? trim($row[2]) : null;
                    $category = strtolower(trim($row[3] ?? 'other'));
                    $context = strtolower(trim($row[4] ?? 'both'));
                    $isActive = strtolower(trim($row[5] ?? 'yes')) === 'yes';

                    // Validate category
                    if (!in_array($category, $validCategories)) {
                        $category = 'other';
                    }

                    // Validate context
                    if (!in_array($context, $validContexts)) {
                        $context = 'both';
                    }

                    // Check if unit with same code exists
                    $unit = UnitOfMeasurement::where('hospital_id', hospital('id'))
                        ->where('code', $code)
                        ->first();

                    if ($unit) {
                        $unit->update([
                            'name' => $name,
                            'symbol' => $symbol,
                            'category' => $category,
                            'context' => $context,
                            'is_active' => $isActive,
                        ]);
                        $updatedCount++;
                    } else {
                        UnitOfMeasurement::create([
                            'hospital_id' => hospital('id'),
                            'code' => $code,
                            'name' => $name,
                            'symbol' => $symbol,
                            'category' => $category,
                            'context' => $context,
                            'is_active' => $isActive,
                        ]);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('units-of-measurement.index')
                    ->with('warning', "Import selesai dengan catatan. {$successCount} data baru, {$updatedCount} data diupdate. " . count($errors) . " baris gagal.");
            }

            return redirect()->route('units-of-measurement.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} data diupdate.");

        } catch (\Exception $e) {
            return redirect()->route('units-of-measurement.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
