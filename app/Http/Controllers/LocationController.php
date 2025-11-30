<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use App\Models\AllocationDriver;
use App\Models\DriverStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocationController extends Controller
{
    /**
     * Display a listing of locations with driver statistics.
     */
    public function index(Request $request)
    {
        $hospitalId = hospital('id');
        $periodMonth = (int) $request->get('period_month', date('n'));
        $periodYear = (int) $request->get('period_year', date('Y'));
        
        // Get allocation driver IDs
        $luasLantaiDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('name', 'Luas Lantai')
            ->first();
        
        $jumlahTempatTidurDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('name', 'Jumlah Tempat Tidur')
            ->first();
        
        $jumlahKamarDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('name', 'Jumlah Kamar')
            ->first();
        
        // Get cost centers with their driver statistics
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->with(['tariffClass'])
            ->orderBy('building_name')
            ->orderBy('name')
            ->get();
        
        // Get driver statistics for the selected period
        $driverStats = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $periodYear)
            ->where('period_month', $periodMonth)
            ->whereIn('allocation_driver_id', [
                $luasLantaiDriver?->id,
                $jumlahTempatTidurDriver?->id,
                $jumlahKamarDriver?->id
            ])
            ->get()
            ->groupBy('cost_center_id');
        
        // Build location data with pivot format
        $locations = [];
        $no = 1;
        
        foreach ($costCenters as $costCenter) {
            $stats = $driverStats->get($costCenter->id, collect());
            
            $luasLantai = $stats->firstWhere('allocation_driver_id', $luasLantaiDriver?->id)?->value ?? null;
            $jumlahTempatTidur = $stats->firstWhere('allocation_driver_id', $jumlahTempatTidurDriver?->id)?->value ?? null;
            $jumlahKamar = $stats->firstWhere('allocation_driver_id', $jumlahKamarDriver?->id)?->value ?? null;
            
            // Only show cost centers that have driver statistics for the selected period
            if ($luasLantai !== null || $jumlahTempatTidur !== null || $jumlahKamar !== null) {
                $locations[] = [
                    'no' => $no++,
                    'cost_center' => $costCenter,
                    'building_name' => $costCenter->building_name,
                    'floor' => $costCenter->floor,
                    'bagian' => $costCenter->name,
                    'luas_m2' => $luasLantai,
                    'kelas' => $costCenter->tariffClass?->name,
                    'jumlah_tempat_tidur' => $jumlahTempatTidur,
                    'jumlah_ruang_perawatan' => $jumlahKamar,
                ];
            }
        }
        
        // Get available periods for filter
        $availablePeriods = DriverStatistic::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
        
        return view('locations.index', compact('locations', 'periodMonth', 'periodYear', 'availablePeriods'));
    }

    /**
     * Export locations to Excel.
     */
    public function export(Request $request)
    {
        $hospitalId = hospital('id');
        $periodMonth = (int) $request->get('period_month', date('n'));
        $periodYear = (int) $request->get('period_year', date('Y'));
        
        // Get allocation driver IDs
        $luasLantaiDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('name', 'Luas Lantai')
            ->first();
        
        $jumlahTempatTidurDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('name', 'Jumlah Tempat Tidur')
            ->first();
        
        $jumlahKamarDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('name', 'Jumlah Kamar')
            ->first();
        
        // Get cost centers with their driver statistics
        $costCenters = CostCenter::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->with(['tariffClass'])
            ->orderBy('building_name')
            ->orderBy('name')
            ->get();
        
        // Get driver statistics for the selected period
        $driverStats = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $periodYear)
            ->where('period_month', $periodMonth)
            ->whereIn('allocation_driver_id', [
                $luasLantaiDriver?->id,
                $jumlahTempatTidurDriver?->id,
                $jumlahKamarDriver?->id
            ])
            ->get()
            ->groupBy('cost_center_id');
        
        // Build location data with pivot format
        $locations = [];
        $no = 1;
        
        foreach ($costCenters as $costCenter) {
            $stats = $driverStats->get($costCenter->id, collect());
            
            $luasLantai = $stats->firstWhere('allocation_driver_id', $luasLantaiDriver?->id)?->value ?? null;
            $jumlahTempatTidur = $stats->firstWhere('allocation_driver_id', $jumlahTempatTidurDriver?->id)?->value ?? null;
            $jumlahKamar = $stats->firstWhere('allocation_driver_id', $jumlahKamarDriver?->id)?->value ?? null;
            
            // Only show cost centers that have driver statistics for the selected period
            if ($luasLantai !== null || $jumlahTempatTidur !== null || $jumlahKamar !== null) {
                $locations[] = [
                    'no' => $no++,
                    'building_name' => $costCenter->building_name ?? '-',
                    'floor' => $costCenter->floor ?? '-',
                    'bagian' => $costCenter->name,
                    'luas_m2' => $luasLantai !== null ? (float) $luasLantai : null,
                    'kelas' => $costCenter->tariffClass?->name ?? '-',
                    'jumlah_tempat_tidur' => $jumlahTempatTidur !== null ? (float) $jumlahTempatTidur : null,
                    'jumlah_ruang_perawatan' => $jumlahKamar !== null ? (float) $jumlahKamar : null,
                ];
            }
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'LOKASI');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set period info
        $sheet->setCellValue('A2', 'Periode: ' . date('F Y', mktime(0, 0, 0, $periodMonth, 1, $periodYear)));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Headers
        $headers = ['No', 'Nama Gedung', 'Lantai', 'Bagian', 'Luas (m2)', 'Kelas', 'Jumlah Tempat Tidur per Ruangan', 'Jumlah Ruang Perawatan'];
        $sheet->fromArray($headers, null, 'A3');
        
        // Style headers
        $headerRange = 'A3:H3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Rows
        if (count($locations) > 0) {
            $rows = [];
            foreach ($locations as $location) {
                $rows[] = [
                    $location['no'],
                    $location['building_name'],
                    $location['floor'],
                    $location['bagian'],
                    $location['luas_m2'] !== null ? $location['luas_m2'] : '-',
                    $location['kelas'],
                    $location['jumlah_tempat_tidur'] !== null ? $location['jumlah_tempat_tidur'] : '-',
                    $location['jumlah_ruang_perawatan'] !== null ? $location['jumlah_ruang_perawatan'] : '-',
                ];
            }
            $sheet->fromArray($rows, null, 'A4');
            
            // Style borders
            $lastRow = 3 + count($locations);
            $range = 'A3:H' . $lastRow;
            $sheet->getStyle($range)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Align columns
            $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C4:C' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E4:E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G4:G' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H4:H' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }
        
        // Autosize columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set row height for headers
        $sheet->getRowDimension(3)->setRowHeight(30);
        
        $filename = 'location_' . hospital('id') . '_' . $periodYear . '_' . str_pad($periodMonth, 2, '0', STR_PAD_LEFT) . '_' . now()->format('Ymd_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
