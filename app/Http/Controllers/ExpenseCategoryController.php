<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExpenseCategoryController extends Controller
{
    use BlocksObserver;
    public function index(Request $request)
    {
        $search = $request->get('search');
        $costType = $request->get('cost_type');
        $allocationCategory = $request->get('allocation_category');
        $isActive = $request->get('is_active');
        
        $baseQuery = ExpenseCategory::where('hospital_id', hospital('id'));
        
        // Build query for filtering
        $query = clone $baseQuery;
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('account_code', 'LIKE', "%{$search}%")
                  ->orWhere('account_name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($costType) {
            $query->where('cost_type', $costType);
        }
        
        if ($allocationCategory) {
            $query->where('allocation_category', $allocationCategory);
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $expenseCategories = $query->orderBy('account_code', 'asc')->paginate(15)->appends($request->query());
        
        // Calculate counts for cost type tabs (considering other filters but not cost_type)
        $costTypeCountQuery = clone $baseQuery;
        if ($search) {
            $costTypeCountQuery->where(function($q) use ($search) {
                $q->where('account_code', 'LIKE', "%{$search}%")
                  ->orWhere('account_name', 'LIKE', "%{$search}%");
            });
        }
        if ($allocationCategory) {
            $costTypeCountQuery->where('allocation_category', $allocationCategory);
        }
        if ($isActive !== null && $isActive !== '') {
            $costTypeCountQuery->where('is_active', $isActive);
        }
        
        $costTypeCounts = [
            'all' => $costTypeCountQuery->count(),
            'fixed' => (clone $costTypeCountQuery)->where('cost_type', 'fixed')->count(),
            'variable' => (clone $costTypeCountQuery)->where('cost_type', 'variable')->count(),
            'semi_variable' => (clone $costTypeCountQuery)->where('cost_type', 'semi_variable')->count(),
        ];
        
        // Calculate counts for allocation category tabs (considering other filters but not allocation_category)
        $allocationCountQuery = clone $baseQuery;
        if ($search) {
            $allocationCountQuery->where(function($q) use ($search) {
                $q->where('account_code', 'LIKE', "%{$search}%")
                  ->orWhere('account_name', 'LIKE', "%{$search}%");
            });
        }
        if ($costType) {
            $allocationCountQuery->where('cost_type', $costType);
        }
        if ($isActive !== null && $isActive !== '') {
            $allocationCountQuery->where('is_active', $isActive);
        }
        
        $allocationCategoryCounts = [
            'all' => $allocationCountQuery->count(),
            'gaji' => (clone $allocationCountQuery)->where('allocation_category', 'gaji')->count(),
            'bhp_medis' => (clone $allocationCountQuery)->where('allocation_category', 'bhp_medis')->count(),
            'bhp_non_medis' => (clone $allocationCountQuery)->where('allocation_category', 'bhp_non_medis')->count(),
            'depresiasi' => (clone $allocationCountQuery)->where('allocation_category', 'depresiasi')->count(),
            'lain_lain' => (clone $allocationCountQuery)->where('allocation_category', 'lain_lain')->count(),
        ];
        
        return view('expense-categories.index', compact('expenseCategories', 'search', 'costType', 'allocationCategory', 'isActive', 'costTypeCounts', 'allocationCategoryCounts'));
    }

    public function create()
    {
        $this->blockObserver('membuat');
        return view('expense-categories.create');
    }

    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        $validated = $request->validate([
            'account_code' => 'required|string|max:50',
            'account_name' => 'required|string|max:150',
            'cost_type' => 'required|in:fixed,variable,semi_variable',
            'allocation_category' => 'required|in:gaji,bhp_medis,bhp_non_medis,depresiasi,lain_lain',
            'is_active' => 'boolean',
        ]);

        ExpenseCategory::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category berhasil dibuat.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $expenseCategory->load(['costReferences', 'glExpenses']);
        
        return view('expense-categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $this->blockObserver('mengubah');
        if ($expenseCategory->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $this->blockObserver('mengubah');
        if ($expenseCategory->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'account_code' => 'required|string|max:50',
            'account_name' => 'required|string|max:150',
            'cost_type' => 'required|in:fixed,variable,semi_variable',
            'allocation_category' => 'required|in:gaji,bhp_medis,bhp_non_medis,depresiasi,lain_lain',
            'is_active' => 'boolean',
        ]);

        $expenseCategory->update(array_merge($validated, [
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $this->blockObserver('menghapus');
        if ($expenseCategory->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if ($expenseCategory->costReferences()->count() > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Expense category tidak dapat dihapus karena masih digunakan di Cost References.');
        }
        
        if ($expenseCategory->glExpenses()->count() > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Expense category tidak dapat dihapus karena masih digunakan di GL Expenses.');
        }
        
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category berhasil dihapus.');
    }

    /**
     * Bulk delete selected expense categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        $this->blockObserver('menghapus');
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = $validated['ids'];

        // Only delete records that belong to the current hospital
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))
            ->whereIn('id', $ids)
            ->get();

        $deleted = 0;
        $failedCostRefs = [];
        $failedGlExpenses = [];
        $failedNames = [];

        foreach ($expenseCategories as $category) {
            // Check if category is used in cost references
            if ($category->costReferences()->count() > 0) {
                $failedCostRefs[] = $category;
                $failedNames[] = $category->account_name;
                continue;
            }
            
            // Check if category is used in GL expenses
            if ($category->glExpenses()->count() > 0) {
                $failedGlExpenses[] = $category;
                $failedNames[] = $category->account_name;
                continue;
            }
            
            $category->delete();
            $deleted++;
        }

        $totalFailed = count($failedCostRefs) + count($failedGlExpenses);
        
        // Build message
        $messages = [];
        
        if ($deleted > 0) {
            $messages[] = "{$deleted} expense categor" . ($deleted > 1 ? 'ies' : 'y') . " berhasil dihapus.";
        }
        
        if ($totalFailed > 0) {
            $failedMsg = "{$totalFailed} expense categor" . ($totalFailed > 1 ? 'ies' : 'y') . " tidak dapat dihapus";
            
            $reasons = [];
            if (count($failedCostRefs) > 0) {
                $reasons[] = count($failedCostRefs) . " kategori masih digunakan di Cost References";
            }
            if (count($failedGlExpenses) > 0) {
                $reasons[] = count($failedGlExpenses) . " kategori masih digunakan di GL Expenses";
            }
            
            if (count($reasons) > 0) {
                $failedMsg .= " karena " . implode(" dan ", $reasons) . ".";
            }
            
            // Show sample names (max 3 untuk tidak terlalu panjang)
            if (count($failedNames) > 0) {
                $sampleNames = array_slice($failedNames, 0, 3);
                $failedMsg .= " Contoh: " . implode(", ", $sampleNames);
                if (count($failedNames) > 3) {
                    $failedMsg .= " dan " . (count($failedNames) - 3) . " lainnya";
                }
                $failedMsg .= ".";
            }
            
            $messages[] = $failedMsg;
        }

        $message = implode(' ', $messages);

        if ($deleted === 0 && $totalFailed > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', $message ?: 'Tidak ada expense category yang dapat dihapus.');
        }

        if ($deleted > 0 && $totalFailed > 0) {
            return redirect()->route('expense-categories.index')
                ->with('warning', $message);
        }

        return redirect()->route('expense-categories.index')
            ->with($deleted > 0 ? 'success' : 'error', $message ?: 'Tidak ada expense category yang dipilih untuk dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $costType = $request->get('cost_type');
        $allocationCategory = $request->get('allocation_category');
        $isActive = $request->get('is_active');
        
        $query = ExpenseCategory::where('hospital_id', hospital('id'))
            ->orderBy('account_code');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('account_code', 'LIKE', "%{$search}%")
                  ->orWhere('account_name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($costType) {
            $query->where('cost_type', $costType);
        }
        
        if ($allocationCategory) {
            $query->where('allocation_category', $allocationCategory);
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Account Code', 'Account Name', 'Cost Type', 'Allocation Category', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->account_code,
                    $item->account_name,
                    ucfirst(str_replace('_', ' ', $item->cost_type)),
                    ucfirst(str_replace('_', ' ', $item->allocation_category)),
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'expense_categories_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    /**
     * Download template for importing expense categories.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Account Code', 'Account Name', 'Cost Type', 'Allocation Category', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data
        $sheet->setCellValue('A2', 'SAMPLE-001');
        $sheet->setCellValue('B2', 'Gaji Pokok Perawat');
        $sheet->setCellValue('C2', 'Fixed');
        $sheet->setCellValue('D2', 'Gaji');
        $sheet->setCellValue('E2', 'Yes');

        // Add validations/comments
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $filename = 'expense_category_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import expense categories from Excel.
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
                    $accountCode = trim($row[0]);
                    $accountName = trim($row[1]);
                    $costType = $this->normalizeCostType($row[2] ?? '');
                    $allocationCategory = $this->normalizeAllocationCategory($row[3] ?? '');
                    $isActive = strtolower(trim($row[4] ?? 'yes')) === 'yes';

                    if (!$costType) {
                        throw new \Exception("Invalid Cost Type: {$row[2]}");
                    }

                    if (!$allocationCategory) {
                        throw new \Exception("Invalid Allocation Category: {$row[3]}");
                    }

                    $expenseCategory = ExpenseCategory::where('hospital_id', hospital('id'))
                        ->where('account_code', $accountCode)
                        ->first();

                    if ($expenseCategory) {
                        $expenseCategory->update([
                            'account_name' => $accountName,
                            'cost_type' => $costType,
                            'allocation_category' => $allocationCategory,
                            'is_active' => $isActive,
                        ]);
                        $updatedCount++;
                    } else {
                        ExpenseCategory::create([
                            'hospital_id' => hospital('id'),
                            'account_code' => $accountCode,
                            'account_name' => $accountName,
                            'cost_type' => $costType,
                            'allocation_category' => $allocationCategory,
                            'is_active' => $isActive,
                        ]);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('expense-categories.index')
                    ->with('warning', "Import selesai dengan catatan. {$successCount} data baru, {$updatedCount} data diupdate. " . count($errors) . " baris gagal: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
            }

            return redirect()->route('expense-categories.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} data diupdate.");

        } catch (\Exception $e) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    private function normalizeCostType($value)
    {
        $value = strtolower(trim($value));
        $value = str_replace(' ', '_', $value);
        
        $validTypes = ['fixed', 'variable', 'semi_variable'];
        return in_array($value, $validTypes) ? $value : null;
    }

    private function normalizeAllocationCategory($value)
    {
        $value = strtolower(trim($value));
        $value = str_replace([' ', '-'], '_', $value);

        $validCategories = ['gaji', 'bhp_medis', 'bhp_non_medis', 'depresiasi', 'lain_lain'];
        
        // Try exact match first
        if (in_array($value, $validCategories)) {
            return $value;
        }
        
        // Map common variations
        $map = [
            'bhp' => 'bhp_medis', // Default to medis if ambiguous
            'non_medis' => 'bhp_non_medis',
            'medis' => 'bhp_medis',
            'lain' => 'lain_lain',
            'lainlain' => 'lain_lain',
        ];

        return $map[$value] ?? null;
    }
}



