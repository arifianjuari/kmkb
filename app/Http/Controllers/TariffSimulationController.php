<?php

namespace App\Http\Controllers;

use App\Services\TariffService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TariffSimulationController extends Controller
{
    protected $tariffService;

    public function __construct(TariffService $tariffService)
    {
        $this->tariffService = $tariffService;
    }

    /**
     * Show the tariff simulation form
     */
    public function index(Request $request)
    {
        $versions = $this->tariffService->getAvailableVersions();
        
        $selectedVersion = $request->get('version', $versions[0] ?? null);
        $globalMargin = $request->get('global_margin', 0.20);
        $jasaSarana = $request->get('jasa_sarana', 0);
        $jasaPelayanan = $request->get('jasa_pelayanan', 0);

        return view('tariff-simulation.index', compact(
            'versions',
            'selectedVersion',
            'globalMargin',
            'jasaSarana',
            'jasaPelayanan'
        ));
    }

    /**
     * Run tariff simulation
     */
    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'version_label' => 'required|string',
            'global_margin' => 'required|numeric|min:0|max:10',
            'jasa_sarana' => 'nullable|numeric|min:0',
            'jasa_pelayanan' => 'nullable|numeric|min:0',
            'service_margins' => 'nullable|array',
            'service_margins.*' => 'numeric|min:0|max:10',
        ]);

        $results = $this->tariffService->simulateTariffs(
            $validated['version_label'],
            $validated['global_margin'],
            $validated['service_margins'] ?? [],
            $validated['jasa_sarana'] ?? 0,
            $validated['jasa_pelayanan'] ?? 0
        );

        return view('tariff-simulation.preview', [
            'results' => $results,
            'versionLabel' => $validated['version_label'],
            'globalMargin' => $validated['global_margin'],
            'jasaSarana' => $validated['jasa_sarana'] ?? 0,
            'jasaPelayanan' => $validated['jasa_pelayanan'] ?? 0,
        ]);
    }

    /**
     * Preview simulation results
     */
    public function preview(Request $request)
    {
        // This can be used for AJAX preview without full page reload
        $validated = $request->validate([
            'version_label' => 'required|string',
            'global_margin' => 'required|numeric|min:0|max:10',
            'jasa_sarana' => 'nullable|numeric|min:0',
            'jasa_pelayanan' => 'nullable|numeric|min:0',
        ]);

        $results = $this->tariffService->simulateTariffs(
            $validated['version_label'],
            $validated['global_margin'],
            [],
            $validated['jasa_sarana'] ?? 0,
            $validated['jasa_pelayanan'] ?? 0
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'summary' => [
                'total_services' => count($results),
                'total_revenue' => array_sum(array_column($results, 'final_tariff_price')),
            ],
        ]);
    }

    /**
     * Export simulation results to Excel
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'version_label' => 'required|string',
            'global_margin' => 'required|numeric|min:0|max:10',
            'jasa_sarana' => 'nullable|numeric|min:0',
            'jasa_pelayanan' => 'nullable|numeric|min:0',
            'service_margins' => 'nullable|array',
            'service_margins.*' => 'numeric|min:0|max:10',
        ]);

        $results = $this->tariffService->simulateTariffs(
            $validated['version_label'],
            $validated['global_margin'],
            $validated['service_margins'] ?? [],
            $validated['jasa_sarana'] ?? 0,
            $validated['jasa_pelayanan'] ?? 0
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tariff Simulation');

        // Headers
        $headers = [
            'Service Code',
            'Service Description',
            'Base Unit Cost',
            'Margin %',
            'Margin Amount',
            'Jasa Sarana',
            'Jasa Pelayanan',
            'Final Tariff Price',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Data
        if (count($results) > 0) {
            $rows = array_map(function ($item) {
                return [
                    $item['service_code'],
                    $item['service_description'],
                    $item['base_unit_cost'],
                    $item['margin_percentage'] * 100, // Convert to percentage
                    $item['margin_amount'],
                    $item['jasa_sarana'],
                    $item['jasa_pelayanan'],
                    $item['final_tariff_price'],
                ];
            }, $results);
            $sheet->fromArray($rows, null, 'A2');
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format header row
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format number columns
        $sheet->getStyle('C2:H' . (count($results) + 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $filename = 'tariff_simulation_' . $validated['version_label'] . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

