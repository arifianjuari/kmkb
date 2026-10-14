<?php

namespace App\Http\Controllers;

use App\Services\SimrsService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SimrsController extends Controller
{
    public function __construct(private readonly SimrsService $simrsService)
    {
    }

    /**
     * Display the master barang view
     *
     * @return \Illuminate\View\View
     */
    public function masterBarang()
    {
        return view('simrs.master-barang');
    }

    /**
     * Export all active master barang matching the current search to Excel.
     */
    public function exportMasterBarang(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getMasterBarang($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'MASTER BARANG SIMRS',
            sheetTitle: 'Master Barang',
            search: $search,
            allRecordsLabel: 'Semua barang aktif',
            headers: ['Kode Barang', 'Nama Barang', 'Harga Beli Dasar (Rp)', 'Ralan (Rp)', 'Kelas 3 (Rp)', 'Isi', 'Expire', 'Status'],
            rows: $rows,
            mapRow: fn ($row): array => [
                (string) $row->kode_brng,
                $row->nama_brng,
                (float) $row->harga_beli_dasar,
                (float) ($row->ralan ?? 0),
                (float) ($row->kelas3 ?? 0),
                (float) ($row->isi ?? 0),
                $row->expire ?: '-',
                (string) $row->status === '1' ? 'Aktif' : 'Tidak Aktif',
            ],
            filePrefix: 'master-barang-simrs',
            stringColumnIndexes: [0],
            moneyColumnFrom: 'C',
            moneyColumnTo: 'F',
        );
    }

    public function exportTindakanRawatJalan(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getTindakanRawatJalan($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'TINDAKAN RAWAT JALAN SIMRS',
            sheetTitle: 'Tindakan Ralan',
            search: $search,
            allRecordsLabel: 'Semua tindakan rawat jalan',
            headers: ['Kode', 'Nama Tindakan', 'Harga (Rp)'],
            rows: $rows,
            mapRow: fn ($row): array => [
                (string) $row->kd_jenis_prw,
                $row->nm_perawatan,
                (float) ($row->total_byrdrpr ?? 0),
            ],
            filePrefix: 'tindakan-rawat-jalan-simrs',
            stringColumnIndexes: [0],
            moneyColumnFrom: 'C',
            moneyColumnTo: 'C',
        );
    }

    public function exportTindakanRawatInap(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getTindakanRawatInap($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'TINDAKAN RAWAT INAP SIMRS',
            sheetTitle: 'Tindakan Ranap',
            search: $search,
            allRecordsLabel: 'Semua tindakan rawat inap',
            headers: ['Kode', 'Nama Tindakan', 'Harga (Rp)'],
            rows: $rows,
            mapRow: fn ($row): array => [
                (string) $row->kd_jenis_prw,
                $row->nm_perawatan,
                (float) ($row->total_byrdrpr ?? 0),
            ],
            filePrefix: 'tindakan-rawat-inap-simrs',
            stringColumnIndexes: [0],
            moneyColumnFrom: 'C',
            moneyColumnTo: 'C',
        );
    }

    public function exportLaboratorium(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getLaboratorium($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'LABORATORIUM SIMRS',
            sheetTitle: 'Laboratorium',
            search: $search,
            allRecordsLabel: 'Semua pemeriksaan laboratorium',
            headers: ['Kode', 'Nama Pemeriksaan', 'Total Biaya (Rp)'],
            rows: $rows,
            mapRow: fn ($row): array => [
                (string) $row->kd_jenis_prw,
                $row->nm_perawatan,
                (float) ($row->total_byr ?? 0),
            ],
            filePrefix: 'laboratorium-simrs',
            stringColumnIndexes: [0],
            moneyColumnFrom: 'C',
            moneyColumnTo: 'C',
        );
    }

    public function exportRadiologi(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getRadiologi($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'RADIOLOGI SIMRS',
            sheetTitle: 'Radiologi',
            search: $search,
            allRecordsLabel: 'Semua pemeriksaan radiologi',
            headers: ['Kode', 'Nama Pemeriksaan', 'BHP (Rp)', 'Dokter (Rp)', 'Petugas (Rp)', 'KSO (Rp)', 'Manajemen (Rp)', 'Total (Rp)'],
            rows: $rows,
            mapRow: function ($row): array {
                $dokter = (float) ($row->tarif_tindakan_dokter ?? 0);
                $petugas = (float) ($row->tarif_tindakan_petugas ?? 0);
                $kso = (float) ($row->kso ?? 0);
                $menejemen = (float) ($row->menejemen ?? 0);
                $total = ($row->total_byr !== null && $row->total_byr !== '')
                    ? (float) $row->total_byr
                    : ($dokter + $petugas + $kso + $menejemen);

                return [
                    (string) $row->kd_jenis_prw,
                    $row->nm_perawatan,
                    (float) ($row->bhp ?? 0),
                    $dokter,
                    $petugas,
                    $kso,
                    $menejemen,
                    $total,
                ];
            },
            filePrefix: 'radiologi-simrs',
            stringColumnIndexes: [0],
            moneyColumnFrom: 'C',
            moneyColumnTo: 'H',
        );
    }

    public function exportOperasi(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getOperasi($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'OPERASI SIMRS',
            sheetTitle: 'Operasi',
            search: $search,
            allRecordsLabel: 'Semua paket operasi',
            headers: [
                'Nama Perawatan',
                'Total (Rp)',
                'Operator 1 (Rp)',
                'Asisten Operator 1 (Rp)',
                'Asisten Operator 2 (Rp)',
                'Asisten Operator 3 (Rp)',
                'Instrumen (Rp)',
                'Dokter Anak (Rp)',
                'Dokter Anestesi (Rp)',
                'Asisten Anestesi (Rp)',
                'Asisten Anestesi 2 (Rp)',
                'Perawat Luar (Rp)',
                'Bagian RS (Rp)',
                'Omloop (Rp)',
                'Omloop 4 (Rp)',
                'Omloop 5 (Rp)',
                'Kode Paket',
            ],
            rows: $rows,
            mapRow: fn ($row): array => [
                $row->nm_perawatan,
                (float) ($row->tarif_total ?? 0),
                (float) ($row->operator1 ?? 0),
                (float) ($row->asisten_operator1 ?? 0),
                (float) ($row->asisten_operator2 ?? 0),
                (float) ($row->asisten_operator3 ?? 0),
                (float) ($row->instrumen ?? 0),
                (float) ($row->dokter_anak ?? 0),
                (float) ($row->dokter_anestesi ?? 0),
                (float) ($row->asisten_anestesi ?? 0),
                (float) ($row->asisten_anestesi2 ?? 0),
                (float) ($row->perawat_luar ?? 0),
                (float) ($row->bagian_rs ?? 0),
                (float) ($row->omloop ?? 0),
                (float) ($row->omloop4 ?? 0),
                (float) ($row->omloop5 ?? 0),
                (string) ($row->kode_paket ?? ''),
            ],
            filePrefix: 'operasi-simrs',
            stringColumnIndexes: [16],
            moneyColumnFrom: 'B',
            moneyColumnTo: 'P',
        );
    }

    public function exportKamar(Request $request)
    {
        $search = $this->searchQuery($request);
        $rows = $this->fetchAllSimrsRows(
            fn (int $limit, int $offset, ?string $search) => $this->simrsService->getKamar($limit, $offset, $search),
            $search
        );

        return $this->streamSimrsExcelExport(
            title: 'KAMAR SIMRS',
            sheetTitle: 'Kamar',
            search: $search,
            allRecordsLabel: 'Semua data kamar',
            headers: ['Kode Kamar', 'Kode Bangsal', 'Tarif Kamar (Rp)', 'Kelas'],
            rows: $rows,
            mapRow: fn ($row): array => [
                (string) $row->kd_kamar,
                (string) $row->kd_bangsal,
                (float) ($row->trf_kamar ?? 0),
                $row->kelas ?? '',
            ],
            filePrefix: 'kamar-simrs',
            stringColumnIndexes: [0, 1],
            moneyColumnFrom: 'C',
            moneyColumnTo: 'C',
        );
    }
    
    /**
     * Display the tindakan rawat jalan view
     *
     * @return \Illuminate\View\View
     */
    public function tindakanRawatJalanView()
    {
        return view('simrs.tindakan-rawat-jalan');
    }
    
    /**
     * Display the tindakan rawat inap view
     *
     * @return \Illuminate\View\View
     */
    public function tindakanRawatInapView()
    {
        return view('simrs.tindakan-rawat-inap');
    }
    
    /**
     * Display the laboratorium view
     *
     * @return \Illuminate\View\View
     */
    public function laboratorium()
    {
        return view('simrs.laboratorium');
    }
    
    /**
     * Display the radiologi view
     *
     * @return \Illuminate\View\View
     */
    public function radiologi()
    {
        return view('simrs.radiologi');
    }
    
    /**
     * Display the operasi view
     *
     * @return \Illuminate\View\View
     */
    public function operasi()
    {
        return view('simrs.operasi');
    }
    
    /**
     * Display the kamar view
     *
     * @return \Illuminate\View\View
     */
    public function kamarView()
    {
        return view('simrs.kamar');
    }

    private function searchQuery(Request $request): ?string
    {
        $search = trim((string) $request->input('search', ''));

        return $search !== '' ? $search : null;
    }

    /**
     * @param  callable(int, int, ?string): array{data?: array, total?: int}  $fetchPage
     * @return array<int, object>
     */
    private function fetchAllSimrsRows(callable $fetchPage, ?string $search): array
    {
        $limit = 1000;
        $offset = 0;
        $rows = [];

        do {
            $result = $fetchPage($limit, $offset, $search);
            $batch = $result['data'] ?? [];
            array_push($rows, ...$batch);
            $offset += count($batch);
            $total = (int) ($result['total'] ?? 0);
        } while (count($batch) === $limit && $offset < $total);

        return $rows;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, object>  $rows
     * @param  callable(object): array<int, mixed>  $mapRow
     * @param  array<int, int>  $stringColumnIndexes
     */
    private function streamSimrsExcelExport(
        string $title,
        string $sheetTitle,
        ?string $search,
        string $allRecordsLabel,
        array $headers,
        array $rows,
        callable $mapRow,
        string $filePrefix,
        array $stringColumnIndexes = [],
        ?string $moneyColumnFrom = null,
        ?string $moneyColumnTo = null,
    ) {
        $lastColumn = chr(ord('A') + count($headers) - 1);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:'.$lastColumn.'1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', $search ? 'Filter: '.$search : $allRecordsLabel);
        $sheet->mergeCells('A2:'.$lastColumn.'2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:'.$lastColumn.'3')->getFont()->setBold(true);
        $sheet->getStyle('A3:'.$lastColumn.'3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle('A3:'.$lastColumn.'3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 4;
        foreach ($rows as $row) {
            $values = $mapRow($row);
            $columnIndex = 0;
            foreach ($values as $value) {
                $cell = chr(ord('A') + $columnIndex).$rowNumber;
                if (in_array($columnIndex, $stringColumnIndexes, true)) {
                    $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
                } elseif (is_int($value) || is_float($value)) {
                    $sheet->setCellValue($cell, $value);
                } else {
                    $sheet->setCellValue($cell, $value);
                }
                $columnIndex++;
            }
            $rowNumber++;
        }

        $lastRow = max(3, $rowNumber - 1);
        $sheet->getStyle('A3:'.$lastColumn.$lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        if ($moneyColumnFrom && $moneyColumnTo && $lastRow >= 4) {
            $sheet->getStyle($moneyColumnFrom.'4:'.$moneyColumnTo.$lastRow)->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $sheet->freezePane('A4');
        $sheet->setAutoFilter('A3:'.$lastColumn.$lastRow);

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = $filePrefix.'-'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
