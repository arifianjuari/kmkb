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
        
        $query = GlExpense::where('hospital_id', hospital('id'))
            ->with(['costCenter', 'expenseCategory']);
        
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
        
        $glExpenses = $query->latest()->paginate(20)->appends($request->query());
        
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('account_name')->get();
        
        return view('gl-expenses.index', compact('glExpenses', 'search', 'periodMonth', 'periodYear', 'costCenterId', 'expenseCategoryId', 'costCenters', 'expenseCategories'));
    }

    public function create()
    {
        $this->blockObserver('membuat');
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('account_name')->get();
        
        return view('gl-expenses.create', compact('costCenters', 'expenseCategories'));
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
        
        return view('gl-expenses.edit', compact('glExpense', 'costCenters', 'expenseCategories'));
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
            
            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows
                
                try {
                    // Expected format: Cost Center Code, Expense Category Code, Amount
                    $costCenterCode = trim($row[0] ?? '');
                    $expenseCategoryCode = trim($row[1] ?? '');
                    $amount = floatval($row[2] ?? 0);
                    
                    if (empty($costCenterCode) || empty($expenseCategoryCode) || $amount <= 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        continue;
                    }
                    
                    $costCenter = CostCenter::where('hospital_id', hospital('id'))
                        ->where('code', $costCenterCode)
                        ->first();
                    
                    if (!$costCenter) {
                        $errors[] = "Baris " . ($index + 2) . ": Cost center dengan code '{$costCenterCode}' tidak ditemukan";
                        continue;
                    }
                    
                    $expenseCategory = ExpenseCategory::where('hospital_id', hospital('id'))
                        ->where('account_code', $expenseCategoryCode)
                        ->first();
                    
                    if (!$expenseCategory) {
                        $errors[] = "Baris " . ($index + 2) . ": Expense category dengan code '{$expenseCategoryCode}' tidak ditemukan";
                        continue;
                    }
                    
                    // Check if record already exists
                    $existing = GlExpense::where('hospital_id', hospital('id'))
                        ->where('period_month', $request->period_month)
                        ->where('period_year', $request->period_year)
                        ->where('cost_center_id', $costCenter->id)
                        ->where('expense_category_id', $expenseCategory->id)
                        ->first();
                    
                    if ($existing) {
                        $existing->update(['amount' => $amount]);
                    } else {
                        GlExpense::create([
                            'hospital_id' => hospital('id'),
                            'period_month' => $request->period_month,
                            'period_year' => $request->period_year,
                            'cost_center_id' => $costCenter->id,
                            'expense_category_id' => $expenseCategory->id,
                            'amount' => $amount,
                        ]);
                    }
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            $message = "Berhasil mengimpor {$imported} data.";
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
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Period', 'Cost Center', 'Expense Category', 'Amount'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->period_month . '/' . $item->period_year,
                    $item->costCenter ? $item->costCenter->name . ' (' . $item->costCenter->code . ')' : '-',
                    $item->expenseCategory ? $item->expenseCategory->account_name . ' (' . $item->expenseCategory->account_code . ')' : '-',
                    (float) $item->amount,
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        $sheet->getStyle('D2:D' . max(2, $data->count() + 1))
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        
        foreach (range('A', 'D') as $col) {
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
}



