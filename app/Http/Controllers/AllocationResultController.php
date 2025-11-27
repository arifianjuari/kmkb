<?php

namespace App\Http\Controllers;

use App\Models\AllocationResult;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AllocationResultController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $sourceCostCenterId = $request->get('source_cost_center_id');
        $targetCostCenterId = $request->get('target_cost_center_id');
        $allocationStep = $request->get('allocation_step');
        
        $query = AllocationResult::where('hospital_id', hospital('id'))
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->with(['sourceCostCenter', 'targetCostCenter']);
        
        if ($sourceCostCenterId) {
            $query->where('source_cost_center_id', $sourceCostCenterId);
        }
        
        if ($targetCostCenterId) {
            $query->where('target_cost_center_id', $targetCostCenterId);
        }
        
        if ($allocationStep) {
            $query->where('allocation_step', $allocationStep);
        }
        
        $allocationResults = $query->orderBy('allocation_step')
            ->orderBy('source_cost_center_id')
            ->orderBy('target_cost_center_id')
            ->paginate(50)
            ->appends($request->query());
        
        // Get summary statistics
        $summary = [
            'total_allocated' => AllocationResult::where('hospital_id', hospital('id'))
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->sum('allocated_amount'),
            'total_records' => AllocationResult::where('hospital_id', hospital('id'))
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->count(),
        ];
        
        // Get cost centers for filter dropdowns
        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get unique allocation steps
        $allocationSteps = AllocationResult::where('hospital_id', hospital('id'))
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->distinct()
            ->pluck('allocation_step')
            ->sort()
            ->values();
        
        return view('allocation-results.index', compact(
            'allocationResults',
            'year',
            'month',
            'sourceCostCenterId',
            'targetCostCenterId',
            'allocationStep',
            'summary',
            'costCenters',
            'allocationSteps'
        ));
    }

    public function show(AllocationResult $allocationResult)
    {
        if ($allocationResult->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $allocationResult->load(['sourceCostCenter', 'targetCostCenter']);
        
        return view('allocation-results.show', compact('allocationResult'));
    }

    public function export(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        $sourceCostCenterId = $request->get('source_cost_center_id');
        $targetCostCenterId = $request->get('target_cost_center_id');
        $allocationStep = $request->get('allocation_step');
        
        $query = AllocationResult::where('hospital_id', hospital('id'))
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->with(['sourceCostCenter', 'targetCostCenter']);
        
        if ($sourceCostCenterId) {
            $query->where('source_cost_center_id', $sourceCostCenterId);
        }
        
        if ($targetCostCenterId) {
            $query->where('target_cost_center_id', $targetCostCenterId);
        }
        
        if ($allocationStep) {
            $query->where('allocation_step', $allocationStep);
        }
        
        $data = $query->orderBy('allocation_step')
            ->orderBy('source_cost_center_id')
            ->orderBy('target_cost_center_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Allocation Results');

        // Headers
        $headers = [
            'Period',
            'Allocation Step',
            'Source Cost Center Code',
            'Source Cost Center Name',
            'Target Cost Center Code',
            'Target Cost Center Name',
            'Allocated Amount',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Data
        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->period_month . '/' . $item->period_year,
                    $item->allocation_step,
                    $item->sourceCostCenter->code ?? '-',
                    $item->sourceCostCenter->name ?? '-',
                    $item->targetCostCenter->code ?? '-',
                    $item->targetCostCenter->name ?? '-',
                    $item->allocated_amount,
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format header row
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format amount column
        $sheet->getStyle('G2:G' . ($data->count() + 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $filename = 'allocation_results_' . hospital('id') . '_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

