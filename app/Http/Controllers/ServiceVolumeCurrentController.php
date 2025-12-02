<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ServiceVolumeCurrentController extends Controller
{
    public function masterBarang(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));
        $sort = $request->input('sort', 'nama_brng');
        $direction = $request->input('direction', 'asc');
        $perPage = (int) $request->input('per_page', 50);

        // Validate sort column and direction
        if (!in_array($sort, ['nama_brng', 'total_pendapatan'])) {
            $sort = 'nama_brng';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }
        if ($perPage < 10 || $perPage > 500) {
            $perPage = 50;
        }

        [$barangData, $grandTotals, $errorMessage, $totalRecords] = $this->getMasterBarangAggregates(
            $selectedYear,
            $selectedPoli,
            $search,
            $sort,
            $direction,
            $perPage
        );

        // Create paginator
        $currentPage = Paginator::resolveCurrentPage();
        $paginatedData = new LengthAwarePaginator(
            $barangData,
            $totalRecords,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $poliOptions = DB::connection('simrs')->select("
            SELECT kd_poli, nm_poli
            FROM poliklinik
            ORDER BY nm_poli ASC
        ");

        return view('service-volume-current.master-barang', [
            'year' => $selectedYear,
            'poli' => $selectedPoli,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
            'barangData' => $paginatedData,
            'grandTotals' => $grandTotals,
            'poliOptions' => $poliOptions,
            'availableYears' => $this->availableYears($currentYear),
            'errorMessage' => $errorMessage,
        ]);
    }

    public function exportMasterBarang(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));
        $sort = $request->input('sort', 'nama_brng');
        $direction = $request->input('direction', 'asc');

        // Validate sort column and direction
        if (!in_array($sort, ['nama_brng', 'total_pendapatan'])) {
            $sort = 'nama_brng';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        // For export, get all data (use very large perPage)
        [$barangData, $grandTotals, $errorMessage, $totalRecords] = $this->getMasterBarangAggregates(
            $selectedYear,
            $selectedPoli,
            $search,
            $sort,
            $direction,
            10000 // Large perPage for export
        );

        if ($errorMessage) {
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if (empty($barangData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data untuk filter tersebut.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'VOLUME PENGGUNAAN OBAT/BHP');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set period info
        $subtitle = 'Tahun: ' . $selectedYear;
        if ($selectedPoli) {
            $subtitle .= ' | Poli: ' . $selectedPoli;
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $header = [
            'Nama Barang',
            'Harga (Rp)',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Jumlah',
            'Total Pendapatan (Rp)',
        ];

        $sheet->fromArray($header, null, 'A3');

        // Style headers
        $headerRange = 'A3:P3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data rows
        $rowIndex = 4;
        foreach ($barangData as $row) {
            $sheet->fromArray([
                $row['nama'],
                $row['harga'],
                $row['jan'],
                $row['feb'],
                $row['mar'],
                $row['apr'],
                $row['may'],
                $row['jun'],
                $row['jul'],
                $row['aug'],
                $row['sep'],
                $row['oct'],
                $row['nov'],
                $row['dec'],
                $row['total_jumlah'],
                $row['total_pendapatan'],
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Grand total row
        $sheet->fromArray([
            'Grand Total',
            null,
            $grandTotals['jan'],
            $grandTotals['feb'],
            $grandTotals['mar'],
            $grandTotals['apr'],
            $grandTotals['may'],
            $grandTotals['jun'],
            $grandTotals['jul'],
            $grandTotals['aug'],
            $grandTotals['sep'],
            $grandTotals['oct'],
            $grandTotals['nov'],
            $grandTotals['dec'],
            $grandTotals['total_jumlah'],
            $grandTotals['total_pendapatan'],
        ], null, 'A' . $rowIndex);

        $lastRow = $rowIndex;

        // Borders for entire table (header + data + total)
        $tableRange = 'A3:P' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Autosize columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Align columns
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:O' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Currency format for Harga dan Total Pendapatan
        foreach (['B', 'P'] as $currencyColumn) {
            $sheet->getStyle($currencyColumn . '4:' . $currencyColumn . $lastRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $fileName = 'service-volume-master-barang-' . $selectedYear . '-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getMasterBarangAggregates(int $selectedYear, ?string $selectedPoli, string $search, string $sort = 'nama_brng', string $direction = 'asc', int $perPage = 50): array
    {
        $barangData = [];
        $errorMessage = null;
        $grandTotals = [
            'jan' => 0,
            'feb' => 0,
            'mar' => 0,
            'apr' => 0,
            'may' => 0,
            'jun' => 0,
            'jul' => 0,
            'aug' => 0,
            'sep' => 0,
            'oct' => 0,
            'nov' => 0,
            'dec' => 0,
            'total_jumlah' => 0,
            'total_pendapatan' => 0,
        ];
        $totalRecords = 0;

        try {
            $bindings = [$selectedYear];

            // Base SQL for building queries
            $baseWhere = "WHERE YEAR(dpo.tgl_perawatan) = ?";
            if ($selectedPoli) {
                $baseWhere .= " AND reg.kd_poli = ? ";
                $bindings[] = $selectedPoli;
            }
            if ($search !== '') {
                $baseWhere .= " AND db.nama_brng LIKE ? ";
                $bindings[] = '%' . $search . '%';
            }

            // First, get grand totals from ALL data (not paginated)
            $grandTotalsSql = "
                SELECT
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 1 THEN dpo.jml ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 2 THEN dpo.jml ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 3 THEN dpo.jml ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 4 THEN dpo.jml ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 5 THEN dpo.jml ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 6 THEN dpo.jml ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 7 THEN dpo.jml ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 8 THEN dpo.jml ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 9 THEN dpo.jml ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 10 THEN dpo.jml ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 11 THEN dpo.jml ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 12 THEN dpo.jml ELSE 0 END) AS `dec`,
                    SUM(dpo.jml) AS total_jumlah,
                    SUM(dpo.total) AS total_pendapatan
                FROM detail_pemberian_obat dpo
                INNER JOIN databarang db ON db.kode_brng = dpo.kode_brng
                INNER JOIN reg_periksa reg ON reg.no_rawat = dpo.no_rawat
                {$baseWhere}
            ";

            $grandTotalsResult = DB::connection('simrs')->select($grandTotalsSql, $bindings);
            if (!empty($grandTotalsResult)) {
                $gt = $grandTotalsResult[0];
                $grandTotals['jan'] = (float) ($gt->jan ?? 0);
                $grandTotals['feb'] = (float) ($gt->feb ?? 0);
                $grandTotals['mar'] = (float) ($gt->mar ?? 0);
                $grandTotals['apr'] = (float) ($gt->apr ?? 0);
                $grandTotals['may'] = (float) ($gt->may ?? 0);
                $grandTotals['jun'] = (float) ($gt->jun ?? 0);
                $grandTotals['jul'] = (float) ($gt->jul ?? 0);
                $grandTotals['aug'] = (float) ($gt->aug ?? 0);
                $grandTotals['sep'] = (float) ($gt->sep ?? 0);
                $grandTotals['oct'] = (float) ($gt->oct ?? 0);
                $grandTotals['nov'] = (float) ($gt->nov ?? 0);
                $grandTotals['dec'] = (float) ($gt->dec ?? 0);
                $grandTotals['total_jumlah'] = (float) ($gt->total_jumlah ?? 0);
                $grandTotals['total_pendapatan'] = (float) ($gt->total_pendapatan ?? 0);
            }

            // Get total count for pagination
            $countSql = "
                SELECT COUNT(DISTINCT dpo.kode_brng) as total
                FROM detail_pemberian_obat dpo
                INNER JOIN databarang db ON db.kode_brng = dpo.kode_brng
                INNER JOIN reg_periksa reg ON reg.no_rawat = dpo.no_rawat
                {$baseWhere}
            ";
            $countResult = DB::connection('simrs')->select($countSql, $bindings);
            $totalRecords = (int) ($countResult[0]->total ?? 0);

            // Get paginated data
            $currentPage = Paginator::resolveCurrentPage();
            $offset = ($currentPage - 1) * $perPage;

            $sql = "
                SELECT
                    dpo.kode_brng,
                    db.nama_brng,
                    dpo.biaya_obat AS harga,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 1 THEN dpo.jml ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 2 THEN dpo.jml ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 3 THEN dpo.jml ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 4 THEN dpo.jml ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 5 THEN dpo.jml ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 6 THEN dpo.jml ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 7 THEN dpo.jml ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 8 THEN dpo.jml ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 9 THEN dpo.jml ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 10 THEN dpo.jml ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 11 THEN dpo.jml ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(dpo.tgl_perawatan) = 12 THEN dpo.jml ELSE 0 END) AS `dec`,
                    SUM(dpo.jml) AS total_jumlah,
                    SUM(dpo.total) AS total_pendapatan
                FROM detail_pemberian_obat dpo
                INNER JOIN databarang db ON db.kode_brng = dpo.kode_brng
                INNER JOIN reg_periksa reg ON reg.no_rawat = dpo.no_rawat
                {$baseWhere}
                GROUP BY dpo.kode_brng, db.nama_brng, dpo.biaya_obat
            ";

            // Apply sorting
            if ($sort === 'total_pendapatan') {
                $sql .= " ORDER BY total_pendapatan " . strtoupper($direction);
            } else {
                $sql .= " ORDER BY db.nama_brng " . strtoupper($direction);
            }

            // Apply pagination
            $sql .= " LIMIT ? OFFSET ? ";
            $bindings[] = $perPage;
            $bindings[] = $offset;

            $rows = DB::connection('simrs')->select($sql, $bindings);

            foreach ($rows as $row) {
                $barangData[] = [
                    'kode' => $row->kode_brng,
                    'nama' => $row->nama_brng,
                    'harga' => (float) $row->harga,
                    'jan' => (float) $row->jan,
                    'feb' => (float) $row->feb,
                    'mar' => (float) $row->mar,
                    'apr' => (float) $row->apr,
                    'may' => (float) $row->may,
                    'jun' => (float) $row->jun,
                    'jul' => (float) $row->jul,
                    'aug' => (float) $row->aug,
                    'sep' => (float) $row->sep,
                    'oct' => (float) $row->oct,
                    'nov' => (float) $row->nov,
                    'dec' => (float) $row->dec,
                    'total_jumlah' => (float) $row->total_jumlah,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to load master barang volume', [
                'year' => $selectedYear,
                'poli' => $selectedPoli,
                'search' => $search,
                'message' => $exception->getMessage(),
            ]);
            $errorMessage = 'Gagal memuat data master barang dari SIMRS. Silakan coba lagi atau hubungi administrator.';
        }

        return [$barangData, $grandTotals, $errorMessage, $totalRecords];
    }

    public function tindakanRawatJalan(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$tindakanData, $grandTotals, $errorMessage] = $this->getTindakanRawatJalanAggregates(
            $selectedYear,
            $selectedPoli,
            $search
        );

        $poliOptions = DB::connection('simrs')->select("
            SELECT kd_poli, nm_poli
            FROM poliklinik
            ORDER BY nm_poli ASC
        ");

        return view('service-volume-current.tindakan-rawat-jalan', [
            'year' => $selectedYear,
            'poli' => $selectedPoli,
            'search' => $search,
            'tindakanData' => $tindakanData,
            'grandTotals' => $grandTotals,
            'poliOptions' => $poliOptions,
            'availableYears' => $this->availableYears($currentYear),
            'errorMessage' => $errorMessage,
        ]);
    }

    public function exportTindakanRawatJalan(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$tindakanData, $grandTotals, $errorMessage] = $this->getTindakanRawatJalanAggregates(
            $selectedYear,
            $selectedPoli,
            $search
        );

        if ($errorMessage) {
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if (empty($tindakanData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data untuk filter tersebut.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'VOLUME TINDAKAN RAWAT JALAN');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set period info
        $subtitle = 'Tahun: ' . $selectedYear;
        if ($selectedPoli) {
            $subtitle .= ' | Poli: ' . $selectedPoli;
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $header = [
            'Tindakan Rawat Jalan',
            'Harga (Rp)',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Jumlah Tindakan',
            'Total Pendapatan (Rp)',
        ];

        $sheet->fromArray($header, null, 'A3');

        // Style headers
        $headerRange = 'A3:P3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data rows
        $rowIndex = 4;
        foreach ($tindakanData as $row) {
            $sheet->fromArray([
                $row['nama'],
                $row['harga'],
                $row['jan'],
                $row['feb'],
                $row['mar'],
                $row['apr'],
                $row['may'],
                $row['jun'],
                $row['jul'],
                $row['aug'],
                $row['sep'],
                $row['oct'],
                $row['nov'],
                $row['dec'],
                $row['total_tindakan'],
                $row['total_pendapatan'],
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Grand total row
        $sheet->fromArray([
            'Grand Total',
            null,
            $grandTotals['jan'],
            $grandTotals['feb'],
            $grandTotals['mar'],
            $grandTotals['apr'],
            $grandTotals['may'],
            $grandTotals['jun'],
            $grandTotals['jul'],
            $grandTotals['aug'],
            $grandTotals['sep'],
            $grandTotals['oct'],
            $grandTotals['nov'],
            $grandTotals['dec'],
            $grandTotals['total_tindakan'],
            $grandTotals['total_pendapatan'],
        ], null, 'A' . $rowIndex);

        $lastRow = $rowIndex;

        // Borders for entire table (header + data + total)
        $tableRange = 'A3:P' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Autosize columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Align columns
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:O' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Currency format for Harga dan Total Pendapatan
        foreach (['B', 'P'] as $currencyColumn) {
            $sheet->getStyle($currencyColumn . '4:' . $currencyColumn . $lastRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $fileName = 'service-volume-ralan-' . $selectedYear . '-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function tindakanRawatInap(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedBangsal = array_filter((array) $request->input('bangsal', []));
        $search = trim((string) $request->input('search'));

        [$tindakanData, $grandTotals, $errorMessage] = $this->getTindakanRawatInapAggregates(
            $selectedYear,
            $selectedBangsal,
            $search
        );

        $bangsalOptions = DB::connection('simrs')->select("
            SELECT kd_bangsal, nm_bangsal
            FROM bangsal
            ORDER BY nm_bangsal ASC
        ");

        return view('service-volume-current.tindakan-rawat-inap', [
            'year' => $selectedYear,
            'bangsal' => $selectedBangsal,
            'search' => $search,
            'tindakanData' => $tindakanData,
            'grandTotals' => $grandTotals,
            'bangsalOptions' => $bangsalOptions,
            'availableYears' => $this->availableYears($currentYear),
            'errorMessage' => $errorMessage,
        ]);
    }

    public function exportTindakanRawatInap(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedBangsal = array_filter((array) $request->input('bangsal', []));
        $search = trim((string) $request->input('search'));

        [$tindakanData, $grandTotals, $errorMessage] = $this->getTindakanRawatInapAggregates(
            $selectedYear,
            $selectedBangsal,
            $search
        );

        if ($errorMessage) {
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if (empty($tindakanData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data untuk filter tersebut.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'VOLUME TINDAKAN RAWAT INAP');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set period info
        $subtitle = 'Tahun: ' . $selectedYear;
        if (!empty($selectedBangsal)) {
            $subtitle .= ' | Bangsal: ' . implode(', ', $selectedBangsal);
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $header = [
            'Tindakan Rawat Inap',
            'Harga (Rp)',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Jumlah Tindakan',
            'Total Pendapatan (Rp)',
        ];

        $sheet->fromArray($header, null, 'A3');

        // Style headers
        $headerRange = 'A3:P3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data rows
        $rowIndex = 4;
        foreach ($tindakanData as $row) {
            $sheet->fromArray([
                $row['nama'],
                $row['harga'],
                $row['jan'],
                $row['feb'],
                $row['mar'],
                $row['apr'],
                $row['may'],
                $row['jun'],
                $row['jul'],
                $row['aug'],
                $row['sep'],
                $row['oct'],
                $row['nov'],
                $row['dec'],
                $row['total_tindakan'],
                $row['total_pendapatan'],
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Grand total row
        $sheet->fromArray([
            'Grand Total',
            null,
            $grandTotals['jan'],
            $grandTotals['feb'],
            $grandTotals['mar'],
            $grandTotals['apr'],
            $grandTotals['may'],
            $grandTotals['jun'],
            $grandTotals['jul'],
            $grandTotals['aug'],
            $grandTotals['sep'],
            $grandTotals['oct'],
            $grandTotals['nov'],
            $grandTotals['dec'],
            $grandTotals['total_tindakan'],
            $grandTotals['total_pendapatan'],
        ], null, 'A' . $rowIndex);

        $lastRow = $rowIndex;

        // Borders for entire table (header + data + total)
        $tableRange = 'A3:P' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Autosize columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Align columns
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:O' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Currency format for Harga dan Total Pendapatan
        foreach (['B', 'P'] as $currencyColumn) {
            $sheet->getStyle($currencyColumn . '4:' . $currencyColumn . $lastRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $fileName = 'service-volume-ranap-' . $selectedYear . '-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function operasi(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedStatus = $request->input('status');
        if ($selectedStatus === 'all') {
            $selectedStatus = null;
        }
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$operasiData, $grandTotals, $errorMessage] = $this->getOperasiAggregates(
            $selectedYear,
            $selectedStatus,
            $selectedPoli,
            $search
        );

        $poliOptions = DB::connection('simrs')->select("
            SELECT kd_poli, nm_poli
            FROM poliklinik
            ORDER BY nm_poli ASC
        ");

        return view('service-volume-current.operasi', [
            'year' => $selectedYear,
            'status' => $selectedStatus,
            'poli' => $selectedPoli,
            'search' => $search,
            'operasiData' => $operasiData,
            'grandTotals' => $grandTotals,
            'poliOptions' => $poliOptions,
            'availableYears' => $this->availableYears($currentYear),
            'errorMessage' => $errorMessage,
        ]);
    }

    public function exportOperasi(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedStatus = $request->input('status');
        if ($selectedStatus === 'all') {
            $selectedStatus = null;
        }
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$operasiData, $grandTotals, $errorMessage] = $this->getOperasiAggregates(
            $selectedYear,
            $selectedStatus,
            $selectedPoli,
            $search
        );

        if ($errorMessage) {
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if (empty($operasiData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data untuk filter tersebut.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'VOLUME TINDAKAN OPERASI');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set period info
        $subtitle = 'Tahun: ' . $selectedYear;
        if ($selectedStatus) {
            $subtitle .= ' | Status: ' . $selectedStatus;
        }
        if ($selectedPoli) {
            $subtitle .= ' | Poli: ' . $selectedPoli;
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $header = [
            'Tindakan Operasi',
            'Harga (Rp)',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Jumlah Operasi',
            'Total Pendapatan (Rp)',
        ];

        $sheet->fromArray($header, null, 'A3');

        // Style headers
        $headerRange = 'A3:P3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data rows
        $rowIndex = 4;
        foreach ($operasiData as $row) {
            $sheet->fromArray([
                $row['nama'],
                $row['harga'],
                $row['jan'],
                $row['feb'],
                $row['mar'],
                $row['apr'],
                $row['may'],
                $row['jun'],
                $row['jul'],
                $row['aug'],
                $row['sep'],
                $row['oct'],
                $row['nov'],
                $row['dec'],
                $row['total_tindakan'],
                $row['total_pendapatan'],
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Grand total row
        $sheet->fromArray([
            'Grand Total',
            null,
            $grandTotals['jan'],
            $grandTotals['feb'],
            $grandTotals['mar'],
            $grandTotals['apr'],
            $grandTotals['may'],
            $grandTotals['jun'],
            $grandTotals['jul'],
            $grandTotals['aug'],
            $grandTotals['sep'],
            $grandTotals['oct'],
            $grandTotals['nov'],
            $grandTotals['dec'],
            $grandTotals['total_tindakan'],
            $grandTotals['total_pendapatan'],
        ], null, 'A' . $rowIndex);

        $lastRow = $rowIndex;

        // Borders for entire table (header + data + total)
        $tableRange = 'A3:P' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Autosize columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Align columns
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:O' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Currency format for Harga dan Total Pendapatan
        foreach (['B', 'P'] as $currencyColumn) {
            $sheet->getStyle($currencyColumn . '4:' . $currencyColumn . $lastRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $fileName = 'service-volume-operasi-' . $selectedYear . '-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function laboratorium(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedStatus = $request->input('status');
        if ($selectedStatus === 'all') {
            $selectedStatus = null;
        }
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$laboratoriumData, $grandTotals, $errorMessage] = $this->getLaboratoriumAggregates(
            $selectedYear,
            $selectedStatus,
            $selectedPoli,
            $search
        );

        $poliOptions = DB::connection('simrs')->select("
            SELECT kd_poli, nm_poli
            FROM poliklinik
            ORDER BY nm_poli ASC
        ");

        return view('service-volume-current.laboratorium', [
            'year' => $selectedYear,
            'status' => $selectedStatus,
            'poli' => $selectedPoli,
            'search' => $search,
            'laboratoriumData' => $laboratoriumData,
            'grandTotals' => $grandTotals,
            'poliOptions' => $poliOptions,
            'availableYears' => $this->availableYears($currentYear),
            'errorMessage' => $errorMessage,
        ]);
    }

    public function exportLaboratorium(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedStatus = $request->input('status');
        if ($selectedStatus === 'all') {
            $selectedStatus = null;
        }
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$laboratoriumData, $grandTotals, $errorMessage] = $this->getLaboratoriumAggregates(
            $selectedYear,
            $selectedStatus,
            $selectedPoli,
            $search
        );

        if ($errorMessage) {
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if (empty($laboratoriumData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data untuk filter tersebut.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'VOLUME TINDAKAN LABORATORIUM');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set period info
        $subtitle = 'Tahun: ' . $selectedYear;
        if ($selectedStatus) {
            $subtitle .= ' | Status: ' . $selectedStatus;
        }
        if ($selectedPoli) {
            $subtitle .= ' | Poli: ' . $selectedPoli;
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $header = [
            'Tindakan Laboratorium',
            'Harga (Rp)',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Jumlah Pemeriksaan',
            'Total Pendapatan (Rp)',
        ];

        $sheet->fromArray($header, null, 'A3');

        // Style headers
        $headerRange = 'A3:P3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data rows
        $rowIndex = 4;
        foreach ($laboratoriumData as $row) {
            $sheet->fromArray([
                $row['nama'],
                $row['harga'],
                $row['jan'],
                $row['feb'],
                $row['mar'],
                $row['apr'],
                $row['may'],
                $row['jun'],
                $row['jul'],
                $row['aug'],
                $row['sep'],
                $row['oct'],
                $row['nov'],
                $row['dec'],
                $row['total_tindakan'],
                $row['total_pendapatan'],
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Grand total row
        $sheet->fromArray([
            'Grand Total',
            null,
            $grandTotals['jan'],
            $grandTotals['feb'],
            $grandTotals['mar'],
            $grandTotals['apr'],
            $grandTotals['may'],
            $grandTotals['jun'],
            $grandTotals['jul'],
            $grandTotals['aug'],
            $grandTotals['sep'],
            $grandTotals['oct'],
            $grandTotals['nov'],
            $grandTotals['dec'],
            $grandTotals['total_tindakan'],
            $grandTotals['total_pendapatan'],
        ], null, 'A' . $rowIndex);

        $lastRow = $rowIndex;

        // Borders for entire table (header + data + total)
        $tableRange = 'A3:P' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Autosize columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Align columns
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:O' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Currency format for Harga dan Total Pendapatan
        foreach (['B', 'P'] as $currencyColumn) {
            $sheet->getStyle($currencyColumn . '4:' . $currencyColumn . $lastRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $fileName = 'service-volume-laboratorium-' . $selectedYear . '-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function radiologi(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedStatus = $request->input('status');
        if ($selectedStatus === 'all') {
            $selectedStatus = null;
        }
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$radiologiData, $grandTotals, $errorMessage] = $this->getRadiologiAggregates(
            $selectedYear,
            $selectedStatus,
            $selectedPoli,
            $search
        );

        $poliOptions = DB::connection('simrs')->select("
            SELECT kd_poli, nm_poli
            FROM poliklinik
            ORDER BY nm_poli ASC
        ");

        return view('service-volume-current.radiologi', [
            'year' => $selectedYear,
            'status' => $selectedStatus,
            'poli' => $selectedPoli,
            'search' => $search,
            'radiologiData' => $radiologiData,
            'grandTotals' => $grandTotals,
            'poliOptions' => $poliOptions,
            'availableYears' => $this->availableYears($currentYear),
            'errorMessage' => $errorMessage,
        ]);
    }

    public function exportRadiologi(Request $request)
    {
        $currentYear = now()->year;
        $selectedYear = (int) $request->input('year', $currentYear);
        $selectedStatus = $request->input('status');
        if ($selectedStatus === 'all') {
            $selectedStatus = null;
        }
        $selectedPoli = $request->input('poli');
        $search = trim((string) $request->input('search'));

        [$radiologiData, $grandTotals, $errorMessage] = $this->getRadiologiAggregates(
            $selectedYear,
            $selectedStatus,
            $selectedPoli,
            $search
        );

        if ($errorMessage) {
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if (empty($radiologiData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data untuk filter tersebut.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'VOLUME TINDAKAN RADIOLOGI');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set period info
        $subtitle = 'Tahun: ' . $selectedYear;
        if ($selectedStatus) {
            $subtitle .= ' | Status: ' . $selectedStatus;
        }
        if ($selectedPoli) {
            $subtitle .= ' | Poli: ' . $selectedPoli;
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $header = [
            'Tindakan Radiologi',
            'Harga (Rp)',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Jumlah Pemeriksaan',
            'Total Pendapatan (Rp)',
        ];

        $sheet->fromArray($header, null, 'A3');

        // Style headers
        $headerRange = 'A3:P3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data rows
        $rowIndex = 4;
        foreach ($radiologiData as $row) {
            $sheet->fromArray([
                $row['nama'],
                $row['harga'],
                $row['jan'],
                $row['feb'],
                $row['mar'],
                $row['apr'],
                $row['may'],
                $row['jun'],
                $row['jul'],
                $row['aug'],
                $row['sep'],
                $row['oct'],
                $row['nov'],
                $row['dec'],
                $row['total_tindakan'],
                $row['total_pendapatan'],
            ], null, 'A' . $rowIndex);

            $rowIndex++;
        }

        // Grand total row
        $sheet->fromArray([
            'Grand Total',
            null,
            $grandTotals['jan'],
            $grandTotals['feb'],
            $grandTotals['mar'],
            $grandTotals['apr'],
            $grandTotals['may'],
            $grandTotals['jun'],
            $grandTotals['jul'],
            $grandTotals['aug'],
            $grandTotals['sep'],
            $grandTotals['oct'],
            $grandTotals['nov'],
            $grandTotals['dec'],
            $grandTotals['total_tindakan'],
            $grandTotals['total_pendapatan'],
        ], null, 'A' . $rowIndex);

        $lastRow = $rowIndex;

        // Borders for entire table (header + data + total)
        $tableRange = 'A3:P' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Autosize columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Align columns
        $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C4:O' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Currency format for Harga dan Total Pendapatan
        foreach (['B', 'P'] as $currencyColumn) {
            $sheet->getStyle($currencyColumn . '4:' . $currencyColumn . $lastRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $fileName = 'service-volume-radiologi-' . $selectedYear . '-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getTindakanRawatInapAggregates(int $selectedYear, array $selectedBangsal, string $search): array
    {
        $tindakanData = [];
        $errorMessage = null;
        $grandTotals = [
            'jan' => 0,
            'feb' => 0,
            'mar' => 0,
            'apr' => 0,
            'may' => 0,
            'jun' => 0,
            'jul' => 0,
            'aug' => 0,
            'sep' => 0,
            'oct' => 0,
            'nov' => 0,
            'dec' => 0,
            'total_tindakan' => 0,
            'total_pendapatan' => 0,
        ];

        try {
            $bindings = [$selectedYear];

            $sql = "
                SELECT
                    tu.kd_jenis_prw AS kode_tindakan,
                    tu.nm_perawatan AS nama_tindakan,
                    tu.tarif_master AS harga,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 1 THEN 1 ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 2 THEN 1 ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 3 THEN 1 ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 4 THEN 1 ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 5 THEN 1 ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 6 THEN 1 ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 7 THEN 1 ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 8 THEN 1 ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 9 THEN 1 ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 10 THEN 1 ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 11 THEN 1 ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 12 THEN 1 ELSE 0 END) AS `dec`,
                    SUM(tu.jumlah) AS total_tindakan,
                    SUM(tu.biaya_rawat) AS total_pendapatan
                FROM (
                    SELECT
                        rid.no_rawat,
                        rid.tgl_perawatan,
                        rid.kd_jenis_prw,
                        jpi.nm_perawatan,
                        jpi.total_byrdrpr AS tarif_master,
                        1 AS jumlah,
                        rid.biaya_rawat
                    FROM rawat_inap_dr rid
                    INNER JOIN jns_perawatan_inap jpi ON jpi.kd_jenis_prw = rid.kd_jenis_prw

                    UNION ALL

                    SELECT
                        rip.no_rawat,
                        rip.tgl_perawatan,
                        rip.kd_jenis_prw,
                        jpi.nm_perawatan,
                        jpi.total_byrdrpr AS tarif_master,
                        1 AS jumlah,
                        rip.biaya_rawat
                    FROM rawat_inap_pr rip
                    INNER JOIN jns_perawatan_inap jpi ON jpi.kd_jenis_prw = rip.kd_jenis_prw

                    UNION ALL

                    SELECT
                        ridp.no_rawat,
                        ridp.tgl_perawatan,
                        ridp.kd_jenis_prw,
                        jpi.nm_perawatan,
                        jpi.total_byrdrpr AS tarif_master,
                        1 AS jumlah,
                        ridp.biaya_rawat
                    FROM rawat_inap_drpr ridp
                    INNER JOIN jns_perawatan_inap jpi ON jpi.kd_jenis_prw = ridp.kd_jenis_prw
                ) AS tu
                LEFT JOIN kamar_inap ran ON ran.no_rawat = tu.no_rawat
                    AND DATE(tu.tgl_perawatan) BETWEEN ran.tgl_masuk AND COALESCE(ran.tgl_keluar, '9999-12-31')
                LEFT JOIN kamar k ON k.kd_kamar = ran.kd_kamar
                LEFT JOIN bangsal b ON b.kd_bangsal = k.kd_bangsal
                WHERE YEAR(tu.tgl_perawatan) = ?
            ";

            if (!empty($selectedBangsal)) {
                $placeholders = implode(',', array_fill(0, count($selectedBangsal), '?'));
                $sql .= " AND b.kd_bangsal IN ($placeholders) ";
                $bindings = array_merge($bindings, $selectedBangsal);
            }

            if ($search !== '') {
                $sql .= " AND tu.nm_perawatan LIKE ? ";
                $bindings[] = '%' . $search . '%';
            }

            $sql .= "
                GROUP BY tu.kd_jenis_prw, tu.nm_perawatan, tu.tarif_master
                ORDER BY tu.nm_perawatan ASC
            ";

            $rows = DB::connection('simrs')->select($sql, $bindings);

            foreach ($rows as $row) {
                $tindakanData[] = [
                    'kode' => $row->kode_tindakan,
                    'nama' => $row->nama_tindakan,
                    'harga' => (float) $row->harga,
                    'jan' => (int) $row->jan,
                    'feb' => (int) $row->feb,
                    'mar' => (int) $row->mar,
                    'apr' => (int) $row->apr,
                    'may' => (int) $row->may,
                    'jun' => (int) $row->jun,
                    'jul' => (int) $row->jul,
                    'aug' => (int) $row->aug,
                    'sep' => (int) $row->sep,
                    'oct' => (int) $row->oct,
                    'nov' => (int) $row->nov,
                    'dec' => (int) $row->dec,
                    'total_tindakan' => (int) $row->total_tindakan,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];

                $grandTotals['jan'] += (int) $row->jan;
                $grandTotals['feb'] += (int) $row->feb;
                $grandTotals['mar'] += (int) $row->mar;
                $grandTotals['apr'] += (int) $row->apr;
                $grandTotals['may'] += (int) $row->may;
                $grandTotals['jun'] += (int) $row->jun;
                $grandTotals['jul'] += (int) $row->jul;
                $grandTotals['aug'] += (int) $row->aug;
                $grandTotals['sep'] += (int) $row->sep;
                $grandTotals['oct'] += (int) $row->oct;
                $grandTotals['nov'] += (int) $row->nov;
                $grandTotals['dec'] += (int) $row->dec;
                $grandTotals['total_tindakan'] += (int) $row->total_tindakan;
                $grandTotals['total_pendapatan'] += (float) $row->total_pendapatan;
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to load tindakan rawat inap volume', [
                'year' => $selectedYear,
                'bangsal' => $selectedBangsal,
                'search' => $search,
                'message' => $exception->getMessage(),
            ]);
            $errorMessage = 'Gagal memuat data dari SIMRS. Silakan coba lagi atau hubungi administrator.';
        }

        return [$tindakanData, $grandTotals, $errorMessage];
    }

    private function getLaboratoriumAggregates(int $selectedYear, ?string $selectedStatus, ?string $selectedPoli, string $search): array
    {
        $laboratoriumData = [];
        $errorMessage = null;
        $grandTotals = [
            'jan' => 0,
            'feb' => 0,
            'mar' => 0,
            'apr' => 0,
            'may' => 0,
            'jun' => 0,
            'jul' => 0,
            'aug' => 0,
            'sep' => 0,
            'oct' => 0,
            'nov' => 0,
            'dec' => 0,
            'total_tindakan' => 0,
            'total_pendapatan' => 0,
        ];

        try {
            $bindings = [$selectedYear];

            $sql = "
                SELECT
                    jpl.kd_jenis_prw,
                    jpl.nm_perawatan,
                    jpl.total_byr AS harga,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 1 THEN 1 ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 2 THEN 1 ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 3 THEN 1 ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 4 THEN 1 ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 5 THEN 1 ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 6 THEN 1 ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 7 THEN 1 ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 8 THEN 1 ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 9 THEN 1 ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 10 THEN 1 ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 11 THEN 1 ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(pl.tgl_periksa) = 12 THEN 1 ELSE 0 END) AS `dec`,
                    COUNT(*) AS total_tindakan,
                    SUM(pl.biaya) AS total_pendapatan
                FROM periksa_lab pl
                INNER JOIN jns_perawatan_lab jpl ON jpl.kd_jenis_prw = pl.kd_jenis_prw
                LEFT JOIN reg_periksa reg ON reg.no_rawat = pl.no_rawat
                WHERE YEAR(pl.tgl_periksa) = ?
            ";

            if ($selectedStatus) {
                $sql .= " AND pl.status = ? ";
                $bindings[] = $selectedStatus;
            }

            if ($selectedPoli) {
                $sql .= " AND reg.kd_poli = ? ";
                $bindings[] = $selectedPoli;
            }

            if ($search !== '') {
                $sql .= " AND jpl.nm_perawatan LIKE ? ";
                $bindings[] = '%' . $search . '%';
            }

            $sql .= "
                GROUP BY jpl.kd_jenis_prw, jpl.nm_perawatan, jpl.total_byr
                ORDER BY jpl.nm_perawatan ASC
            ";

            $rows = DB::connection('simrs')->select($sql, $bindings);

            foreach ($rows as $row) {
                $laboratoriumData[] = [
                    'kode' => $row->kd_jenis_prw,
                    'nama' => $row->nm_perawatan,
                    'harga' => (float) $row->harga,
                    'jan' => (int) $row->jan,
                    'feb' => (int) $row->feb,
                    'mar' => (int) $row->mar,
                    'apr' => (int) $row->apr,
                    'may' => (int) $row->may,
                    'jun' => (int) $row->jun,
                    'jul' => (int) $row->jul,
                    'aug' => (int) $row->aug,
                    'sep' => (int) $row->sep,
                    'oct' => (int) $row->oct,
                    'nov' => (int) $row->nov,
                    'dec' => (int) $row->dec,
                    'total_tindakan' => (int) $row->total_tindakan,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];

                $grandTotals['jan'] += (int) $row->jan;
                $grandTotals['feb'] += (int) $row->feb;
                $grandTotals['mar'] += (int) $row->mar;
                $grandTotals['apr'] += (int) $row->apr;
                $grandTotals['may'] += (int) $row->may;
                $grandTotals['jun'] += (int) $row->jun;
                $grandTotals['jul'] += (int) $row->jul;
                $grandTotals['aug'] += (int) $row->aug;
                $grandTotals['sep'] += (int) $row->sep;
                $grandTotals['oct'] += (int) $row->oct;
                $grandTotals['nov'] += (int) $row->nov;
                $grandTotals['dec'] += (int) $row->dec;
                $grandTotals['total_tindakan'] += (int) $row->total_tindakan;
                $grandTotals['total_pendapatan'] += (float) $row->total_pendapatan;
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to load laboratorium volume', [
                'year' => $selectedYear,
                'status' => $selectedStatus,
                'poli' => $selectedPoli,
                'search' => $search,
                'message' => $exception->getMessage(),
            ]);
            $errorMessage = 'Gagal memuat data laboratorium dari SIMRS. Silakan coba lagi atau hubungi administrator.';
        }

        return [$laboratoriumData, $grandTotals, $errorMessage];
    }

    private function getRadiologiAggregates(int $selectedYear, ?string $selectedStatus, ?string $selectedPoli, string $search): array
    {
        $radiologiData = [];
        $errorMessage = null;
        $grandTotals = [
            'jan' => 0,
            'feb' => 0,
            'mar' => 0,
            'apr' => 0,
            'may' => 0,
            'jun' => 0,
            'jul' => 0,
            'aug' => 0,
            'sep' => 0,
            'oct' => 0,
            'nov' => 0,
            'dec' => 0,
            'total_tindakan' => 0,
            'total_pendapatan' => 0,
        ];

        try {
            $bindings = [$selectedYear];

            $sql = "
                SELECT
                    jpr.kd_jenis_prw,
                    jpr.nm_perawatan,
                    jpr.total_byr AS harga,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 1 THEN 1 ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 2 THEN 1 ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 3 THEN 1 ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 4 THEN 1 ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 5 THEN 1 ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 6 THEN 1 ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 7 THEN 1 ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 8 THEN 1 ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 9 THEN 1 ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 10 THEN 1 ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 11 THEN 1 ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(pr.tgl_periksa) = 12 THEN 1 ELSE 0 END) AS `dec`,
                    COUNT(*) AS total_tindakan,
                    SUM(pr.biaya) AS total_pendapatan
                FROM periksa_radiologi pr
                INNER JOIN jns_perawatan_radiologi jpr ON jpr.kd_jenis_prw = pr.kd_jenis_prw
                LEFT JOIN reg_periksa reg ON reg.no_rawat = pr.no_rawat
                WHERE YEAR(pr.tgl_periksa) = ?
            ";

            if ($selectedStatus) {
                $sql .= " AND pr.status = ? ";
                $bindings[] = $selectedStatus;
            }

            if ($selectedPoli) {
                $sql .= " AND reg.kd_poli = ? ";
                $bindings[] = $selectedPoli;
            }

            if ($search !== '') {
                $sql .= " AND jpr.nm_perawatan LIKE ? ";
                $bindings[] = '%' . $search . '%';
            }

            $sql .= "
                GROUP BY jpr.kd_jenis_prw, jpr.nm_perawatan, jpr.total_byr
                ORDER BY jpr.nm_perawatan ASC
            ";

            $rows = DB::connection('simrs')->select($sql, $bindings);

            foreach ($rows as $row) {
                $radiologiData[] = [
                    'kode' => $row->kd_jenis_prw,
                    'nama' => $row->nm_perawatan,
                    'harga' => (float) $row->harga,
                    'jan' => (int) $row->jan,
                    'feb' => (int) $row->feb,
                    'mar' => (int) $row->mar,
                    'apr' => (int) $row->apr,
                    'may' => (int) $row->may,
                    'jun' => (int) $row->jun,
                    'jul' => (int) $row->jul,
                    'aug' => (int) $row->aug,
                    'sep' => (int) $row->sep,
                    'oct' => (int) $row->oct,
                    'nov' => (int) $row->nov,
                    'dec' => (int) $row->dec,
                    'total_tindakan' => (int) $row->total_tindakan,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];

                $grandTotals['jan'] += (int) $row->jan;
                $grandTotals['feb'] += (int) $row->feb;
                $grandTotals['mar'] += (int) $row->mar;
                $grandTotals['apr'] += (int) $row->apr;
                $grandTotals['may'] += (int) $row->may;
                $grandTotals['jun'] += (int) $row->jun;
                $grandTotals['jul'] += (int) $row->jul;
                $grandTotals['aug'] += (int) $row->aug;
                $grandTotals['sep'] += (int) $row->sep;
                $grandTotals['oct'] += (int) $row->oct;
                $grandTotals['nov'] += (int) $row->nov;
                $grandTotals['dec'] += (int) $row->dec;
                $grandTotals['total_tindakan'] += (int) $row->total_tindakan;
                $grandTotals['total_pendapatan'] += (float) $row->total_pendapatan;
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to load radiologi volume', [
                'year' => $selectedYear,
                'status' => $selectedStatus,
                'poli' => $selectedPoli,
                'search' => $search,
                'message' => $exception->getMessage(),
            ]);
            $errorMessage = 'Gagal memuat data radiologi dari SIMRS. Silakan coba lagi atau hubungi administrator.';
        }

        return [$radiologiData, $grandTotals, $errorMessage];
    }

    private function getOperasiAggregates(int $selectedYear, ?string $selectedStatus, ?string $selectedPoli, string $search): array
    {
        $operasiData = [];
        $errorMessage = null;
        $grandTotals = [
            'jan' => 0,
            'feb' => 0,
            'mar' => 0,
            'apr' => 0,
            'may' => 0,
            'jun' => 0,
            'jul' => 0,
            'aug' => 0,
            'sep' => 0,
            'oct' => 0,
            'nov' => 0,
            'dec' => 0,
            'total_tindakan' => 0,
            'total_pendapatan' => 0,
        ];

        try {
            $bindings = [$selectedYear];

            $tarifExpression = "
                COALESCE(po.operator1, 0) +
                COALESCE(po.operator2, 0) +
                COALESCE(po.operator3, 0) +
                COALESCE(po.asisten_operator1, 0) +
                COALESCE(po.asisten_operator2, 0) +
                COALESCE(po.asisten_operator3, 0) +
                COALESCE(po.instrumen, 0) +
                COALESCE(po.dokter_anak, 0) +
                COALESCE(po.dokter_anestesi, 0) +
                COALESCE(po.asisten_anestesi, 0) +
                COALESCE(po.asisten_anestesi2, 0) +
                COALESCE(po.perawat_luar, 0) +
                COALESCE(po.bagian_rs, 0) +
                COALESCE(po.omloop, 0) +
                COALESCE(po.omloop4, 0) +
                COALESCE(po.omloop5, 0)
            ";

            $biayaExpression = "
                COALESCE(o.biayaoperator1, 0) +
                COALESCE(o.biayaoperator2, 0) +
                COALESCE(o.biayaoperator3, 0) +
                COALESCE(o.biayaasisten_operator1, 0) +
                COALESCE(o.biayaasisten_operator2, 0) +
                COALESCE(o.biayaasisten_operator3, 0) +
                COALESCE(o.biayainstrumen, 0) +
                COALESCE(o.biayadokter_anak, 0) +
                COALESCE(o.biayaperawaat_resusitas, 0) +
                COALESCE(o.biayadokter_anestesi, 0) +
                COALESCE(o.biayaasisten_anestesi, 0) +
                COALESCE(o.biayaasisten_anestesi2, 0) +
                COALESCE(o.biayabidan, 0) +
                COALESCE(o.biayabidan2, 0) +
                COALESCE(o.biayabidan3, 0) +
                COALESCE(o.biayaperawat_luar, 0) +
                COALESCE(o.biayaalat, 0) +
                COALESCE(o.biayasewaok, 0) +
                COALESCE(o.akomodasi, 0) +
                COALESCE(o.bagian_rs, 0) +
                COALESCE(o.biaya_omloop, 0) +
                COALESCE(o.biaya_omloop2, 0) +
                COALESCE(o.biaya_omloop3, 0) +
                COALESCE(o.biaya_omloop4, 0) +
                COALESCE(o.biaya_omloop5, 0) +
                COALESCE(o.biayasarpras, 0) +
                COALESCE(o.biaya_dokter_pjanak, 0) +
                COALESCE(o.biaya_dokter_umum, 0)
            ";

            $sql = "
                SELECT
                    opu.kode_paket,
                    opu.nama_operasi,
                    opu.tarif_master AS harga,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 1 THEN opu.jumlah ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 2 THEN opu.jumlah ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 3 THEN opu.jumlah ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 4 THEN opu.jumlah ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 5 THEN opu.jumlah ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 6 THEN opu.jumlah ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 7 THEN opu.jumlah ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 8 THEN opu.jumlah ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 9 THEN opu.jumlah ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 10 THEN opu.jumlah ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 11 THEN opu.jumlah ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(opu.tgl_operasi) = 12 THEN opu.jumlah ELSE 0 END) AS `dec`,
                    SUM(opu.jumlah) AS total_tindakan,
                    SUM(opu.total_biaya) AS total_pendapatan
                FROM (
                    SELECT
                        o.kode_paket,
                        COALESCE(po.nm_perawatan, CONCAT('Paket ', o.kode_paket)) AS nama_operasi,
                        {$tarifExpression} AS tarif_master,
                        o.tgl_operasi,
                        o.status,
                        reg.kd_poli,
                        1 AS jumlah,
                        {$biayaExpression} AS total_biaya
                    FROM operasi o
                    LEFT JOIN paket_operasi po ON po.kode_paket = o.kode_paket
                    LEFT JOIN reg_periksa reg ON reg.no_rawat = o.no_rawat
                ) AS opu
                WHERE YEAR(opu.tgl_operasi) = ?
            ";

            if ($selectedStatus) {
                $sql .= " AND opu.status = ? ";
                $bindings[] = $selectedStatus;
            }

            if ($selectedPoli) {
                $sql .= " AND opu.kd_poli = ? ";
                $bindings[] = $selectedPoli;
            }

            if ($search !== '') {
                $sql .= " AND opu.nama_operasi LIKE ? ";
                $bindings[] = '%' . $search . '%';
            }

            $sql .= "
                GROUP BY opu.kode_paket, opu.nama_operasi, opu.tarif_master
                ORDER BY opu.nama_operasi ASC
            ";

            $rows = DB::connection('simrs')->select($sql, $bindings);

            foreach ($rows as $row) {
                $operasiData[] = [
                    'kode' => $row->kode_paket,
                    'nama' => $row->nama_operasi,
                    'harga' => (float) $row->harga,
                    'jan' => (int) $row->jan,
                    'feb' => (int) $row->feb,
                    'mar' => (int) $row->mar,
                    'apr' => (int) $row->apr,
                    'may' => (int) $row->may,
                    'jun' => (int) $row->jun,
                    'jul' => (int) $row->jul,
                    'aug' => (int) $row->aug,
                    'sep' => (int) $row->sep,
                    'oct' => (int) $row->oct,
                    'nov' => (int) $row->nov,
                    'dec' => (int) $row->dec,
                    'total_tindakan' => (int) $row->total_tindakan,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];

                $grandTotals['jan'] += (int) $row->jan;
                $grandTotals['feb'] += (int) $row->feb;
                $grandTotals['mar'] += (int) $row->mar;
                $grandTotals['apr'] += (int) $row->apr;
                $grandTotals['may'] += (int) $row->may;
                $grandTotals['jun'] += (int) $row->jun;
                $grandTotals['jul'] += (int) $row->jul;
                $grandTotals['aug'] += (int) $row->aug;
                $grandTotals['sep'] += (int) $row->sep;
                $grandTotals['oct'] += (int) $row->oct;
                $grandTotals['nov'] += (int) $row->nov;
                $grandTotals['dec'] += (int) $row->dec;
                $grandTotals['total_tindakan'] += (int) $row->total_tindakan;
                $grandTotals['total_pendapatan'] += (float) $row->total_pendapatan;
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to load operasi volume', [
                'year' => $selectedYear,
                'status' => $selectedStatus,
                'poli' => $selectedPoli,
                'search' => $search,
                'message' => $exception->getMessage(),
            ]);
            $errorMessage = 'Gagal memuat data operasi dari SIMRS. Silakan coba lagi atau hubungi administrator.';
        }

        return [$operasiData, $grandTotals, $errorMessage];
    }
    private function getTindakanRawatJalanAggregates(int $selectedYear, ?string $selectedPoli, string $search): array
    {
        $tindakanData = [];
        $errorMessage = null;
        $grandTotals = [
            'jan' => 0,
            'feb' => 0,
            'mar' => 0,
            'apr' => 0,
            'may' => 0,
            'jun' => 0,
            'jul' => 0,
            'aug' => 0,
            'sep' => 0,
            'oct' => 0,
            'nov' => 0,
            'dec' => 0,
            'total_tindakan' => 0,
            'total_pendapatan' => 0,
        ];

        try {
            $bindings = [$selectedYear];

            $sql = "
                SELECT
                    tu.kd_jenis_prw AS kode_tindakan,
                    tu.nm_perawatan AS nama_tindakan,
                    tu.tarif_master AS harga,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 1 THEN 1 ELSE 0 END) AS jan,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 2 THEN 1 ELSE 0 END) AS feb,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 3 THEN 1 ELSE 0 END) AS mar,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 4 THEN 1 ELSE 0 END) AS apr,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 5 THEN 1 ELSE 0 END) AS may,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 6 THEN 1 ELSE 0 END) AS jun,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 7 THEN 1 ELSE 0 END) AS jul,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 8 THEN 1 ELSE 0 END) AS aug,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 9 THEN 1 ELSE 0 END) AS sep,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 10 THEN 1 ELSE 0 END) AS oct,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 11 THEN 1 ELSE 0 END) AS nov,
                    SUM(CASE WHEN MONTH(tu.tgl_perawatan) = 12 THEN 1 ELSE 0 END) AS `dec`,
                    SUM(tu.jumlah) AS total_tindakan,
                    SUM(tu.biaya_rawat) AS total_pendapatan
                FROM (
                    SELECT
                        rjd.no_rawat,
                        rjd.tgl_perawatan,
                        rjd.kd_jenis_prw,
                        jp.nm_perawatan,
                        jp.total_byrdrpr AS tarif_master,
                        1 AS jumlah,
                        rjd.biaya_rawat
                    FROM rawat_jl_dr rjd
                    INNER JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjd.kd_jenis_prw

                    UNION ALL

                    SELECT
                        rjp.no_rawat,
                        rjp.tgl_perawatan,
                        rjp.kd_jenis_prw,
                        jp.nm_perawatan,
                        jp.total_byrdrpr AS tarif_master,
                        1 AS jumlah,
                        rjp.biaya_rawat
                    FROM rawat_jl_pr rjp
                    INNER JOIN jns_perawatan jp ON jp.kd_jenis_prw = rjp.kd_jenis_prw
                ) AS tu
                INNER JOIN reg_periksa reg ON reg.no_rawat = tu.no_rawat
                LEFT JOIN poliklinik p ON p.kd_poli = reg.kd_poli
                WHERE YEAR(tu.tgl_perawatan) = ?
            ";

            if ($selectedPoli) {
                $sql .= " AND reg.kd_poli = ? ";
                $bindings[] = $selectedPoli;
            }

            if ($search !== '') {
                $sql .= " AND tu.nm_perawatan LIKE ? ";
                $bindings[] = '%' . $search . '%';
            }

            $sql .= "
                GROUP BY tu.kd_jenis_prw, tu.nm_perawatan, tu.tarif_master
                ORDER BY tu.nm_perawatan ASC
            ";

            $rows = DB::connection('simrs')->select($sql, $bindings);

            foreach ($rows as $row) {
                $tindakanData[] = [
                    'kode' => $row->kode_tindakan,
                    'nama' => $row->nama_tindakan,
                    'harga' => (float) $row->harga,
                    'jan' => (int) $row->jan,
                    'feb' => (int) $row->feb,
                    'mar' => (int) $row->mar,
                    'apr' => (int) $row->apr,
                    'may' => (int) $row->may,
                    'jun' => (int) $row->jun,
                    'jul' => (int) $row->jul,
                    'aug' => (int) $row->aug,
                    'sep' => (int) $row->sep,
                    'oct' => (int) $row->oct,
                    'nov' => (int) $row->nov,
                    'dec' => (int) $row->dec,
                    'total_tindakan' => (int) $row->total_tindakan,
                    'total_pendapatan' => (float) $row->total_pendapatan,
                ];

                $grandTotals['jan'] += (int) $row->jan;
                $grandTotals['feb'] += (int) $row->feb;
                $grandTotals['mar'] += (int) $row->mar;
                $grandTotals['apr'] += (int) $row->apr;
                $grandTotals['may'] += (int) $row->may;
                $grandTotals['jun'] += (int) $row->jun;
                $grandTotals['jul'] += (int) $row->jul;
                $grandTotals['aug'] += (int) $row->aug;
                $grandTotals['sep'] += (int) $row->sep;
                $grandTotals['oct'] += (int) $row->oct;
                $grandTotals['nov'] += (int) $row->nov;
                $grandTotals['dec'] += (int) $row->dec;
                $grandTotals['total_tindakan'] += (int) $row->total_tindakan;
                $grandTotals['total_pendapatan'] += (float) $row->total_pendapatan;
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to load tindakan rawat jalan volume', [
                'year' => $selectedYear,
                'poli' => $selectedPoli,
                'search' => $search,
                'message' => $exception->getMessage(),
            ]);
            $errorMessage = 'Gagal memuat data dari SIMRS. Silakan coba lagi atau hubungi administrator.';
        }

        return [$tindakanData, $grandTotals, $errorMessage];
    }

    private function availableYears(int $currentYear, int $range = 5): array
    {
        $years = [];
        for ($i = 0; $i < $range; $i++) {
            $years[] = $currentYear - $i;
        }

        return $years;
    }
}

