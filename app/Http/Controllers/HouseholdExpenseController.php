<?php

namespace App\Http\Controllers;

use App\Models\HouseholdExpense;
use App\Models\HouseholdItem;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HouseholdExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periodYear = $request->input('period_year', date('Y'));
        $periodMonth = $request->input('period_month');
        $costCenterId = $request->input('cost_center_id');
        $search = $request->input('search');

        $query = HouseholdExpense::where('hospital_id', hospital('id'))
            ->with(['costCenter', 'householdItem']);

        // Filter by year
        $query->where('period_year', $periodYear);

        // Filter by month
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }

        // Filter by cost center
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }

        // Search by item name
        if ($search) {
            $query->whereHas('householdItem', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderBy('period_month')
            ->orderBy('cost_center_id')
            ->paginate(25)
            ->withQueryString();

        // Get filter options
        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = HouseholdItem::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Calculate totals for current filter
        $totalAmount = (clone $query)->sum('total_amount');

        return view('household-expenses.index', compact(
            'expenses',
            'costCenters',
            'items',
            'periodYear',
            'periodMonth',
            'costCenterId',
            'search',
            'totalAmount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = HouseholdItem::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Pre-fill from query params
        $selectedCostCenter = $request->input('cost_center_id');
        $selectedMonth = $request->input('period_month', date('n'));
        $selectedYear = $request->input('period_year', date('Y'));

        return view('household-expenses.create', compact(
            'costCenters',
            'items',
            'selectedCostCenter',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_center_id' => 'required|exists:cost_centers,id',
            'household_item_id' => 'required|exists:household_items,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2100',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['hospital_id'] = hospital('id');

        // Check for duplicate
        $exists = HouseholdExpense::where('hospital_id', hospital('id'))
            ->where('cost_center_id', $validated['cost_center_id'])
            ->where('household_item_id', $validated['household_item_id'])
            ->where('period_month', $validated['period_month'])
            ->where('period_year', $validated['period_year'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data untuk item, cost center, dan periode yang sama sudah ada.');
        }

        HouseholdExpense::create($validated);

        return redirect()->route('household-expenses.index', [
            'cost_center_id' => $validated['cost_center_id'],
            'period_year' => $validated['period_year'],
        ])->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HouseholdExpense $householdExpense)
    {
        $this->authorizeHospital($householdExpense);
        $householdExpense->load(['costCenter', 'householdItem']);
        return view('household-expenses.show', compact('householdExpense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HouseholdExpense $householdExpense)
    {
        $this->authorizeHospital($householdExpense);

        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = HouseholdItem::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('household-expenses.edit', compact('householdExpense', 'costCenters', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HouseholdExpense $householdExpense)
    {
        $this->authorizeHospital($householdExpense);

        $validated = $request->validate([
            'cost_center_id' => 'required|exists:cost_centers,id',
            'household_item_id' => 'required|exists:household_items,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2100',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Check for duplicate (excluding current)
        $exists = HouseholdExpense::where('hospital_id', hospital('id'))
            ->where('cost_center_id', $validated['cost_center_id'])
            ->where('household_item_id', $validated['household_item_id'])
            ->where('period_month', $validated['period_month'])
            ->where('period_year', $validated['period_year'])
            ->where('id', '!=', $householdExpense->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data untuk item, cost center, dan periode yang sama sudah ada.');
        }

        $householdExpense->update($validated);

        return redirect()->route('household-expenses.index', [
            'cost_center_id' => $validated['cost_center_id'],
            'period_year' => $validated['period_year'],
        ])->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HouseholdExpense $householdExpense)
    {
        $this->authorizeHospital($householdExpense);

        $costCenterId = $householdExpense->cost_center_id;
        $periodYear = $householdExpense->period_year;

        $householdExpense->delete();

        return redirect()->route('household-expenses.index', [
            'cost_center_id' => $costCenterId,
            'period_year' => $periodYear,
        ])->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Bulk delete expenses.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $deleted = HouseholdExpense::where('hospital_id', hospital('id'))
            ->whereIn('id', $ids)
            ->delete();

        return redirect()->back()->with('success', "Berhasil menghapus {$deleted} data.");
    }

    /**
     * Export expenses to Excel.
     */
    public function export(Request $request)
    {
        $periodYear = $request->input('period_year', date('Y'));
        $periodMonth = $request->input('period_month');
        $costCenterId = $request->input('cost_center_id');

        $query = HouseholdExpense::where('hospital_id', hospital('id'))
            ->with(['costCenter', 'householdItem'])
            ->where('period_year', $periodYear);

        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }

        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }

        $expenses = $query->orderBy('cost_center_id')
            ->orderBy('period_month')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['No', 'Cost Center', 'Item', 'Satuan', 'Bulan', 'Tahun', 'Qty', 'Harga Satuan', 'Total'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0'],
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Data rows
        $row = 2;
        $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($expenses as $index => $expense) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $expense->costCenter->name ?? '-');
            $sheet->setCellValue('C' . $row, $expense->householdItem->name ?? '-');
            $sheet->setCellValue('D' . $row, $expense->householdItem->unit ?? '-');
            $sheet->setCellValue('E' . $row, $months[$expense->period_month] ?? $expense->period_month);
            $sheet->setCellValue('F' . $row, $expense->period_year);
            $sheet->setCellValue('G' . $row, $expense->quantity);
            $sheet->setCellValue('H' . $row, $expense->unit_price);
            $sheet->setCellValue('I' . $row, $expense->total_amount);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'household_expenses_' . $periodYear . '_' . date('His') . '.xlsx';

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
        $headers = ['Kode Cost Center', 'Nama Item', 'Bulan (1-12)', 'Tahun', 'Qty', 'Harga Satuan'];
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

        // Instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructionSheet->setCellValue('A1', 'Petunjuk Pengisian:');
        $instructionSheet->setCellValue('A3', '1. Kode Cost Center: Masukkan kode cost center yang sudah ada di sistem.');
        $instructionSheet->setCellValue('A4', '2. Nama Item: Masukkan nama item yang sudah ada di Master Item.');
        $instructionSheet->setCellValue('A5', '3. Bulan: Masukkan angka 1-12 (Januari=1, Desember=12).');
        $instructionSheet->setCellValue('A6', '4. Tahun: Masukkan tahun, contoh: 2024.');
        $instructionSheet->setCellValue('A7', '5. Qty: Jumlah kebutuhan.');
        $instructionSheet->setCellValue('A8', '6. Harga Satuan: Harga per satuan item.');

        // Auto-size columns
        $spreadsheet->setActiveSheetIndex(0);
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_household_expenses.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import expenses from Excel.
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
            $updated = 0;
            $errors = [];

            // Cache cost centers and items
            $costCenters = CostCenter::where('hospital_id', hospital('id'))
                ->pluck('id', 'code')
                ->toArray();

            $items = HouseholdItem::where('hospital_id', hospital('id'))
                ->pluck('id', 'name')
                ->toArray();

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $costCenterCode = trim($row[0] ?? '');
                $itemName = trim($row[1] ?? '');
                $month = intval($row[2] ?? 0);
                $year = intval($row[3] ?? 0);
                $qty = floatval($row[4] ?? 0);
                $unitPrice = floatval($row[5] ?? 0);

                // Validate cost center
                if (!isset($costCenters[$costCenterCode])) {
                    $errors[] = "Baris {$rowNum}: Cost center '{$costCenterCode}' tidak ditemukan.";
                    continue;
                }

                // Validate item
                if (!isset($items[$itemName])) {
                    $errors[] = "Baris {$rowNum}: Item '{$itemName}' tidak ditemukan di Master Item.";
                    continue;
                }

                // Validate period
                if ($month < 1 || $month > 12) {
                    $errors[] = "Baris {$rowNum}: Bulan harus antara 1-12.";
                    continue;
                }

                if ($year < 2020 || $year > 2100) {
                    $errors[] = "Baris {$rowNum}: Tahun tidak valid.";
                    continue;
                }

                $result = HouseholdExpense::updateOrCreate(
                    [
                        'hospital_id' => hospital('id'),
                        'cost_center_id' => $costCenters[$costCenterCode],
                        'household_item_id' => $items[$itemName],
                        'period_month' => $month,
                        'period_year' => $year,
                    ],
                    [
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }
            }

            $message = "Berhasil: {$imported} data baru, {$updated} data diperbarui.";
            if (!empty($errors)) {
                $message .= ' Beberapa baris memiliki error: ' . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= ' dan ' . (count($errors) - 3) . ' error lainnya.';
                }
            }

            return redirect()->route('household-expenses.index')
                ->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            return redirect()->route('household-expenses.index')
                ->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Authorize that the expense belongs to the current hospital.
     */
    private function authorizeHospital(HouseholdExpense $expense)
    {
        if ($expense->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }
}
