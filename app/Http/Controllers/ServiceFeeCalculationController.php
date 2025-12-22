<?php

namespace App\Http\Controllers;

use App\Models\ServiceFeeCalculation;
use App\Models\ServiceFeeConfig;
use App\Models\ServiceFeeAssignment;
use App\Models\ServiceFeeIndex;
use App\Models\RevenueRecord;
use App\Models\CostReference;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ServiceFeeCalculationController extends Controller
{
    /**
     * Display calculation results.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');
        $search = $request->get('search');

        $query = ServiceFeeCalculation::where('hospital_id', hospital('id'))
            ->with(['costReference', 'config'])
            ->where('period_year', $year);

        if ($month) {
            $query->where('period_month', $month);
        }

        if ($search) {
            $query->whereHas('costReference', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $calculations = $query->orderBy('calculated_fee', 'desc')
            ->paginate(50);

        // Summary
        $summary = ServiceFeeCalculation::where('hospital_id', hospital('id'))
            ->where('period_year', $year)
            ->when($month, fn($q) => $q->where('period_month', $month))
            ->selectRaw('SUM(total_index_points) as total_points, SUM(calculated_fee) as total_fee, COUNT(*) as count')
            ->first();

        return view('service-fees.calculations.index', compact(
            'calculations', 'summary', 'year', 'month', 'search'
        ));
    }

    /**
     * Show the calculation form.
     */
    public function form()
    {
        $configs = ServiceFeeConfig::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('period_year', 'desc')
            ->get();

        // Check prerequisites
        $hasRevenue = RevenueRecord::where('hospital_id', hospital('id'))->exists();
        $hasIndexes = ServiceFeeIndex::where('hospital_id', hospital('id'))->exists();
        $hasAssignments = ServiceFeeAssignment::where('hospital_id', hospital('id'))->exists();

        return view('service-fees.calculations.form', compact(
            'configs', 'hasRevenue', 'hasIndexes', 'hasAssignments'
        ));
    }

    /**
     * Run the calculation.
     */
    public function run(Request $request)
    {
        $validated = $request->validate([
            'service_fee_config_id' => 'required|exists:service_fee_configs,id',
            'period_year' => 'required|integer|min:2020|max:2099',
            'period_month' => 'required|integer|min:1|max:12',
        ]);

        $configId = $validated['service_fee_config_id'];
        $year = $validated['period_year'];
        $month = $validated['period_month'];
        $hospitalId = hospital('id');

        // Get config
        $config = ServiceFeeConfig::findOrFail($configId);
        if ($config->hospital_id !== $hospitalId) {
            abort(403);
        }

        // Get total revenue for the period
        $totalRevenue = RevenueRecord::where('hospital_id', $hospitalId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->sum('gross_revenue');

        if ($totalRevenue <= 0) {
            return back()->with('error', "Tidak ada data pendapatan untuk periode {$month}/{$year}.");
        }

        // Calculate budget per category
        $budgetJasaPelayanan = $totalRevenue * ($config->jasa_pelayanan_pct / 100);
        $budgetMedis = $budgetJasaPelayanan * ($config->pct_medis / 100);
        $budgetKeperawatan = $budgetJasaPelayanan * ($config->pct_keperawatan / 100);
        $budgetPenunjang = $budgetJasaPelayanan * ($config->pct_penunjang / 100);
        $budgetManajemen = $budgetJasaPelayanan * ($config->pct_manajemen / 100);

        $budgets = [
            'medis' => $budgetMedis,
            'keperawatan' => $budgetKeperawatan,
            'penunjang' => $budgetPenunjang,
            'manajemen' => $budgetManajemen,
        ];

        // Get total index points per category
        $totalPointsPerCategory = [];
        $assignments = ServiceFeeAssignment::where('hospital_id', $hospitalId)
            ->active()
            ->with('serviceFeeIndex')
            ->get();

        foreach ($assignments as $assignment) {
            $index = $assignment->serviceFeeIndex;
            if (!$index || !$index->is_active) continue;
            
            $category = $index->category;
            $points = $assignment->effective_points;
            
            if (!isset($totalPointsPerCategory[$category])) {
                $totalPointsPerCategory[$category] = 0;
            }
            $totalPointsPerCategory[$category] += $points;
        }

        // Calculate point value per category
        $pointValues = [];
        foreach ($budgets as $category => $budget) {
            $totalPoints = $totalPointsPerCategory[$category] ?? 0;
            $pointValues[$category] = $totalPoints > 0 ? $budget / $totalPoints : 0;
        }

        // Calculate fee for each cost reference
        $calculatedCount = 0;
        $costReferenceAssignments = $assignments->groupBy('cost_reference_id');

        foreach ($costReferenceAssignments as $costRefId => $refAssignments) {
            $totalPoints = 0;
            $breakdown = [];
            $feeByCategory = [];

            foreach ($refAssignments as $assignment) {
                $index = $assignment->serviceFeeIndex;
                if (!$index || !$index->is_active) continue;

                $points = $assignment->effective_points;
                $category = $index->category;
                $pointValue = $pointValues[$category] ?? 0;
                $fee = $points * $pointValue;

                $totalPoints += $points;
                
                if (!isset($feeByCategory[$category])) {
                    $feeByCategory[$category] = 0;
                }
                $feeByCategory[$category] += $fee;

                $breakdown[] = [
                    'role' => $index->role,
                    'role_label' => $index->role_label,
                    'category' => $category,
                    'category_label' => $index->category_label,
                    'professional_category' => $index->professional_category,
                    'base_index' => $index->base_index,
                    'final_index' => $index->final_index,
                    'participation_pct' => $assignment->participation_pct,
                    'headcount' => $assignment->headcount,
                    'points' => $points,
                    'point_value' => $pointValue,
                    'fee' => $fee,
                ];
            }

            $totalFee = array_sum($feeByCategory);

            // Calculate weighted point value for display
            $avgPointValue = $totalPoints > 0 ? $totalFee / $totalPoints : 0;

            ServiceFeeCalculation::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'cost_reference_id' => $costRefId,
                    'period_year' => $year,
                    'period_month' => $month,
                ],
                [
                    'service_fee_config_id' => $configId,
                    'total_index_points' => $totalPoints,
                    'point_value' => $avgPointValue,
                    'calculated_fee' => $totalFee,
                    'breakdown' => $breakdown,
                    'calculation_method' => 'index',
                    'calculated_by' => auth()->id(),
                ]
            );

            $calculatedCount++;
        }

        return redirect()->route('service-fees.calculations.index', [
            'year' => $year,
            'month' => $month,
        ])->with('success', "Berhasil menghitung jasa untuk {$calculatedCount} layanan.");
    }

    /**
     * Show calculation detail.
     */
    public function show(ServiceFeeCalculation $calculation)
    {
        if ($calculation->hospital_id !== hospital('id')) {
            abort(403);
        }

        $calculation->load(['costReference', 'config']);

        return view('service-fees.calculations.show', compact('calculation'));
    }

    /**
     * Export calculations to Excel.
     */
    public function export(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');

        $query = ServiceFeeCalculation::where('hospital_id', hospital('id'))
            ->with(['costReference', 'config'])
            ->where('period_year', $year);

        if ($month) {
            $query->where('period_month', $month);
        }

        $calculations = $query->orderBy('calculated_fee', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'Kode Layanan', 'Nama Layanan', 'Kategori',
            'Total Index Points', 'Nilai per Point', 'Jasa Terhitung',
            'Tahun', 'Bulan', 'Konfigurasi'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Data
        $row = 2;
        foreach ($calculations as $calc) {
            $sheet->setCellValue('A' . $row, $calc->costReference->code ?? '');
            $sheet->setCellValue('B' . $row, $calc->costReference->name ?? '');
            $sheet->setCellValue('C' . $row, $calc->costReference->category ?? '');
            $sheet->setCellValue('D' . $row, $calc->total_index_points);
            $sheet->setCellValue('E' . $row, $calc->point_value);
            $sheet->setCellValue('F' . $row, $calc->calculated_fee);
            $sheet->setCellValue('G' . $row, $calc->period_year);
            $sheet->setCellValue('H' . $row, $calc->period_month);
            $sheet->setCellValue('I' . $row, $calc->config->name ?? '');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'jasa_tenaga_kesehatan_' . $year . ($month ? '_' . str_pad($month, 2, '0', STR_PAD_LEFT) : '') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
