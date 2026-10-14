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
        $search = trim((string) $request->input('search', ''));
        $search = $search !== '' ? $search : null;
        $limit = 1000;
        $offset = 0;
        $rows = [];

        do {
            $result = $this->simrsService->getMasterBarang($limit, $offset, $search);
            $batch = $result['data'] ?? [];
            array_push($rows, ...$batch);
            $offset += count($batch);
            $total = (int) ($result['total'] ?? 0);
        } while (count($batch) === $limit && $offset < $total);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Barang');
        $sheet->setCellValue('A1', 'MASTER BARANG SIMRS');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', $search ? 'Filter: '.$search : 'Semua barang aktif');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['Kode Barang', 'Nama Barang', 'Harga Beli Dasar (Rp)', 'Ralan (Rp)', 'Kelas 3 (Rp)', 'Isi', 'Expire', 'Status'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:H3')->getFont()->setBold(true);
        $sheet->getStyle('A3:H3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 4;
        foreach ($rows as $row) {
            $sheet->setCellValueExplicit('A'.$rowNumber, (string) $row->kode_brng, DataType::TYPE_STRING);
            $sheet->setCellValue('B'.$rowNumber, $row->nama_brng);
            $sheet->setCellValue('C'.$rowNumber, (float) $row->harga_beli_dasar);
            $sheet->setCellValue('D'.$rowNumber, (float) ($row->ralan ?? 0));
            $sheet->setCellValue('E'.$rowNumber, (float) ($row->kelas3 ?? 0));
            $sheet->setCellValue('F'.$rowNumber, (float) ($row->isi ?? 0));
            $sheet->setCellValue('G'.$rowNumber, $row->expire ?: '-');
            $sheet->setCellValue('H'.$rowNumber, (string) $row->status === '1' ? 'Aktif' : 'Tidak Aktif');
            $rowNumber++;
        }

        $lastRow = max(3, $rowNumber - 1);
        $sheet->getStyle('A3:H'.$lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('C4:F'.$lastRow)->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->freezePane('A4');
        $sheet->setAutoFilter('A3:H'.$lastRow);

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = 'master-barang-simrs-'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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
}
