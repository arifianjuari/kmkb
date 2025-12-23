<?php

namespace App\Http\Controllers;

use App\Models\GlExpense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExpenseBreakdownController extends Controller
{
    /**
     * Display expense breakdown with split descriptions.
     * This expands GL Expenses that were aggregated during import.
     */
    public function index(Request $request)
    {
        $periodYear = $request->get('period_year', date('Y'));
        $costCenterId = $request->get('cost_center_id');
        
        // Get GL Expenses with their related data
        $query = GlExpense::where('hospital_id', hospital('id'))
            ->where('period_year', $periodYear)
            ->with(['costCenter', 'expenseCategory'])
            ->orderBy('expense_category_id')
            ->orderBy('period_month');
        
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        $glExpenses = $query->get();
        
        // Process and expand the data by splitting descriptions
        $breakdownData = [];
        
        foreach ($glExpenses as $expense) {
            $accountCode = $expense->expenseCategory ? $expense->expenseCategory->account_code : '-';
            $accountName = $expense->expenseCategory ? $expense->expenseCategory->account_name : '-';
            $descriptions = $expense->description ? explode('; ', $expense->description) : [''];
            $descriptionCount = max(count($descriptions), 1);
            
            // Split amount equally among descriptions (if multiple)
            // Note: In real scenario, we don't have individual amounts, so we show total
            foreach ($descriptions as $desc) {
                $key = $accountCode . '|' . trim($desc);
                
                if (!isset($breakdownData[$key])) {
                    $breakdownData[$key] = [
                        'account_code' => $accountCode,
                        'account_name' => $accountName,
                        'description' => trim($desc) ?: $accountName,
                        'cost_center' => $expense->costCenter ? $expense->costCenter->name : '-',
                        'months' => array_fill(1, 12, 0),
                        'total' => 0,
                    ];
                }
                
                // Add amount to the corresponding month
                // If there are multiple descriptions, we can't split the amount accurately
                // So we add the full amount but track it per description-period combination
                $month = $expense->period_month;
                
                // For expenses with multiple descriptions, divide equally for display
                $amountPerDesc = $expense->amount / $descriptionCount;
                $breakdownData[$key]['months'][$month] += $amountPerDesc;
                $breakdownData[$key]['total'] += $amountPerDesc;
            }
        }
        
        // Group by account code for subtotals
        $groupedData = [];
        foreach ($breakdownData as $item) {
            $accountCode = $item['account_code'];
            if (!isset($groupedData[$accountCode])) {
                $groupedData[$accountCode] = [
                    'account_code' => $accountCode,
                    'account_name' => $item['account_name'],
                    'subtotal' => 0,
                    'months_subtotal' => array_fill(1, 12, 0),
                    'items' => [],
                ];
            }
            $groupedData[$accountCode]['items'][] = $item;
            $groupedData[$accountCode]['subtotal'] += $item['total'];
            
            foreach ($item['months'] as $m => $val) {
                $groupedData[$accountCode]['months_subtotal'][$m] += $val;
            }
        }
        
        // Sort by account code
        ksort($groupedData);
        
        // Get cost centers for filter
        $costCenters = \App\Models\CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Calculate grand total
        $grandTotal = array_sum(array_column($groupedData, 'subtotal'));
        $monthsGrandTotal = array_fill(1, 12, 0);
        foreach ($groupedData as $group) {
            foreach ($group['months_subtotal'] as $m => $val) {
                $monthsGrandTotal[$m] += $val;
            }
        }
        
        return view('expense-breakdown.index', compact(
            'groupedData', 
            'periodYear', 
            'costCenterId', 
            'costCenters',
            'grandTotal',
            'monthsGrandTotal'
        ));
    }
    
    /**
     * Export expense breakdown to Excel.
     */
    public function export(Request $request)
    {
        $periodYear = $request->get('period_year', date('Y'));
        $costCenterId = $request->get('cost_center_id');
        
        // Get GL Expenses
        $query = GlExpense::where('hospital_id', hospital('id'))
            ->where('period_year', $periodYear)
            ->with(['costCenter', 'expenseCategory'])
            ->orderBy('expense_category_id')
            ->orderBy('period_month');
        
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        $glExpenses = $query->get();
        
        // Process data same as index
        $breakdownData = [];
        
        foreach ($glExpenses as $expense) {
            $accountCode = $expense->expenseCategory ? $expense->expenseCategory->account_code : '-';
            $accountName = $expense->expenseCategory ? $expense->expenseCategory->account_name : '-';
            $descriptions = $expense->description ? explode('; ', $expense->description) : [''];
            $descriptionCount = max(count($descriptions), 1);
            
            foreach ($descriptions as $desc) {
                $key = $accountCode . '|' . trim($desc);
                
                if (!isset($breakdownData[$key])) {
                    $breakdownData[$key] = [
                        'account_code' => $accountCode,
                        'account_name' => $accountName,
                        'description' => trim($desc) ?: $accountName,
                        'cost_center' => $expense->costCenter ? $expense->costCenter->name : '-',
                        'months' => array_fill(1, 12, 0),
                        'total' => 0,
                    ];
                }
                
                $month = $expense->period_month;
                $amountPerDesc = $expense->amount / $descriptionCount;
                $breakdownData[$key]['months'][$month] += $amountPerDesc;
                $breakdownData[$key]['total'] += $amountPerDesc;
            }
        }
        
        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Expense Breakdown');
        
        // Headers
        $headers = ['Account Code', 'Description', 'Subtotal', 'Total'];
        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOP', 'DES'];
        $headers = array_merge($headers, $months);
        
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
        
        // Data rows
        $row = 2;
        $currentAccountCode = null;
        $subtotalRow = null;
        
        foreach ($breakdownData as $item) {
            if ($currentAccountCode !== $item['account_code']) {
                $currentAccountCode = $item['account_code'];
            }
            
            $rowData = [
                $item['account_code'],
                $item['description'],
                '', // Subtotal placeholder
                $item['total'],
            ];
            
            for ($m = 1; $m <= 12; $m++) {
                $rowData[] = $item['months'][$m];
            }
            
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Number format
        $sheet->getStyle('C2:P' . ($row - 1))
            ->getNumberFormat()->setFormatCode('#,##0');
        
        $filename = 'expense_breakdown_' . $periodYear . '_' . now()->format('Ymd_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
