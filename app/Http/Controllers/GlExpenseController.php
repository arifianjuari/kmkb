<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\GlExpense;
use App\Models\CostCenter;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GlExpenseController extends Controller
{
    use BlocksObserver;
    public function index(Request $request)
    {
        $search = $request->get('search');
        $periodMonth = $request->get('period_month');
        $periodYear = $request->get('period_year', date('Y'));
        $costCenterId = $request->get('cost_center_id');
        $expenseCategoryId = $request->get('expense_category_id');
        $sortBy = $request->get('sort_by', 'period');
        $sortDir = $request->get('sort_dir', 'desc');
        
        $query = GlExpense::where('gl_expenses.hospital_id', hospital('id'))
            ->with(['costCenter', 'expenseCategory']);
        
        if ($periodMonth) {
            $query->where('gl_expenses.period_month', $periodMonth);
        }
        
        if ($periodYear) {
            $query->where('gl_expenses.period_year', $periodYear);
        }
        
        if ($costCenterId) {
            $query->where('gl_expenses.cost_center_id', $costCenterId);
        }
        
        if ($expenseCategoryId) {
            $query->where('gl_expenses.expense_category_id', $expenseCategoryId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('costCenter', function($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                })->orWhereHas('expenseCategory', function($sub) use ($search) {
                    $sub->where('account_name', 'LIKE', "%{$search}%")
                        ->orWhere('account_code', 'LIKE', "%{$search}%");
                });
            });
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'cost_center':
                $query->leftJoin('cost_centers', 'gl_expenses.cost_center_id', '=', 'cost_centers.id')
                      ->orderBy('cost_centers.name', $sortDir)
                      ->select('gl_expenses.*');
                break;
            case 'account_code':
                $query->leftJoin('expense_categories', 'gl_expenses.expense_category_id', '=', 'expense_categories.id')
                      ->orderBy('expense_categories.account_code', $sortDir)
                      ->select('gl_expenses.*');
                break;
            case 'amount':
                $query->orderBy('gl_expenses.amount', $sortDir);
                break;
            default:
                // Default sort by period (year desc, month desc)
                $query->orderBy('gl_expenses.period_year', 'desc')->orderBy('gl_expenses.period_month', 'desc');
                break;
        }
        
        $glExpenses = $query->paginate(20)->appends($request->query());
        
        // Calculate total amount per period for the current page items
        $periodTotals = [];
        foreach ($glExpenses as $expense) {
            $periodKey = $expense->period_month . '/' . $expense->period_year;
            if (!isset($periodTotals[$periodKey])) {
                $periodTotals[$periodKey] = 0;
            }
            $periodTotals[$periodKey] += $expense->amount;
        }
        
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('account_name')->get();
        
        return view('gl-expenses.index', compact('glExpenses', 'search', 'periodMonth', 'periodYear', 'costCenterId', 'expenseCategoryId', 'costCenters', 'expenseCategories', 'sortBy', 'sortDir', 'periodTotals'));
    }

    public function create()
    {
        $this->blockObserver('membuat');
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('account_name')->get();
        $fundingSources = $this->getFundingSources();
        
        return view('gl-expenses.create', compact('costCenters', 'expenseCategories', 'fundingSources'));
    }

    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'funding_source' => 'nullable|string|max:255',
        ]);

        // Ensure cost center and expense category belong to same hospital
        $costCenter = CostCenter::where('id', $validated['cost_center_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$costCenter) {
            return back()->withErrors(['cost_center_id' => 'Cost center tidak valid.'])->withInput();
        }

        $expenseCategory = ExpenseCategory::where('id', $validated['expense_category_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$expenseCategory) {
            return back()->withErrors(['expense_category_id' => 'Expense category tidak valid.'])->withInput();
        }

        GlExpense::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
        ]));

        return redirect()->route('gl-expenses.index')
            ->with('success', 'GL expense berhasil dibuat.');
    }

    public function show(GlExpense $glExpense)
    {
        if ($glExpense->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $glExpense->load(['costCenter', 'expenseCategory']);
        
        return view('gl-expenses.show', compact('glExpense'));
    }

    public function edit(GlExpense $glExpense)
    {
        $this->blockObserver('mengubah');
        if ($glExpense->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('account_name')->get();
        $fundingSources = $this->getFundingSources();
        
        return view('gl-expenses.edit', compact('glExpense', 'costCenters', 'expenseCategories', 'fundingSources'));
    }

    public function update(Request $request, GlExpense $glExpense)
    {
        $this->blockObserver('mengubah');
        if ($glExpense->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'funding_source' => 'nullable|string|max:255',
        ]);

        // Ensure cost center and expense category belong to same hospital
        $costCenter = CostCenter::where('id', $validated['cost_center_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$costCenter) {
            return back()->withErrors(['cost_center_id' => 'Cost center tidak valid.'])->withInput();
        }

        $expenseCategory = ExpenseCategory::where('id', $validated['expense_category_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$expenseCategory) {
            return back()->withErrors(['expense_category_id' => 'Expense category tidak valid.'])->withInput();
        }

        $glExpense->update($validated);

        return redirect()->route('gl-expenses.index')
            ->with('success', 'GL expense berhasil diperbarui.');
    }

    public function destroy(GlExpense $glExpense)
    {
        $this->blockObserver('menghapus');
        if ($glExpense->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $glExpense->delete();

        return redirect()->route('gl-expenses.index')
            ->with('success', 'GL expense berhasil dihapus.');
    }

    public function importForm()
    {
        return view('gl-expenses.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            $imported = 0;
            $aggregated = 0;
            $errors = [];
            
            // Step 1: Aggregate rows by cost_center_code + account_code
            $aggregatedData = [];
            
            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows
                
                try {
                    // Expected format: Cost Center Code, Account Code, Amount, Description (optional), Funding Source (optional)
                    $costCenterCode = trim($row[0] ?? '');
                    $accountCode = trim($row[1] ?? '');
                    $amount = floatval($row[2] ?? 0);
                    $description = trim($row[3] ?? '');
                    $fundingSource = trim($row[4] ?? '');
                    
                    if (empty($costCenterCode) || empty($accountCode)) {
                        $errors[] = "Baris " . ($index + 2) . ": Cost Center Code atau Account Code kosong";
                        continue;
                    }
                    
                    if ($amount <= 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Amount harus lebih dari 0";
                        continue;
                    }
                    
                    // Create unique key for aggregation
                    $key = $costCenterCode . '|' . $accountCode;
                    
                    if (!isset($aggregatedData[$key])) {
                        $aggregatedData[$key] = [
                            'cost_center_code' => $costCenterCode,
                            'account_code' => $accountCode,
                            'amount' => 0,
                            'descriptions' => [],
                            'funding_sources' => [],
                            'row_numbers' => [],
                        ];
                    }
                    
                    // Aggregate amount
                    $aggregatedData[$key]['amount'] += $amount;
                    $aggregatedData[$key]['row_numbers'][] = $index + 2;
                    
                    // Collect unique descriptions
                    if (!empty($description) && !in_array($description, $aggregatedData[$key]['descriptions'])) {
                        $aggregatedData[$key]['descriptions'][] = $description;
                    }
                    
                    // Collect unique funding sources
                    if (!empty($fundingSource) && !in_array($fundingSource, $aggregatedData[$key]['funding_sources'])) {
                        $aggregatedData[$key]['funding_sources'][] = $fundingSource;
                    }
                    
                    // Track if this key has multiple rows
                    if (count($aggregatedData[$key]['row_numbers']) > 1) {
                        $aggregated++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            // Step 2: Process aggregated data
            foreach ($aggregatedData as $key => $data) {
                try {
                    $costCenter = CostCenter::where('hospital_id', hospital('id'))
                        ->where('code', $data['cost_center_code'])
                        ->first();
                    
                    if (!$costCenter) {
                        $rowInfo = implode(', ', $data['row_numbers']);
                        $errors[] = "Baris {$rowInfo}: Cost center dengan code '{$data['cost_center_code']}' tidak ditemukan";
                        continue;
                    }
                    
                    $expenseCategory = ExpenseCategory::where('hospital_id', hospital('id'))
                        ->where('account_code', $data['account_code'])
                        ->first();
                    
                    if (!$expenseCategory) {
                        $rowInfo = implode(', ', $data['row_numbers']);
                        $errors[] = "Baris {$rowInfo}: Expense category dengan account code '{$data['account_code']}' tidak ditemukan";
                        continue;
                    }
                    
                    // Combine descriptions and funding sources
                    $combinedDescription = implode('; ', $data['descriptions']);
                    $combinedFundingSource = implode('; ', $data['funding_sources']);
                    
                    // Check if record already exists in database
                    $existing = GlExpense::where('hospital_id', hospital('id'))
                        ->where('period_month', $request->period_month)
                        ->where('period_year', $request->period_year)
                        ->where('cost_center_id', $costCenter->id)
                        ->where('expense_category_id', $expenseCategory->id)
                        ->first();
                    
                    if ($existing) {
                        $existing->update([
                            'amount' => $data['amount'],
                            'description' => $combinedDescription ?: $existing->description,
                            'funding_source' => $combinedFundingSource ?: $existing->funding_source,
                        ]);
                    } else {
                        GlExpense::create([
                            'hospital_id' => hospital('id'),
                            'period_month' => $request->period_month,
                            'period_year' => $request->period_year,
                            'cost_center_id' => $costCenter->id,
                            'expense_category_id' => $expenseCategory->id,
                            'amount' => $data['amount'],
                            'description' => $combinedDescription,
                            'funding_source' => $combinedFundingSource,
                        ]);
                    }
                    
                    $imported++;
                } catch (\Exception $e) {
                    $rowInfo = implode(', ', $data['row_numbers']);
                    $errors[] = "Baris {$rowInfo}: " . $e->getMessage();
                }
            }
            
            $message = "Berhasil mengimpor {$imported} data.";
            if ($aggregated > 0) {
                $message .= " ({$aggregated} baris digabungkan karena kombinasi Cost Center + Account Code yang sama)";
            }
            if (count($errors) > 0) {
                $message .= " Terdapat " . count($errors) . " error: " . implode(', ', array_slice($errors, 0, 5));
            }
            
            return redirect()->route('gl-expenses.index')
                ->with('success', $message)
                ->with('errors', $errors);
                
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error membaca file: ' . $e->getMessage()])->withInput();
        }
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $periodMonth = $request->get('period_month');
        $periodYear = $request->get('period_year', date('Y'));
        $costCenterId = $request->get('cost_center_id');
        $expenseCategoryId = $request->get('expense_category_id');
        
        $query = GlExpense::where('hospital_id', hospital('id'))
            ->with(['costCenter', 'expenseCategory'])
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->orderBy('cost_center_id');
        
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        
        if ($periodYear) {
            $query->where('period_year', $periodYear);
        }
        
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        if ($expenseCategoryId) {
            $query->where('expense_category_id', $expenseCategoryId);
        }
        
        if ($search) {
            $query->whereHas('costCenter', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            })->orWhereHas('expenseCategory', function($q) use ($search) {
                $q->where('account_name', 'LIKE', "%{$search}%")
                  ->orWhere('account_code', 'LIKE', "%{$search}%");
            });
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Period', 'Cost Center Code', 'Cost Center', 'Account Code', 'Expense Category', 'Amount', 'Description', 'Funding Source'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->period_month . '/' . $item->period_year,
                    $item->costCenter ? $item->costCenter->code : '-',
                    $item->costCenter ? $item->costCenter->name : '-',
                    $item->expenseCategory ? $item->expenseCategory->account_code : '-',
                    $item->expenseCategory ? $item->expenseCategory->account_name : '-',
                    (float) $item->amount,
                    $item->description ?? '',
                    $item->funding_source ?? '',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        $sheet->getStyle('F2:F' . max(2, $data->count() + 1))
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'gl_expenses_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Bulk delete GL expenses
     */
    public function bulkDelete(Request $request)
    {
        $this->blockObserver('menghapus');
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:gl_expenses,id',
        ]);

        try {
            $deleted = GlExpense::where('hospital_id', hospital('id'))
                ->whereIn('id', $request->ids)
                ->delete();

            return redirect()->route('gl-expenses.index')
                ->with('success', "Berhasil menghapus {$deleted} GL expense(s).");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus GL expenses: ' . $e->getMessage());
        }
    }

    /**
     * Download import template for GL Expenses
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Template for data entry
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        // Headers matching the import format
        $headers = ['Cost Center Code', 'Account Code', 'Amount', 'Description', 'Funding Source'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Add sample data rows
        $sheet->setCellValue('A2', 'CC001');
        $sheet->setCellValue('B2', '511100');
        $sheet->setCellValue('C2', 1500000);
        $sheet->setCellValue('D2', 'Gaji Pegawai Bulan Januari');
        $sheet->setCellValue('E2', 'APBD');

        $sheet->setCellValue('A3', 'CC001');
        $sheet->setCellValue('B3', '512100');
        $sheet->setCellValue('C3', 750000);
        $sheet->setCellValue('D3', 'Pembelian ATK');
        $sheet->setCellValue('E3', 'BLUD');

        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format amount column as number
        $sheet->getStyle('C2:C100')
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

        // Sheet 2: Cost Centers Reference
        $costCenterSheet = $spreadsheet->createSheet();
        $costCenterSheet->setTitle('Daftar Cost Center');
        
        $costCenterSheet->setCellValue('A1', 'Code');
        $costCenterSheet->setCellValue('B1', 'Name');
        $costCenterSheet->getStyle('A1:B1')->getFont()->setBold(true);
        $costCenterSheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $costCenterSheet->getStyle('A1:B1')->getFont()->getColor()->setARGB('FFFFFFFF');

        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['code', 'name']);
        
        $row = 2;
        foreach ($costCenters as $cc) {
            $costCenterSheet->setCellValue('A' . $row, $cc->code);
            $costCenterSheet->setCellValue('B' . $row, $cc->name);
            $row++;
        }

        $costCenterSheet->getColumnDimension('A')->setAutoSize(true);
        $costCenterSheet->getColumnDimension('B')->setAutoSize(true);

        // Sheet 3: Account Codes Reference
        $accountSheet = $spreadsheet->createSheet();
        $accountSheet->setTitle('Daftar Account Code');
        
        $accountSheet->setCellValue('A1', 'Account Code');
        $accountSheet->setCellValue('B1', 'Account Name');
        $accountSheet->getStyle('A1:B1')->getFont()->setBold(true);
        $accountSheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF70AD47');
        $accountSheet->getStyle('A1:B1')->getFont()->getColor()->setARGB('FFFFFFFF');

        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get(['account_code', 'account_name']);
        
        $row = 2;
        foreach ($expenseCategories as $ec) {
            $accountSheet->setCellValue('A' . $row, $ec->account_code);
            $accountSheet->setCellValue('B' . $row, $ec->account_name);
            $row++;
        }

        $accountSheet->getColumnDimension('A')->setAutoSize(true);
        $accountSheet->getColumnDimension('B')->setAutoSize(true);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'gl_expenses_import_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Get distinct funding sources for autocomplete
     */
    public function getFundingSources()
    {
        return GlExpense::where('hospital_id', hospital('id'))
            ->whereNotNull('funding_source')
            ->where('funding_source', '!=', '')
            ->distinct()
            ->pluck('funding_source')
            ->toArray();
    }
}



