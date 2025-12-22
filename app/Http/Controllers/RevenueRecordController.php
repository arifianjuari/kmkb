<?php

namespace App\Http\Controllers;

use App\Models\RevenueRecord;
use App\Models\RevenueSource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RevenueRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month');
        $sourceId = $request->get('source_id');

        $query = RevenueRecord::where('hospital_id', hospital('id'))
            ->with('revenueSource')
            ->where('period_year', $year);

        if ($month) {
            $query->where('period_month', $month);
        }

        if ($sourceId) {
            $query->where('revenue_source_id', $sourceId);
        }

        $records = $query->orderBy('period_month', 'desc')
            ->orderBy('revenue_source_id')
            ->paginate(50);

        $sources = RevenueSource::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('sort_order')
            ->get();

        // Summary by source
        $summaryQuery = RevenueRecord::where('hospital_id', hospital('id'))
            ->where('period_year', $year);

        if ($month) {
            $summaryQuery->where('period_month', $month);
        }

        $summary = $summaryQuery->selectRaw('revenue_source_id, SUM(gross_revenue) as total_gross, SUM(net_revenue) as total_net, SUM(claim_count) as total_claims')
            ->groupBy('revenue_source_id')
            ->with('revenueSource')
            ->get();

        $totalRevenue = $summary->sum('total_gross');

        return view('service-fees.revenue-records.index', compact(
            'records', 'sources', 'summary', 'totalRevenue', 'year', 'month', 'sourceId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sources = RevenueSource::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('sort_order')
            ->get();

        $categories = RevenueRecord::getCategories();

        return view('service-fees.revenue-records.create', compact('sources', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'revenue_source_id' => 'required|exists:revenue_sources,id',
            'period_year' => 'required|integer|min:2020|max:2099',
            'period_month' => 'required|integer|min:1|max:12',
            'category' => 'nullable|string',
            'gross_revenue' => 'required|numeric|min:0',
            'net_revenue' => 'nullable|numeric|min:0',
            'claim_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['hospital_id'] = hospital('id');
        $validated['created_by'] = auth()->id();

        RevenueRecord::updateOrCreate(
            [
                'hospital_id' => $validated['hospital_id'],
                'revenue_source_id' => $validated['revenue_source_id'],
                'period_year' => $validated['period_year'],
                'period_month' => $validated['period_month'],
                'category' => $validated['category'],
            ],
            $validated
        );

        return redirect()->route('service-fees.revenue-records.index', [
            'year' => $validated['period_year'],
            'month' => $validated['period_month'],
        ])->with('success', 'Data pendapatan berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RevenueRecord $revenueRecord)
    {
        $this->authorizeHospital($revenueRecord);

        $sources = RevenueSource::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('sort_order')
            ->get();

        $categories = RevenueRecord::getCategories();

        return view('service-fees.revenue-records.edit', compact('revenueRecord', 'sources', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RevenueRecord $revenueRecord)
    {
        $this->authorizeHospital($revenueRecord);

        $validated = $request->validate([
            'revenue_source_id' => 'required|exists:revenue_sources,id',
            'period_year' => 'required|integer|min:2020|max:2099',
            'period_month' => 'required|integer|min:1|max:12',
            'category' => 'nullable|string',
            'gross_revenue' => 'required|numeric|min:0',
            'net_revenue' => 'nullable|numeric|min:0',
            'claim_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $revenueRecord->update($validated);

        return redirect()->route('service-fees.revenue-records.index', [
            'year' => $validated['period_year'],
            'month' => $validated['period_month'],
        ])->with('success', 'Data pendapatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RevenueRecord $revenueRecord)
    {
        $this->authorizeHospital($revenueRecord);

        $year = $revenueRecord->period_year;
        $month = $revenueRecord->period_month;

        $revenueRecord->delete();

        return redirect()->route('service-fees.revenue-records.index', [
            'year' => $year,
            'month' => $month,
        ])->with('success', 'Data pendapatan berhasil dihapus.');
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        $sources = RevenueSource::where('hospital_id', hospital('id'))
            ->active()
            ->orderBy('sort_order')
            ->get();

        return view('service-fees.revenue-records.import', compact('sources'));
    }

    /**
     * Process import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header
        array_shift($rows);

        $imported = 0;
        $errors = [];
        $hospitalId = hospital('id');

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Account for header and 0-index

            if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[4])) {
                continue;
            }

            try {
                $sourceCode = trim($row[0]);
                $source = RevenueSource::where('hospital_id', $hospitalId)
                    ->where('code', $sourceCode)
                    ->first();

                if (!$source) {
                    $errors[] = "Baris {$rowNum}: Kode sumber '{$sourceCode}' tidak ditemukan.";
                    continue;
                }

                RevenueRecord::updateOrCreate(
                    [
                        'hospital_id' => $hospitalId,
                        'revenue_source_id' => $source->id,
                        'period_year' => (int) $row[1],
                        'period_month' => (int) $row[2],
                        'category' => $row[3] ?? null,
                    ],
                    [
                        'gross_revenue' => (float) str_replace([',', '.'], ['', '.'], $row[4]),
                        'net_revenue' => isset($row[5]) ? (float) str_replace([',', '.'], ['', '.'], $row[5]) : null,
                        'claim_count' => isset($row[6]) ? (int) $row[6] : null,
                        'notes' => $row[7] ?? null,
                        'created_by' => auth()->id(),
                    ]
                );

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }

        $message = "Berhasil mengimport {$imported} data pendapatan.";
        if (count($errors) > 0) {
            $message .= ' Terdapat ' . count($errors) . ' error.';
            return back()->with('warning', $message)->with('import_errors', $errors);
        }

        return redirect()->route('service-fees.revenue-records.index')
            ->with('success', $message);
    }

    /**
     * Download import template.
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['Kode Sumber', 'Tahun', 'Bulan', 'Kategori', 'Pendapatan Kotor', 'Pendapatan Bersih', 'Jumlah Klaim', 'Catatan'];
        $sheet->fromArray($headers, null, 'A1');

        // Example data
        $examples = [
            ['bpjs', 2025, 1, 'rawat_jalan', 1000000000, 950000000, 500, 'Data Januari'],
            ['bpjs', 2025, 1, 'rawat_inap', 2000000000, 1900000000, 300, ''],
            ['umum', 2025, 1, '', 500000000, 500000000, 200, ''],
        ];
        $sheet->fromArray($examples, null, 'A2');

        // Instructions sheet
        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle('Petunjuk');
        $instructions->setCellValue('A1', 'Petunjuk Pengisian Template Import Pendapatan');
        $instructions->setCellValue('A3', 'Kolom:');
        $instructions->setCellValue('A4', '1. Kode Sumber: bpjs, umum, asuransi, jamkesda, corporate');
        $instructions->setCellValue('A5', '2. Tahun: Format 4 digit (misal: 2025)');
        $instructions->setCellValue('A6', '3. Bulan: 1-12');
        $instructions->setCellValue('A7', '4. Kategori: rawat_jalan, rawat_inap, igd, penunjang (opsional)');
        $instructions->setCellValue('A8', '5. Pendapatan Kotor: Nominal (wajib)');
        $instructions->setCellValue('A9', '6. Pendapatan Bersih: Nominal (opsional)');
        $instructions->setCellValue('A10', '7. Jumlah Klaim: Angka (opsional, untuk BPJS)');
        $instructions->setCellValue('A11', '8. Catatan: Teks bebas (opsional)');

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_pendapatan.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Authorize that the model belongs to current hospital.
     */
    private function authorizeHospital($model)
    {
        if ($model->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }
}
