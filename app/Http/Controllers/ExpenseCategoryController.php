<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $costType = $request->get('cost_type');
        $allocationCategory = $request->get('allocation_category');
        $isActive = $request->get('is_active');
        
        $query = ExpenseCategory::where('hospital_id', hospital('id'));
        
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
        
        $expenseCategories = $query->latest()->paginate(15)->appends($request->query());
        
        return view('expense-categories.index', compact('expenseCategories', 'search', 'costType', 'allocationCategory', 'isActive'));
    }

    public function create()
    {
        return view('expense-categories.create');
    }

    public function store(Request $request)
    {
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
        if ($expenseCategory->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
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
}


