<?php

namespace App\Http\Controllers;

use App\Models\HouseholdItem;
use App\Models\UnitOfMeasurement;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HouseholdItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HouseholdItem::where('hospital_id', hospital('id'));

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $items = $query->orderBy('name')->paginate(25)->withQueryString();

        return view('household-items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unitsOfMeasurement = UnitOfMeasurement::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('name')
            ->get();

        return view('household-items.create', compact('unitsOfMeasurement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'unit_of_measurement_id' => 'nullable|exists:units_of_measurement,id',
            'unit' => 'required_without:unit_of_measurement_id|nullable|string|max:50',
            'default_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['hospital_id'] = hospital('id');
        $validated['is_active'] = $request->boolean('is_active', true);

        // If UoM selected, get the unit name from it
        if (!empty($validated['unit_of_measurement_id'])) {
            $uom = UnitOfMeasurement::find($validated['unit_of_measurement_id']);
            if ($uom) {
                $validated['unit'] = $uom->name;
            }
        }

        HouseholdItem::create($validated);

        return redirect()->route('household-items.index')
            ->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HouseholdItem $householdItem)
    {
        $this->authorizeHospital($householdItem);
        return view('household-items.show', compact('householdItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HouseholdItem $householdItem)
    {
        $this->authorizeHospital($householdItem);

        $unitsOfMeasurement = UnitOfMeasurement::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('name')
            ->get();

        return view('household-items.edit', compact('householdItem', 'unitsOfMeasurement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HouseholdItem $householdItem)
    {
        $this->authorizeHospital($householdItem);

        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'unit_of_measurement_id' => 'nullable|exists:units_of_measurement,id',
            'unit' => 'required_without:unit_of_measurement_id|nullable|string|max:50',
            'default_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // If UoM selected, get the unit name from it
        if (!empty($validated['unit_of_measurement_id'])) {
            $uom = UnitOfMeasurement::find($validated['unit_of_measurement_id']);
            if ($uom) {
                $validated['unit'] = $uom->name;
            }
        }

        $householdItem->update($validated);

        return redirect()->route('household-items.index')
            ->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HouseholdItem $householdItem)
    {
        $this->authorizeHospital($householdItem);

        // Check if item is being used
        if ($householdItem->householdExpenses()->exists()) {
            return redirect()->route('household-items.index')
                ->with('error', 'Item tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        $householdItem->delete();

        return redirect()->route('household-items.index')
            ->with('success', 'Item berhasil dihapus.');
    }

    /**
     * Bulk delete household items.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:household_items,id',
        ]);

        try {
            // Get items to check if any are being used
            $items = HouseholdItem::where('hospital_id', hospital('id'))
                ->whereIn('id', $request->ids)
                ->get();

            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($items as $item) {
                if ($item->householdExpenses()->exists()) {
                    $skippedCount++;
                } else {
                    $item->delete();
                    $deletedCount++;
                }
            }

            $message = "Berhasil menghapus {$deletedCount} item.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} item tidak dapat dihapus karena masih digunakan.";
            }

            return redirect()->route('household-items.index')
                ->with($skippedCount > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    /**
     * Export items to Excel.
     */
    public function export(Request $request)
    {
        $query = HouseholdItem::where('hospital_id', hospital('id'));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['No', 'Kode', 'Nama Item', 'Satuan', 'Harga Default', 'Status'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0'],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Data rows
        $row = 2;
        foreach ($items as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->code);
            $sheet->setCellValue('C' . $row, $item->name);
            $sheet->setCellValue('D' . $row, $item->unit_display);
            $sheet->setCellValue('E' . $row, $item->default_price);
            $sheet->setCellValue('F' . $row, $item->is_active ? 'Aktif' : 'Tidak Aktif');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'household_items_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Kode', 'Nama Item', 'Satuan', 'Harga Default'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0'],
            ],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['AMB', 'Air Minum Botol', 'Box', 43000],
            ['AMG', 'Air Minum Galon', 'Galon', 18000],
            ['GLT', 'Gas LPG Tabung Besar', 'Tabung', 265000],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_household_items.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import items from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row
            array_shift($rows);

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $code = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $unit = trim($row[2] ?? '');
                $defaultPrice = is_numeric($row[3] ?? '') ? floatval($row[3]) : null;

                if (empty($name) || empty($unit)) {
                    $errors[] = "Baris {$rowNum}: Nama dan Satuan wajib diisi.";
                    continue;
                }

                HouseholdItem::updateOrCreate(
                    [
                        'hospital_id' => hospital('id'),
                        'name' => $name,
                    ],
                    [
                        'code' => $code ?: null,
                        'unit' => $unit,
                        'default_price' => $defaultPrice,
                        'is_active' => true,
                    ]
                );

                $imported++;
            }

            $message = "Berhasil mengimpor {$imported} item.";
            if (!empty($errors)) {
                $message .= ' Beberapa baris memiliki error: ' . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('household-items.index')
                ->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            return redirect()->route('household-items.index')
                ->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Authorize that the item belongs to the current hospital.
     */
    private function authorizeHospital(HouseholdItem $item)
    {
        if ($item->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }
}
