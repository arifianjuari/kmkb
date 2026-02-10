<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\AssetDepreciation;
use App\Models\CostCenter;
use App\Models\GlExpense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('kode_satker', 'like', "%{$search}%")
                  ->orWhere('lokasi_ruang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_bmn')) {
            $query->where('jenis_bmn', $request->jenis_bmn);
        }

        $assets = $query->orderBy('name')->paginate(25)->withQueryString();
        $categories = AssetCategory::where('hospital_id', hospital('id'))->active()->orderBy('name')->get();
        $statusOptions = FixedAsset::getStatusOptions();
        $jenisBmnOptions = FixedAsset::getJenisBmnOptions();

        return view('fixed-assets.index', compact('assets', 'categories', 'statusOptions', 'jenisBmnOptions'));
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
        $jenisBmnOptions = FixedAsset::getJenisBmnOptions();

        return view('fixed-assets.create', compact('categories', 'costCenters', 'statusOptions', 'conditionOptions', 'jenisBmnOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Core fields
            'asset_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'asset_category_id' => 'nullable|exists:asset_categories,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'description' => 'nullable|string',
            // BMN Identification
            'jenis_bmn' => 'nullable|string|max:50',
            'kode_satker' => 'nullable|string|max:30',
            'nama_satker' => 'nullable|string|max:255',
            'nup' => 'nullable|integer',
            'kode_register' => 'nullable|string|max:50',
            // Inventory fields
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|in:good,fair,poor,damaged',
            // Document fields
            'jenis_dokumen' => 'nullable|string|max:255',
            'no_dokumen' => 'nullable|string|max:255',
            'no_bpkb' => 'nullable|string|max:255',
            'no_polisi' => 'nullable|string|max:20',
            'no_sertifikat' => 'nullable|string|max:255',
            'jenis_sertifikat' => 'nullable|string|max:50',
            'status_sertifikasi' => 'nullable|string|max:50',
            // Address fields
            'alamat_lengkap' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:20',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'kode_kabupaten_kota' => 'nullable|string|max:10',
            'provinsi' => 'nullable|string|max:255',
            'kode_provinsi' => 'nullable|string|max:10',
            'kode_pos' => 'nullable|string|max:10',
            // Land/Building fields
            'luas_tanah_seluruhnya' => 'nullable|numeric|min:0',
            'luas_tanah_bangunan' => 'nullable|numeric|min:0',
            'luas_tanah_sarana' => 'nullable|numeric|min:0',
            'luas_lahan_kosong' => 'nullable|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'luas_tapak_bangunan' => 'nullable|numeric|min:0',
            'luas_pemanfaatan' => 'nullable|numeric|min:0',
            'jumlah_lantai' => 'nullable|integer|min:0',
            // Financial fields
            'acquisition_date' => 'required|date',
            'tanggal_buku_pertama' => 'nullable|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1|max:100',
            'salvage_value' => 'nullable|numeric|min:0',
            'nilai_mutasi' => 'nullable|numeric',
            'nilai_penyusutan' => 'nullable|numeric|min:0',
            'nilai_buku' => 'nullable|numeric|min:0',
            // Maintenance
            'warranty_end_date' => 'nullable|date',
            'calibration_due_date' => 'nullable|date',
            // Status
            'status' => 'nullable|in:active,disposed,sold,in_repair',
            'status_penggunaan' => 'nullable|string|max:100',
            'no_psp' => 'nullable|string|max:255',
            'tanggal_psp' => 'nullable|date',
            'sbsk' => 'nullable|string|max:50',
            'optimalisasi' => 'nullable|string|max:255',
            'penghuni' => 'nullable|string|max:255',
            'pengguna' => 'nullable|string|max:255',
            // Organization
            'kode_kpknl' => 'nullable|string|max:20',
            'uraian_kpknl' => 'nullable|string|max:255',
            'uraian_kanwil_djkn' => 'nullable|string|max:255',
            'nama_kl' => 'nullable|string|max:255',
            'nama_e1' => 'nullable|string|max:255',
            'nama_korwil' => 'nullable|string|max:255',
            'lokasi_ruang' => 'nullable|string|max:255',
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
        $jenisBmnOptions = FixedAsset::getJenisBmnOptions();

        return view('fixed-assets.edit', compact('fixedAsset', 'categories', 'costCenters', 'statusOptions', 'conditionOptions', 'jenisBmnOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $this->authorizeHospital($fixedAsset);

        $validated = $request->validate([
            // Core fields
            'asset_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'asset_category_id' => 'nullable|exists:asset_categories,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'description' => 'nullable|string',
            // BMN Identification
            'jenis_bmn' => 'nullable|string|max:50',
            'kode_satker' => 'nullable|string|max:30',
            'nama_satker' => 'nullable|string|max:255',
            'nup' => 'nullable|integer',
            'kode_register' => 'nullable|string|max:50',
            // Inventory fields
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'condition' => 'nullable|in:good,fair,poor,damaged',
            // Document fields
            'jenis_dokumen' => 'nullable|string|max:255',
            'no_dokumen' => 'nullable|string|max:255',
            'no_bpkb' => 'nullable|string|max:255',
            'no_polisi' => 'nullable|string|max:20',
            'no_sertifikat' => 'nullable|string|max:255',
            'jenis_sertifikat' => 'nullable|string|max:50',
            'status_sertifikasi' => 'nullable|string|max:50',
            // Address fields
            'alamat_lengkap' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:20',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'kode_kabupaten_kota' => 'nullable|string|max:10',
            'provinsi' => 'nullable|string|max:255',
            'kode_provinsi' => 'nullable|string|max:10',
            'kode_pos' => 'nullable|string|max:10',
            // Land/Building fields
            'luas_tanah_seluruhnya' => 'nullable|numeric|min:0',
            'luas_tanah_bangunan' => 'nullable|numeric|min:0',
            'luas_tanah_sarana' => 'nullable|numeric|min:0',
            'luas_lahan_kosong' => 'nullable|numeric|min:0',
            'luas_bangunan' => 'nullable|numeric|min:0',
            'luas_tapak_bangunan' => 'nullable|numeric|min:0',
            'luas_pemanfaatan' => 'nullable|numeric|min:0',
            'jumlah_lantai' => 'nullable|integer|min:0',
            // Financial fields
            'acquisition_date' => 'required|date',
            'tanggal_buku_pertama' => 'nullable|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1|max:100',
            'salvage_value' => 'nullable|numeric|min:0',
            'nilai_mutasi' => 'nullable|numeric',
            'nilai_penyusutan' => 'nullable|numeric|min:0',
            'nilai_buku' => 'nullable|numeric|min:0',
            // Maintenance
            'warranty_end_date' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
            'calibration_due_date' => 'nullable|date',
            // Status
            'status' => 'nullable|in:active,disposed,sold,in_repair',
            'status_penggunaan' => 'nullable|string|max:100',
            'no_psp' => 'nullable|string|max:255',
            'tanggal_psp' => 'nullable|date',
            'sbsk' => 'nullable|string|max:50',
            'optimalisasi' => 'nullable|string|max:255',
            'penghuni' => 'nullable|string|max:255',
            'pengguna' => 'nullable|string|max:255',
            // Disposal
            'disposal_date' => 'nullable|date',
            'tanggal_penghapusan' => 'nullable|date',
            'disposal_reason' => 'nullable|string',
            'disposal_value' => 'nullable|numeric|min:0',
            // Organization
            'kode_kpknl' => 'nullable|string|max:20',
            'uraian_kpknl' => 'nullable|string|max:255',
            'uraian_kanwil_djkn' => 'nullable|string|max:255',
            'nama_kl' => 'nullable|string|max:255',
            'nama_e1' => 'nullable|string|max:255',
            'nama_korwil' => 'nullable|string|max:255',
            'lokasi_ruang' => 'nullable|string|max:255',
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

        // Get expense categories for depreciation COA selection
        $expenseCategories = ExpenseCategory::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        return view('fixed-assets.depreciation', compact('assets', 'summary', 'expenseCategories'));
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
        
        // ===== SHEET 1: Data Template =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Aset');

        // Comprehensive BMN-compatible headers
        $headers = [
            // Identifikasi (1-6)
            'Jenis BMN', 'Kode Satker', 'Nama Satker', 'Kode Barang', 'NUP', 'Nama Barang',
            // Detail Barang (7-9)
            'Merk', 'Tipe', 'Kondisi',
            // Tanggal & Nilai (10-16)
            'Tanggal Perolehan', 'Tanggal Buku Pertama', 'Umur Aset', 'Nilai Perolehan', 'Nilai Penyusutan', 'Nilai Buku', 'Nilai Mutasi',
            // Alamat (17-23)
            'Alamat', 'RT/RW', 'Kelurahan', 'Kecamatan', 'Kab/Kota', 'Provinsi', 'Kode Pos',
            // Tanah/Bangunan (24-27)
            'Luas Tanah (m2)', 'Luas Bangunan (m2)', 'Jumlah Lantai', 'Status Sertifikasi',
            // Dokumen (28-31)
            'No Sertifikat', 'Jenis Sertifikat', 'No Polisi', 'No BPKB',
            // Status & Lokasi (32-35)
            'Status Penggunaan', 'Lokasi Ruang', 'No PSP', 'Kode Register'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4472C4'],
            ],
            'alignment' => ['wrapText' => true],
        ];
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);

        // Sample data row
        $sampleData = [
            'PERALATAN DAN MESIN', '060010500055925000KD', 'RSUD Kota Batu', '3050201001', 1, 'CT Scan 64 Slice',
            'Siemens', 'Somatom Perspective', 'Baik',
            '2020-01-15', '2020-02-01', 8, 1500000000, 150000000, 1350000000, 0,
            'Jl. Raya Oro-oro Ombo', '001/002', 'Temas', 'Batu', 'Kota Batu', 'Jawa Timur', '65315',
            0, 0, 0, 'Belum Bersertifikat',
            '', '', '', '',
            'Digunakan Sendiri', 'Ruang Radiologi', '', ''
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-width columns
        foreach (range(1, count($headers)) as $colIdx) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ===== SHEET 2: Petunjuk =====
        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Petunjuk Pengisian');
        
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT ASET TETAP (BMN)'],
            [''],
            ['KOLOM WAJIB DIISI:'],
            ['Kode Barang', 'Kode 16 digit dari SIMAK BMN (akan menjadi Asset Code)'],
            ['NUP', 'Nomor Urut Pendaftaran, angka unik per kode barang'],
            ['Nama Barang', 'Nama aset/barang'],
            ['Tanggal Perolehan', 'Format: YYYY-MM-DD (contoh: 2020-01-15)'],
            ['Nilai Perolehan', 'Nilai pembelian/perolehan dalam Rupiah (angka saja, tanpa titik/koma)'],
            ['Umur Aset', 'Umur ekonomis dalam tahun (angka)'],
            [''],
            ['PILIHAN ISIAN:'],
            [''],
            ['Jenis BMN:', 'Pilih salah satu:'],
            ['', '- TANAH'],
            ['', '- BANGUNAN'],
            ['', '- PERALATAN DAN MESIN'],
            ['', '- JALAN, IRIGASI DAN JARINGAN'],
            ['', '- ASET TETAP LAINNYA'],
            ['', '- KONSTRUKSI DALAM PENGERJAAN'],
            [''],
            ['Kondisi:', 'Pilih salah satu (akan dikonversi otomatis):'],
            ['', '- Baik / B → good'],
            ['', '- Rusak Ringan / RR → fair'],
            ['', '- Rusak Berat / RB → poor'],
            ['', '- Tidak Baik → damaged'],
            [''],
            ['Status Penggunaan:', 'Pilih salah satu:'],
            ['', '- Digunakan Sendiri'],
            ['', '- Digunakan Pihak Lain'],
            ['', '- Tidak Digunakan'],
            ['', '- Sewa'],
            ['', '- Pinjam Pakai'],
            ['', '- Kerjasama Pemanfaatan'],
            [''],
            ['Status Sertifikasi:', 'Untuk Tanah/Bangunan:'],
            ['', '- Sudah Bersertifikat'],
            ['', '- Belum Bersertifikat'],
            ['', '- Dalam Proses'],
            [''],
            ['Jenis Sertifikat:', 'Untuk Tanah:'],
            ['', '- SHM (Sertifikat Hak Milik)'],
            ['', '- HGB (Hak Guna Bangunan)'],
            ['', '- HP (Hak Pakai)'],
            ['', '- HPL (Hak Pengelolaan)'],
            [''],
            ['CATATAN PENTING:'],
            ['- Kolom Kode Barang + NUP akan digunakan sebagai identifikasi unik'],
            ['- Barang dengan Kode sama tapi NUP berbeda = barang berbeda'],
            ['- Kolom yang tidak wajib bisa dikosongkan'],
            ['- File Excel dapat langsung di-import tanpa harus mengubah format'],
        ];
        
        foreach ($instructions as $idx => $row) {
            $rowNum = $idx + 1;
            if (is_array($row) && count($row) >= 2) {
                $guide->setCellValue('A' . $rowNum, $row[0]);
                $guide->setCellValue('B' . $rowNum, $row[1]);
            } elseif (is_array($row) && count($row) == 1) {
                $guide->setCellValue('A' . $rowNum, $row[0]);
            }
        }
        
        // Style the instruction sheet
        $guide->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $guide->getStyle('A3')->getFont()->setBold(true);
        $guide->getStyle('A11')->getFont()->setBold(true);
        $guide->getStyle('A45')->getFont()->setBold(true);
        $guide->getColumnDimension('A')->setWidth(25);
        $guide->getColumnDimension('B')->setWidth(50);

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_bmn_fixed_assets.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import assets from Excel (supports BMN format from SIMAK BMN/SIMAN).
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

            // Get headers from first row
            $headers = array_shift($rows);
            $headerMap = $this->mapBmnHeaders($headers);

            $imported = 0;
            $updated = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $data = $this->extractBmnData($row, $headerMap);
                    
                    // Generate asset code if not present
                    if (empty($data['asset_code'])) {
                        $nup = $data['nup'] ?? $rowNum;
                        $data['asset_code'] = 'BMN-' . ($data['kode_satker'] ?? 'UNKNOWN') . '-' . $nup;
                    }

                    // Check if required fields exist
                    if (empty($data['name'])) {
                        $errors[] = "Baris {$rowNum}: Nama Barang tidak ditemukan.";
                        continue;
                    }

                    // Set defaults
                    $data['hospital_id'] = hospital('id');
                    $data['status'] = $data['status'] ?? 'active';
                    $data['condition'] = $this->mapCondition($data['condition'] ?? 'Baik');
                    $data['acquisition_date'] = $this->parseDate($data['acquisition_date'] ?? null) ?? now()->format('Y-m-d');
                    $data['acquisition_cost'] = $this->parseNumber($data['acquisition_cost'] ?? 0);
                    $data['useful_life_years'] = $this->parseNumber($data['useful_life_years'] ?? 4);
                    $data['salvage_value'] = $data['salvage_value'] ?? 0;
                    $data['nilai_penyusutan'] = $this->parseNumber($data['nilai_penyusutan'] ?? 0);
                    $data['nilai_buku'] = $this->parseNumber($data['nilai_buku'] ?? 0);

                    // Check for existing asset using asset_code + NUP combination
                    // BMN items with same kode barang but different NUP are different assets
                    $nup = $data['nup'] ?? null;
                    
                    $existing = null;
                    if ($nup !== null) {
                        $existing = FixedAsset::where('hospital_id', hospital('id'))
                            ->where('asset_code', $data['asset_code'])
                            ->where('nup', $nup)
                            ->first();
                    }

                    $isNewAsset = false;
                    $asset = null;
                    
                    if ($existing) {
                        $existing->update($data);
                        $asset = $existing;
                        $updated++;
                    } else {
                        $asset = FixedAsset::create($data);
                        $isNewAsset = true;
                        $imported++;
                    }

                    // Create initial depreciation record from nilai_penyusutan if > 0
                    // This allows SIMAK BMN historical data to be recognized
                    $nilaiPenyusutan = $this->parseNumber($data['nilai_penyusutan'] ?? 0);
                    if ($nilaiPenyusutan > 0 && $asset) {
                        // Check if historical depreciation already exists
                        $hasHistorical = AssetDepreciation::where('fixed_asset_id', $asset->id)
                            ->where('period_month', 0) // period 0 = historical/initial
                            ->where('period_year', 0)
                            ->exists();
                        
                        if (!$hasHistorical) {
                            AssetDepreciation::create([
                                'fixed_asset_id' => $asset->id,
                                'period_month' => 0, // 0 indicates historical data
                                'period_year' => 0,
                                'depreciation_amount' => $nilaiPenyusutan,
                                'accumulated_depreciation' => $nilaiPenyusutan,
                                'book_value' => $this->parseNumber($data['acquisition_cost'] ?? 0) - $nilaiPenyusutan,
                                'notes' => 'Data historis dari import SIMAK BMN',
                            ]);
                        } else {
                            // Update existing historical record
                            AssetDepreciation::where('fixed_asset_id', $asset->id)
                                ->where('period_month', 0)
                                ->where('period_year', 0)
                                ->update([
                                    'depreciation_amount' => $nilaiPenyusutan,
                                    'accumulated_depreciation' => $nilaiPenyusutan,
                                    'book_value' => $this->parseNumber($data['acquisition_cost'] ?? 0) - $nilaiPenyusutan,
                                ]);
                        }
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNum}: " . $e->getMessage();
                }
            }

            $message = "Berhasil mengimpor {$imported} aset baru, {$updated} aset diperbarui.";
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' baris memiliki error.';
            }

            return redirect()->route('fixed-assets.index')
                ->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            return redirect()->route('fixed-assets.index')
                ->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Map BMN Excel headers to database columns.
     */
    private function mapBmnHeaders(array $headers): array
    {
        $mapping = [
            'Jenis BMN' => 'jenis_bmn',
            'Kode Satker' => 'kode_satker',
            'Nama Satker' => 'nama_satker',
            'Kode Barang' => 'asset_code',
            'NUP' => 'nup',
            'Nama Barang' => 'name',
            'Merk' => 'brand',
            'Tipe' => 'model',
            'Kondisi' => 'condition',
            'Umur Aset' => 'useful_life_years',
            'Tanggal Perolehan' => 'acquisition_date',
            'Tanggal Buku Pertama' => 'tanggal_buku_pertama',
            'Nilai Perolehan' => 'acquisition_cost',
            'Nilai Penyusutan' => 'nilai_penyusutan',
            'Nilai Buku' => 'nilai_buku',
            'Nilai Mutasi' => 'nilai_mutasi',
            'Alamat' => 'alamat_lengkap',
            'Kelurahan/Desa' => 'kelurahan',
            'Kelurahan' => 'kelurahan',
            'Kecamatan' => 'kecamatan',
            'Kab/Kota' => 'kabupaten_kota',
            'Provinsi' => 'provinsi',
            'Kode Pos' => 'kode_pos',
            'Status Penggunaan' => 'status_penggunaan',
            'Lokasi Ruang' => 'lokasi_ruang',
            'No Sertifikat' => 'no_sertifikat',
            'Jenis Sertipikat' => 'jenis_sertifikat',
            'Status Sertifikasi' => 'status_sertifikasi',
            'No Polisi' => 'no_polisi',
            'No BPKB' => 'no_bpkb',
            'Luas Tanah Seluruhnya' => 'luas_tanah_seluruhnya',
            'Luas Bangunan' => 'luas_bangunan',
            'Jumlah Lantai' => 'jumlah_lantai',
            'No PSP' => 'no_psp',
            'Tanggal PSP' => 'tanggal_psp',
            'Kode Register' => 'kode_register',
            // Add more mappings as needed
        ];

        $headerMap = [];
        foreach ($headers as $index => $header) {
            $header = trim($header ?? '');
            if (isset($mapping[$header])) {
                $headerMap[$mapping[$header]] = $index;
            }
        }

        return $headerMap;
    }

    /**
     * Extract BMN data from row using header map.
     */
    private function extractBmnData(array $row, array $headerMap): array
    {
        $data = [];
        foreach ($headerMap as $field => $index) {
            $value = trim($row[$index] ?? '');
            if ($value !== '') {
                $data[$field] = $value;
            }
        }
        return $data;
    }

    /**
     * Map BMN condition to database enum.
     */
    private function mapCondition(string $condition): string
    {
        $condition = strtolower(trim($condition));
        return match (true) {
            str_contains($condition, 'baik') => 'good',
            str_contains($condition, 'rusak ringan'), str_contains($condition, 'cukup') => 'fair',
            str_contains($condition, 'rusak berat'), str_contains($condition, 'kurang') => 'poor',
            str_contains($condition, 'rusak') => 'damaged',
            default => 'good',
        };
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric date
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try common date formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Parse number from string with formatting.
     */
    private function parseNumber($value): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }

        // Remove thousands separators and convert comma to dot
        $value = str_replace(['.', ','], ['', '.'], $value);
        return is_numeric($value) ? floatval($value) : 0;
    }

    private function authorizeHospital(FixedAsset $asset)
    {
        if ($asset->hospital_id !== hospital('id')) {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Calculate depreciation for bulk assets (selected or all).
     */
    public function bulkCalculate(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'calculate_mode' => 'required|in:all,selected',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:fixed_assets,id',
        ]);

        $query = FixedAsset::where('hospital_id', hospital('id'))
            ->where('status', 'active');

        if ($validated['calculate_mode'] === 'selected' && !empty($validated['asset_ids'])) {
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

        $mode = $validated['calculate_mode'] === 'all' ? 'semua' : 'yang dipilih';
        $month = \DateTime::createFromFormat('!m', $validated['period_month'])->format('F');
        
        return redirect()->route('fixed-assets.index')
            ->with('success', "Berhasil menghitung depresiasi untuk {$calculated} aset {$mode} (periode {$month} {$validated['period_year']}). {$skipped} aset dilewati.");
    }

    /**
     * Sync depreciation records to GL Expenses.
     */
    public function syncDepreciationToGl(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
        ]);

        // Get expense category - use selected or create default
        if (!empty($validated['expense_category_id'])) {
            $depreciationCategory = ExpenseCategory::find($validated['expense_category_id']);
        } else {
            // Fallback: Get or create default Depreciation expense category
            $depreciationCategory = ExpenseCategory::firstOrCreate(
                [
                    'hospital_id' => hospital('id'),
                    'account_code' => '6900',
                ],
                [
                    'account_name' => 'Biaya Penyusutan Aset Tetap',
                    'cost_type' => 'fixed',
                    'allocation_category' => 'depresiasi',
                    'is_active' => true,
                ]
            );
        }

        // Get all unsynced depreciations for the period
        $depreciations = AssetDepreciation::with(['fixedAsset'])
            ->whereHas('fixedAsset', function ($q) {
                $q->where('hospital_id', hospital('id'));
            })
            ->where('period_month', $validated['period_month'])
            ->where('period_year', $validated['period_year'])
            ->where('is_synced_to_gl', false)
            ->get();

        $synced = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($depreciations as $depreciation) {
                $asset = $depreciation->fixedAsset;

                // Skip if asset doesn't have cost center
                if (!$asset->cost_center_id) {
                    $errors[] = "Aset '{$asset->name}' tidak memiliki cost center.";
                    continue;
                }

                // Create GL Expense entry
                $glExpense = GlExpense::create([
                    'hospital_id' => hospital('id'),
                    'period_month' => $validated['period_month'],
                    'period_year' => $validated['period_year'],
                    'cost_center_id' => $asset->cost_center_id,
                    'expense_category_id' => $depreciationCategory->id,
                    'amount' => $depreciation->depreciation_amount,
                    'transaction_date' => now()->setYear($validated['period_year'])->setMonth($validated['period_month'])->endOfMonth(),
                    'reference_number' => 'DEP-' . $asset->asset_code . '-' . $validated['period_year'] . str_pad($validated['period_month'], 2, '0', STR_PAD_LEFT),
                    'description' => 'Depresiasi: ' . $asset->name,
                    'funding_source' => 'internal',
                ]);

                // Mark depreciation as synced
                $depreciation->update([
                    'is_synced_to_gl' => true,
                    'gl_expense_id' => $glExpense->id,
                ]);

                $synced++;
            }

            DB::commit();

            $message = "Berhasil sync {$synced} record depresiasi ke GL Expenses.";
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' record dilewati.';
            }

            return redirect()->route('fixed-assets.depreciation')
                ->with($errors ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('fixed-assets.depreciation')
                ->with('error', 'Gagal sync: ' . $e->getMessage());
        }
    }

    /**
     * Show Fixed Asset dashboard with summary statistics.
     */
    public function dashboard()
    {
        $hospitalId = hospital('id');

        // Summary statistics
        $totalAssets = FixedAsset::where('hospital_id', $hospitalId)->count();
        $activeAssets = FixedAsset::where('hospital_id', $hospitalId)->where('status', 'active')->count();
        $totalAcquisitionCost = FixedAsset::where('hospital_id', $hospitalId)->sum('acquisition_cost');
        
        // Calculate total book value
        $assets = FixedAsset::where('hospital_id', $hospitalId)->where('status', 'active')->get();
        $totalBookValue = $assets->sum(fn($a) => $a->current_book_value);
        $totalAccumulatedDepreciation = $totalAcquisitionCost - $totalBookValue;
        $totalMonthlyDepreciation = $assets->sum(fn($a) => $a->monthly_depreciation);

        // Distribution by Jenis BMN
        $jenisBmnDistribution = FixedAsset::where('hospital_id', $hospitalId)
            ->whereNotNull('jenis_bmn')
            ->selectRaw('jenis_bmn, COUNT(*) as count, SUM(acquisition_cost) as total_value')
            ->groupBy('jenis_bmn')
            ->get();

        // Top 10 assets by value
        $topAssets = FixedAsset::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->orderByDesc('acquisition_cost')
            ->limit(10)
            ->get();

        // Recent depreciations
        $recentDepreciations = AssetDepreciation::whereHas('fixedAsset', fn($q) => $q->where('hospital_id', $hospitalId))
            ->with('fixedAsset')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(10)
            ->get();

        // Monthly depreciation trend (last 12 months)
        $depreciationTrend = AssetDepreciation::whereHas('fixedAsset', fn($q) => $q->where('hospital_id', $hospitalId))
            ->selectRaw('period_year, period_month, SUM(depreciation_amount) as total')
            ->groupBy('period_year', 'period_month')
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->limit(12)
            ->get();

        // Sync status
        $unsyncedCount = AssetDepreciation::whereHas('fixedAsset', fn($q) => $q->where('hospital_id', $hospitalId))
            ->where('is_synced_to_gl', false)
            ->count();

        return view('fixed-assets.dashboard', compact(
            'totalAssets', 'activeAssets', 'totalAcquisitionCost', 'totalBookValue',
            'totalAccumulatedDepreciation', 'totalMonthlyDepreciation',
            'jenisBmnDistribution', 'topAssets', 'recentDepreciations',
            'depreciationTrend', 'unsyncedCount'
        ));
    }

    /**
     * Show depreciation contribution report.
     */
    public function depreciationReport(Request $request)
    {
        $hospitalId = hospital('id');
        $periodMonth = $request->input('period_month', now()->month);
        $periodYear = $request->input('period_year', now()->year);

        // Get depreciation by cost center
        $depreciationByCostCenter = AssetDepreciation::whereHas('fixedAsset', fn($q) => $q->where('hospital_id', $hospitalId))
            ->where('period_month', $periodMonth)
            ->where('period_year', $periodYear)
            ->with(['fixedAsset.costCenter'])
            ->get()
            ->groupBy(fn($d) => $d->fixedAsset->cost_center_id)
            ->map(function ($items, $costCenterId) {
                $costCenter = $items->first()->fixedAsset->costCenter;
                return [
                    'cost_center' => $costCenter,
                    'total_depreciation' => $items->sum('depreciation_amount'),
                    'asset_count' => $items->count(),
                    'items' => $items,
                ];
            })
            ->filter(fn($item) => $item['cost_center'] !== null)
            ->sortByDesc('total_depreciation');

        // Get total GL expenses for comparison
        $totalGlExpenses = GlExpense::where('hospital_id', $hospitalId)
            ->where('period_month', $periodMonth)
            ->where('period_year', $periodYear)
            ->sum('amount');

        $totalDepreciation = $depreciationByCostCenter->sum('total_depreciation');
        $depreciationPercentage = $totalGlExpenses > 0 ? ($totalDepreciation / $totalGlExpenses) * 100 : 0;

        // Get depreciation by Jenis BMN
        $depreciationByJenisBmn = AssetDepreciation::whereHas('fixedAsset', fn($q) => $q->where('hospital_id', $hospitalId))
            ->where('period_month', $periodMonth)
            ->where('period_year', $periodYear)
            ->with('fixedAsset')
            ->get()
            ->groupBy(fn($d) => $d->fixedAsset->jenis_bmn ?? 'Tidak Dikategorikan')
            ->map(fn($items, $jenis) => [
                'jenis_bmn' => $jenis,
                'total' => $items->sum('depreciation_amount'),
                'count' => $items->count(),
            ])
            ->sortByDesc('total');

        return view('fixed-assets.depreciation-report', compact(
            'depreciationByCostCenter', 'depreciationByJenisBmn',
            'totalDepreciation', 'totalGlExpenses', 'depreciationPercentage',
            'periodMonth', 'periodYear'
        ));
    }

    /**
     * Bulk delete fixed assets.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:fixed_assets,id',
        ]);

        try {
            $deleted = FixedAsset::where('hospital_id', hospital('id'))
                ->whereIn('id', $request->ids)
                ->delete();

            return redirect()->route('fixed-assets.index')
                ->with('success', "Berhasil menghapus {$deleted} aset.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }

    /**
     * Store a manual depreciation record.
     */
    public function storeDepreciation(Request $request, FixedAsset $fixedAsset)
    {
        $this->authorizeHospital($fixedAsset);

        $validated = $request->validate([
            'period_month' => 'required|integer|min:0|max:12',
            'period_year' => 'required|integer|min:0|max:2100',
            'depreciation_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate accumulated and book value
        $previousAccumulated = $fixedAsset->getAccumulatedDepreciation();
        $newAccumulated = $previousAccumulated + $validated['depreciation_amount'];
        $bookValue = max(0, $fixedAsset->acquisition_cost - $newAccumulated);

        AssetDepreciation::create([
            'fixed_asset_id' => $fixedAsset->id,
            'period_month' => $validated['period_month'],
            'period_year' => $validated['period_year'],
            'depreciation_amount' => $validated['depreciation_amount'],
            'accumulated_depreciation' => $newAccumulated,
            'book_value' => $bookValue,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('fixed-assets.show', $fixedAsset)
            ->with('success', 'Record depresiasi berhasil ditambahkan.');
    }

    /**
     * Update a depreciation record.
     */
    public function updateDepreciation(Request $request, FixedAsset $fixedAsset, AssetDepreciation $depreciation)
    {
        $this->authorizeHospital($fixedAsset);

        if ($depreciation->fixed_asset_id !== $fixedAsset->id) {
            abort(403, 'Invalid depreciation record.');
        }

        $validated = $request->validate([
            'period_month' => 'required|integer|min:0|max:12',
            'period_year' => 'required|integer|min:0|max:2100',
            'depreciation_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $depreciation->update([
            'period_month' => $validated['period_month'],
            'period_year' => $validated['period_year'],
            'depreciation_amount' => $validated['depreciation_amount'],
            'notes' => $validated['notes'],
        ]);

        // Recalculate all accumulated values for this asset
        $this->recalculateDepreciationAccumulation($fixedAsset);

        return redirect()->route('fixed-assets.show', $fixedAsset)
            ->with('success', 'Record depresiasi berhasil diperbarui.');
    }

    /**
     * Delete a depreciation record.
     */
    public function deleteDepreciation(FixedAsset $fixedAsset, AssetDepreciation $depreciation)
    {
        $this->authorizeHospital($fixedAsset);

        if ($depreciation->fixed_asset_id !== $fixedAsset->id) {
            abort(403, 'Invalid depreciation record.');
        }

        if ($depreciation->is_synced_to_gl) {
            return redirect()->route('fixed-assets.show', $fixedAsset)
                ->with('error', 'Tidak dapat menghapus record yang sudah sync ke GL.');
        }

        $depreciation->delete();

        // Recalculate all accumulated values for this asset
        $this->recalculateDepreciationAccumulation($fixedAsset);

        return redirect()->route('fixed-assets.show', $fixedAsset)
            ->with('success', 'Record depresiasi berhasil dihapus.');
    }

    /**
     * Recalculate accumulated depreciation for all records of an asset.
     */
    private function recalculateDepreciationAccumulation(FixedAsset $asset)
    {
        $depreciations = $asset->depreciations()
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->get();

        $accumulated = 0;
        foreach ($depreciations as $dep) {
            $accumulated += $dep->depreciation_amount;
            $dep->update([
                'accumulated_depreciation' => $accumulated,
                'book_value' => max(0, $asset->acquisition_cost - $accumulated),
            ]);
        }
    }
}
