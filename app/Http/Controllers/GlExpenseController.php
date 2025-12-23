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
        
        // Get unique periods from current page to calculate totals
        $periodsOnPage = $glExpenses->unique(function ($item) {
            return $item->period_month . '-' . $item->period_year;
        })->map(function ($item) {
            return ['month' => $item->period_month, 'year' => $item->period_year];
        })->values()->toArray();
        
        // Calculate total amount per period from ALL data (not just current page)
        $periodTotals = [];
        foreach ($periodsOnPage as $period) {
            $periodKey = $period['month'] . '/' . $period['year'];
            
            // Build query with same filters but for specific period
            $totalQuery = GlExpense::where('hospital_id', hospital('id'))
                ->where('period_month', $period['month'])
                ->where('period_year', $period['year']);
            
            // Apply same filters as main query (except period filters since we're targeting specific period)
            if ($costCenterId) {
                $totalQuery->where('cost_center_id', $costCenterId);
            }
            if ($expenseCategoryId) {
                $totalQuery->where('expense_category_id', $expenseCategoryId);
            }
            if ($search) {
                $totalQuery->where(function($q) use ($search) {
                    $q->whereHas('costCenter', function($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('code', 'LIKE', "%{$search}%");
                    })->orWhereHas('expenseCategory', function($sub) use ($search) {
                        $sub->where('account_name', 'LIKE', "%{$search}%")
                            ->orWhere('account_code', 'LIKE', "%{$search}%");
                    });
                });
            }
            
            $periodTotals[$periodKey] = $totalQuery->sum('amount');
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
            'transaction_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:50',
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

        // Auto-generate line_number
        $maxLineNumber = GlExpense::where('hospital_id', hospital('id'))
            ->where('period_month', $validated['period_month'])
            ->where('period_year', $validated['period_year'])
            ->where('cost_center_id', $validated['cost_center_id'])
            ->where('expense_category_id', $validated['expense_category_id'])
            ->max('line_number') ?? 0;

        GlExpense::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'line_number' => $maxLineNumber + 1,
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
            'transaction_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:50',
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
            $errors = [];
            
            // Cache for line numbers per cost_center + account_code combination
            $lineNumberCache = [];
            
            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows
                
                try {
                    // Expected format: Cost Center Code, Account Code, Amount, Description, Funding Source, Transaction Date (optional), Reference Number (optional)
                    $costCenterCode = trim($row[0] ?? '');
                    $accountCode = trim($row[1] ?? '');
                    $amount = floatval($row[2] ?? 0);
                    $description = trim($row[3] ?? '');
                    $fundingSource = trim($row[4] ?? '');
                    $transactionDate = !empty($row[5]) ? $this->parseDate($row[5]) : null;
                    $referenceNumber = trim($row[6] ?? '');
                    
                    if (empty($costCenterCode) || empty($accountCode)) {
                        $errors[] = "Baris " . ($index + 2) . ": Cost Center Code atau Account Code kosong";
                        continue;
                    }
                    
                    if ($amount <= 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Amount harus lebih dari 0";
                        continue;
                    }
                    
                    // Find cost center
                    $costCenter = CostCenter::where('hospital_id', hospital('id'))
                        ->where('code', $costCenterCode)
                        ->first();
                    
                    if (!$costCenter) {
                        $errors[] = "Baris " . ($index + 2) . ": Cost center dengan code '{$costCenterCode}' tidak ditemukan";
                        continue;
                    }
                    
                    // Find expense category
                    $expenseCategory = ExpenseCategory::where('hospital_id', hospital('id'))
                        ->where('account_code', $accountCode)
                        ->first();
                    
                    if (!$expenseCategory) {
                        $errors[] = "Baris " . ($index + 2) . ": Expense category dengan account code '{$accountCode}' tidak ditemukan";
                        continue;
                    }
                    
                    // Generate line_number
                    $cacheKey = $costCenter->id . '|' . $expenseCategory->id;
                    
                    if (!isset($lineNumberCache[$cacheKey])) {
                        // Get max line_number from database for this combination
                        $maxLineNumber = GlExpense::where('hospital_id', hospital('id'))
                            ->where('period_month', $request->period_month)
                            ->where('period_year', $request->period_year)
                            ->where('cost_center_id', $costCenter->id)
                            ->where('expense_category_id', $expenseCategory->id)
                            ->max('line_number') ?? 0;
                        
                        $lineNumberCache[$cacheKey] = $maxLineNumber;
                    }
                    
                    // Increment line number
                    $lineNumberCache[$cacheKey]++;
                    $lineNumber = $lineNumberCache[$cacheKey];
                    
                    // Create new record (no aggregation)
                    GlExpense::create([
                        'hospital_id' => hospital('id'),
                        'period_month' => $request->period_month,
                        'period_year' => $request->period_year,
                        'cost_center_id' => $costCenter->id,
                        'expense_category_id' => $expenseCategory->id,
                        'line_number' => $lineNumber,
                        'amount' => $amount,
                        'transaction_date' => $transactionDate,
                        'reference_number' => $referenceNumber ?: null,
                        'description' => $description,
                        'funding_source' => $fundingSource,
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            $message = "Berhasil mengimpor {$imported} data sebagai record terpisah.";
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
    
    /**
     * Parse date from Excel format
     */
    private function parseDate($value)
    {
        if (empty($value)) return null;
        
        // If numeric (Excel serial date)
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        
        // Try common formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
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

        // Updated headers with new columns
        $headers = ['Period', 'Cost Center Code', 'Cost Center', 'Account Code', 'Expense Category', 'Line #', 'Amount', 'Transaction Date', 'Reference Number', 'Description', 'Funding Source'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style header
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->period_month . '/' . $item->period_year,
                    $item->costCenter ? $item->costCenter->code : '-',
                    $item->costCenter ? $item->costCenter->name : '-',
                    $item->expenseCategory ? $item->expenseCategory->account_code : '-',
                    $item->expenseCategory ? $item->expenseCategory->account_name : '-',
                    $item->line_number ?? 1,
                    (float) $item->amount,
                    $item->transaction_date ? $item->transaction_date->format('Y-m-d') : '',
                    $item->reference_number ?? '',
                    $item->description ?? '',
                    $item->funding_source ?? '',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        // Format amount column (G)
        $sheet->getStyle('G2:G' . max(2, $data->count() + 1))
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        
        // Auto-size all columns
        foreach (range('A', 'K') as $col) {
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

        // Headers matching the import format (with new columns)
        $headers = ['Cost Center Code', 'Account Code', 'Amount', 'Description', 'Funding Source', 'Transaction Date', 'Reference Number'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Add sample data rows
        $sheet->setCellValue('A2', 'CC001');
        $sheet->setCellValue('B2', '511100');
        $sheet->setCellValue('C2', 1500000);
        $sheet->setCellValue('D2', 'Gaji Pegawai Bulan Januari');
        $sheet->setCellValue('E2', 'APBD');
        $sheet->setCellValue('F2', '2025-01-15');
        $sheet->setCellValue('G2', 'INV-001');

        $sheet->setCellValue('A3', 'CC001');
        $sheet->setCellValue('B3', '512100');
        $sheet->setCellValue('C3', 750000);
        $sheet->setCellValue('D3', 'Pembelian ATK Batch 1');
        $sheet->setCellValue('E3', 'BLUD');
        $sheet->setCellValue('F3', '2025-01-10');
        $sheet->setCellValue('G3', 'INV-002');
        
        // Second ATK purchase (same CC + Account - different record)
        $sheet->setCellValue('A4', 'CC001');
        $sheet->setCellValue('B4', '512100');
        $sheet->setCellValue('C4', 500000);
        $sheet->setCellValue('D4', 'Pembelian ATK Batch 2');
        $sheet->setCellValue('E4', 'BLUD');
        $sheet->setCellValue('F4', '2025-01-20');
        $sheet->setCellValue('G4', 'INV-003');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
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



