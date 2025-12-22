<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\AssetDepreciation;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FixedAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FixedAsset::where('hospital_id', hospital('id'))
            ->with(['assetCategory', 'costCenter']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('name')->paginate(25)->withQueryString();
        $categories = AssetCategory::where('hospital_id', hospital('id'))->active()->orderBy('name')->get();
        $statusOptions = FixedAsset::getStatusOptions();

        return view('fixed-assets.index', compact('assets', 'categories', 'statusOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AssetCategory::where('hospital_id', hospital('id'))->active()->orderBy('name')->get();
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->orderBy('name')->get();
        $statusOptions = FixedAsset::getStatusOptions();
        $conditionOptions = FixedAsset::getConditionOptions();

        return view('fixed-assets.create', compact('categories', 'costCenters', 'statusOptions', 'conditionOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|max:50|unique:fixed_assets,asset_code,NULL,id,hospital_id,' . hospital('id'),
            'name' => 'required|string|max:255',
            'asset_category_id' => 'nullable|exists:asset_categories,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|in:good,fair,poor,damaged',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1|max:100',
            'salvage_value' => 'nullable|numeric|min:0',
            'warranty_end_date' => 'nullable|date',
            'calibration_due_date' => 'nullable|date',
            'status' => 'nullable|in:active,disposed,sold,in_repair',
        ]);

        $validated['hospital_id'] = hospital('id');
        $validated['salvage_value'] = $validated['salvage_value'] ?? 0;
        $validated['condition'] = $validated['condition'] ?? 'good';
        $validated['status'] = $validated['status'] ?? 'active';

        FixedAsset::create($validated);

        return redirect()->route('fixed-assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FixedAsset $fixedAsset)
    {
        $this->authorizeHospital($fixedAsset);
        $fixedAsset->load(['assetCategory', 'costCenter', 'depreciations' => function ($q) {
            $q->orderBy('period_year', 'desc')->orderBy('period_month', 'desc');
        }]);

        return view('fixed-assets.show', compact('fixedAsset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FixedAsset $fixedAsset)
    {
        $this->authorizeHospital($fixedAsset);

        $categories = AssetCategory::where('hospital_id', hospital('id'))->active()->orderBy('name')->get();
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->orderBy('name')->get();
        $statusOptions = FixedAsset::getStatusOptions();
        $conditionOptions = FixedAsset::getConditionOptions();

        return view('fixed-assets.edit', compact('fixedAsset', 'categories', 'costCenters', 'statusOptions', 'conditionOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $this->authorizeHospital($fixedAsset);

        $validated = $request->validate([
            'asset_code' => 'required|string|max:50|unique:fixed_assets,asset_code,' . $fixedAsset->id . ',id,hospital_id,' . hospital('id'),
            'name' => 'required|string|max:255',
            'asset_category_id' => 'nullable|exists:asset_categories,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|in:good,fair,poor,damaged',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1|max:100',
            'salvage_value' => 'nullable|numeric|min:0',
            'warranty_end_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
            'calibration_due_date' => 'nullable|date',
            'status' => 'nullable|in:active,disposed,sold,in_repair',
            'disposal_date' => 'nullable|date',
            'disposal_reason' => 'nullable|string',
            'disposal_value' => 'nullable|numeric|min:0',
        ]);

        $validated['salvage_value'] = $validated['salvage_value'] ?? 0;

        $fixedAsset->update($validated);

        return redirect()->route('fixed-assets.index')
            ->with('success', 'Aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FixedAsset $fixedAsset)
    {
        $this->authorizeHospital($fixedAsset);

        $fixedAsset->depreciations()->delete();
        $fixedAsset->delete();

        return redirect()->route('fixed-assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Calculate depreciation for a specific period.
     */
    public function calculateDepreciation(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:fixed_assets,id',
        ]);

        $query = FixedAsset::where('hospital_id', hospital('id'))
            ->where('status', 'active');

        if (!empty($validated['asset_ids'])) {
            $query->whereIn('id', $validated['asset_ids']);
        }

        $assets = $query->get();
        $calculated = 0;
        $skipped = 0;

        foreach ($assets as $asset) {
            // Check if already fully depreciated
            if ($asset->is_fully_depreciated) {
                $skipped++;
                continue;
            }

            // Check if already calculated for this period
            $existing = AssetDepreciation::where('fixed_asset_id', $asset->id)
                ->where('period_month', $validated['period_month'])
                ->where('period_year', $validated['period_year'])
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Get previous accumulated
            $previousAccumulated = $asset->getAccumulatedDepreciation();
            $monthlyDepreciation = $asset->monthly_depreciation;

            // Don't depreciate beyond salvage value
            $maxDepreciation = $asset->acquisition_cost - $asset->salvage_value - $previousAccumulated;
            $monthlyDepreciation = min($monthlyDepreciation, $maxDepreciation);

            if ($monthlyDepreciation <= 0) {
                $skipped++;
                continue;
            }

            AssetDepreciation::create([
                'fixed_asset_id' => $asset->id,
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'depreciation_amount' => $monthlyDepreciation,
                'accumulated_depreciation' => $previousAccumulated + $monthlyDepreciation,
                'book_value' => $asset->acquisition_cost - $previousAccumulated - $monthlyDepreciation,
            ]);

            $calculated++;
        }

        return redirect()->route('fixed-assets.depreciation')
            ->with('success', "Berhasil menghitung depresiasi untuk {$calculated} aset. {$skipped} aset dilewati.");
    }

    /**
     * Show depreciation calculator page.
     */
    public function depreciation(Request $request)
    {
        $query = FixedAsset::where('hospital_id', hospital('id'))
            ->where('status', 'active')
            ->with(['assetCategory', 'costCenter']);

        $assets = $query->orderBy('name')->get();

        // Get summary
        $summary = [
            'total_assets' => $assets->count(),
            'total_acquisition_cost' => $assets->sum('acquisition_cost'),
            'total_monthly_depreciation' => $assets->sum(fn($a) => $a->monthly_depreciation),
            'total_book_value' => $assets->sum(fn($a) => $a->current_book_value),
        ];

        return view('fixed-assets.depreciation', compact('assets', 'summary'));
    }

    /**
     * Export depreciation report.
     */
    public function exportDepreciation(Request $request)
    {
        $assets = FixedAsset::where('hospital_id', hospital('id'))
            ->where('status', 'active')
            ->with(['assetCategory', 'costCenter'])
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'Kode Aset', 'Nama Aset', 'Kategori', 'Cost Center', 'Harga Perolehan', 'Umur Ekonomis', 'Nilai Sisa', 'Depresiasi/Bulan', 'Akumulasi', 'Nilai Buku'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0'],
            ],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($assets as $index => $asset) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $asset->asset_code);
            $sheet->setCellValue('C' . $row, $asset->name);
            $sheet->setCellValue('D' . $row, $asset->assetCategory?->name ?? '-');
            $sheet->setCellValue('E' . $row, $asset->costCenter?->name ?? '-');
            $sheet->setCellValue('F' . $row, $asset->acquisition_cost);
            $sheet->setCellValue('G' . $row, $asset->useful_life_years . ' tahun');
            $sheet->setCellValue('H' . $row, $asset->salvage_value);
            $sheet->setCellValue('I' . $row, $asset->monthly_depreciation);
            $sheet->setCellValue('J' . $row, $asset->getAccumulatedDepreciation());
            $sheet->setCellValue('K' . $row, $asset->current_book_value);
            $row++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'depreciation_report_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Kode Aset', 'Nama Aset', 'Kategori', 'Cost Center', 'Merk', 'Model', 'No. Seri', 'Lokasi', 'Tgl Perolehan (YYYY-MM-DD)', 'Harga Perolehan', 'Umur Ekonomis (Tahun)', 'Nilai Sisa'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0'],
            ],
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        $sampleData = [
            ['AST-001', 'CT Scan Siemens', '', '', 'Siemens', 'Somatom', 'SN001', 'Radiologi', '2020-01-15', 1500000000, 8, 150000000],
            ['AST-002', 'USG 4D', '', '', 'GE', 'Voluson', 'SN002', 'Poli Kandungan', '2021-06-01', 500000000, 8, 50000000],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_fixed_assets.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import assets from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            array_shift($rows);

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                if (empty(array_filter($row))) {
                    continue;
                }

                $assetCode = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $acquisitionDate = trim($row[8] ?? '');
                $acquisitionCost = is_numeric($row[9] ?? '') ? floatval($row[9]) : null;
                $usefulLife = is_numeric($row[10] ?? '') ? intval($row[10]) : null;

                if (empty($assetCode) || empty($name) || empty($acquisitionDate) || $acquisitionCost === null || $usefulLife === null) {
                    $errors[] = "Baris {$rowNum}: Data wajib tidak lengkap.";
                    continue;
                }

                FixedAsset::updateOrCreate(
                    [
                        'hospital_id' => hospital('id'),
                        'asset_code' => $assetCode,
                    ],
                    [
                        'name' => $name,
                        'brand' => trim($row[4] ?? '') ?: null,
                        'model' => trim($row[5] ?? '') ?: null,
                        'serial_number' => trim($row[6] ?? '') ?: null,
                        'location' => trim($row[7] ?? '') ?: null,
                        'acquisition_date' => $acquisitionDate,
                        'acquisition_cost' => $acquisitionCost,
                        'useful_life_years' => $usefulLife,
                        'salvage_value' => is_numeric($row[11] ?? '') ? floatval($row[11]) : 0,
                        'status' => 'active',
                        'condition' => 'good',
                    ]
                );

                $imported++;
            }

            $message = "Berhasil mengimpor {$imported} aset.";
            if (!empty($errors)) {
                $message .= ' Beberapa baris memiliki error: ' . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('fixed-assets.index')
                ->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            return redirect()->route('fixed-assets.index')
                ->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    private function authorizeHospital(FixedAsset $asset)
    {
        if ($asset->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }
}
