<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\AllocationDriver;
use App\Models\CostCenter;
use App\Models\DriverStatistic;
use App\Models\Employee;
use App\Models\EmployeeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{
    use BlocksObserver;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $costCenterId = $request->get('cost_center_id');

        $query = Employee::where('hospital_id', hospital('id'))
            ->with(['assignments.costCenter', 'primaryAssignment.costCenter']);

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('employee_number', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($costCenterId) {
            $query->whereHas('assignments', function($q) use ($costCenterId) {
                $q->where('cost_center_id', $costCenterId)
                  ->active();
            });
        }

        // Get counts for tabs
        $baseQuery = Employee::where('hospital_id', hospital('id'));
        $statusCounts = [
            'all' => $baseQuery->count(),
            'active' => (clone $baseQuery)->where('status', Employee::STATUS_ACTIVE)->count(),
            'inactive' => (clone $baseQuery)->where('status', Employee::STATUS_INACTIVE)->count(),
            'resigned' => (clone $baseQuery)->where('status', Employee::STATUS_RESIGNED)->count(),
        ];

        $employees = $query->orderBy('name')->paginate(20)->appends($request->query());

        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees', 'search', 'status', 'costCenterId', 'statusCounts', 'costCenters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->blockObserver('membuat');

        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.form', [
            'employee' => null,
            'costCenters' => $costCenters,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->blockObserver('membuat');

        $validated = $request->validate([
            'employee_number' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'job_title' => 'nullable|string|max:100',
            'employment_type' => 'nullable|in:pns,pppk,tni,polri,bumn,contract,honorary,outsource',
            'education_level' => 'nullable|in:sd,smp,sma,d1,d2,d3,d4,s1,s2,s3,specialist',
            'professional_category' => 'nullable|in:doctor_specialist,doctor_general,nurse,midwife,health_analyst,pharmacist,nutritionist,radiographer,physiotherapist,admin,non_medical',
            'base_salary' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,resigned',
            'notes' => 'nullable|string',
            'assignments' => 'required|array|min:1',
            'assignments.*.cost_center_id' => 'required|exists:cost_centers,id',
            'assignments.*.fte_percentage' => 'required|numeric|min:1|max:100',
            'assignments.*.is_primary' => 'nullable|boolean',
        ]);

        // Convert FTE from percentage (1-100) to decimal (0.01-1.00)
        foreach ($validated['assignments'] as &$assignment) {
            $assignment['fte_percentage'] = $assignment['fte_percentage'] / 100;
        }

        // Check total FTE doesn't exceed 1.0
        $totalFte = collect($validated['assignments'])->sum('fte_percentage');
        if ($totalFte > 1.0) {
            return back()->withErrors(['assignments' => 'Total FTE tidak boleh melebihi 100%.'])->withInput();
        }

        // Check unique employee number per hospital
        $exists = Employee::where('hospital_id', hospital('id'))
            ->where('employee_number', $validated['employee_number'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['employee_number' => 'NIK/NIP sudah terdaftar.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'hospital_id' => hospital('id'),
                'employee_number' => $validated['employee_number'],
                'name' => $validated['name'],
                'job_title' => $validated['job_title'] ?? null,
                'employment_type' => $validated['employment_type'] ?? null,
                'education_level' => $validated['education_level'] ?? null,
                'professional_category' => $validated['professional_category'] ?? null,
                'base_salary' => $validated['base_salary'] ?? null,
                'allowances' => $validated['allowances'] ?? null,
                'join_date' => $validated['join_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);

            // Create assignments
            $hasPrimary = false;
            foreach ($validated['assignments'] as $index => $assignment) {
                $isPrimary = $assignment['is_primary'] ?? ($index === 0);
                if ($isPrimary) $hasPrimary = true;

                EmployeeAssignment::create([
                    'employee_id' => $employee->id,
                    'cost_center_id' => $assignment['cost_center_id'],
                    'fte_percentage' => $assignment['fte_percentage'],
                    'effective_date' => $validated['join_date'] ?? now(),
                    'is_primary' => $isPrimary,
                ]);
            }

            // Ensure at least one primary
            if (!$hasPrimary) {
                $employee->assignments()->first()->update(['is_primary' => true]);
            }

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Pegawai berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating employee: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        if ($employee->hospital_id !== hospital('id')) {
            abort(404);
        }

        $employee->load(['assignments.costCenter']);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $this->blockObserver('mengubah');

        if ($employee->hospital_id !== hospital('id')) {
            abort(404);
        }

        $employee->load(['assignments.costCenter']);

        $costCenters = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.form', [
            'employee' => $employee,
            'costCenters' => $costCenters,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $this->blockObserver('mengubah');

        if ($employee->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'employee_number' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'job_title' => 'nullable|string|max:100',
            'employment_type' => 'nullable|in:pns,pppk,tni,polri,bumn,contract,honorary,outsource',
            'education_level' => 'nullable|in:sd,smp,sma,d1,d2,d3,d4,s1,s2,s3,specialist',
            'professional_category' => 'nullable|in:doctor_specialist,doctor_general,nurse,midwife,health_analyst,pharmacist,nutritionist,radiographer,physiotherapist,admin,non_medical',
            'base_salary' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'resign_date' => 'nullable|date|after_or_equal:join_date',
            'status' => 'required|in:active,inactive,resigned',
            'notes' => 'nullable|string',
            'assignments' => 'required|array|min:1',
            'assignments.*.cost_center_id' => 'required|exists:cost_centers,id',
            'assignments.*.fte_percentage' => 'required|numeric|min:1|max:100',
            'assignments.*.is_primary' => 'nullable|boolean',
        ]);

        // Convert FTE from percentage (1-100) to decimal (0.01-1.00)
        foreach ($validated['assignments'] as &$assignment) {
            $assignment['fte_percentage'] = $assignment['fte_percentage'] / 100;
        }

        // Check total FTE doesn't exceed 1.0
        $totalFte = collect($validated['assignments'])->sum('fte_percentage');
        if ($totalFte > 1.0) {
            return back()->withErrors(['assignments' => 'Total FTE tidak boleh melebihi 100%.'])->withInput();
        }

        // Check unique employee number (excluding self)
        $exists = Employee::where('hospital_id', hospital('id'))
            ->where('employee_number', $validated['employee_number'])
            ->where('id', '!=', $employee->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['employee_number' => 'NIK/NIP sudah terdaftar.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $employee->update([
                'employee_number' => $validated['employee_number'],
                'name' => $validated['name'],
                'job_title' => $validated['job_title'] ?? null,
                'employment_type' => $validated['employment_type'] ?? null,
                'education_level' => $validated['education_level'] ?? null,
                'professional_category' => $validated['professional_category'] ?? null,
                'base_salary' => $validated['base_salary'] ?? null,
                'allowances' => $validated['allowances'] ?? null,
                'join_date' => $validated['join_date'],
                'resign_date' => $validated['resign_date'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);

            // Delete existing active assignments and recreate
            $employee->assignments()->whereNull('end_date')->delete();

            $hasPrimary = false;
            foreach ($validated['assignments'] as $index => $assignment) {
                $isPrimary = $assignment['is_primary'] ?? ($index === 0);
                if ($isPrimary) $hasPrimary = true;

                EmployeeAssignment::create([
                    'employee_id' => $employee->id,
                    'cost_center_id' => $assignment['cost_center_id'],
                    'fte_percentage' => $assignment['fte_percentage'],
                    'effective_date' => now(),
                    'is_primary' => $isPrimary,
                ]);
            }

            if (!$hasPrimary) {
                $employee->assignments()->whereNull('end_date')->first()->update(['is_primary' => true]);
            }

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Data pegawai berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating employee: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->blockObserver('menghapus');

        if ($employee->hospital_id !== hospital('id')) {
            abort(404);
        }

        try {
            $employee->delete();

            return redirect()->route('employees.index')
                ->with('success', 'Pegawai berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('employees.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pegawai.');
        }
    }

    /**
     * Export employees to Excel.
     */
    public function export(Request $request)
    {
        $query = Employee::where('hospital_id', hospital('id'))
            ->with(['assignments.costCenter'])
            ->orderBy('name');

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'NIK/NIP', 'Nama', 'Jabatan', 'Tipe Kepegawaian', 'Pendidikan', 
            'Kategori', 'Gaji Pokok', 'Tunjangan', 'Tgl Masuk', 'Tgl Keluar', 
            'Status', 'Penempatan', 'Total FTE'
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        // Rows
        $row = 2;
        foreach ($data as $employee) {
            $assignments = $employee->assignments->map(function($a) {
                return $a->costCenter->name . ' (' . number_format($a->fte_percentage * 100, 0) . '%)';
            })->implode(', ');

            $sheet->setCellValue('A' . $row, $employee->employee_number);
            $sheet->setCellValue('B' . $row, $employee->name);
            $sheet->setCellValue('C' . $row, $employee->job_title ?? '-');
            $sheet->setCellValue('D' . $row, $employee->employment_type_label ?? '-');
            $sheet->setCellValue('E' . $row, $employee->education_level_label ?? '-');
            $sheet->setCellValue('F' . $row, $employee->professional_category_label ?? '-');
            $sheet->setCellValue('G' . $row, $employee->base_salary ?? 0);
            $sheet->setCellValue('H' . $row, $employee->allowances ?? 0);
            $sheet->setCellValue('I' . $row, $employee->join_date?->format('Y-m-d') ?? '-');
            $sheet->setCellValue('J' . $row, $employee->resign_date?->format('Y-m-d') ?? '-');
            $sheet->setCellValue('K' . $row, $employee->status_label);
            $sheet->setCellValue('L' . $row, $assignments ?: '-');
            $sheet->setCellValue('M' . $row, number_format($employee->total_fte * 100, 0) . '%');
            $row++;
        }

        // Format salary columns as number
        $sheet->getStyle('G2:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'employees_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Download template for importing employees.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Extended headers including new fields
        $headers = [
            'NIK_NIP', 'Nama', 'Jabatan', 'Tipe_Kepegawaian', 'Pendidikan',
            'Kategori_Profesi', 'Gaji_Pokok', 'Tunjangan', 'Tanggal_Masuk',
            'Status', 'Cost_Center_Code', 'FTE_Percentage', 'Is_Primary'
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        // Sample data - Row 2: Complete example
        $sheet->setCellValue('A2', 'P001');
        $sheet->setCellValue('B2', 'Dr. Ahmad Specialist');
        $sheet->setCellValue('C2', 'Dokter Spesialis Penyakit Dalam');
        $sheet->setCellValue('D2', 'pns');
        $sheet->setCellValue('E2', 'specialist');
        $sheet->setCellValue('F2', 'doctor_specialist');
        $sheet->setCellValue('G2', '6000000');
        $sheet->setCellValue('H2', '10000000');
        $sheet->setCellValue('I2', '2023-01-15');
        $sheet->setCellValue('J2', 'active');
        $sheet->setCellValue('K2', 'IGD');
        $sheet->setCellValue('L2', '1.00');
        $sheet->setCellValue('M2', 'Yes');

        // Row 3: Nurse with split assignment
        $sheet->setCellValue('A3', 'P002');
        $sheet->setCellValue('B3', 'Ns. Budi, S.Kep');
        $sheet->setCellValue('C3', 'Perawat Pelaksana');
        $sheet->setCellValue('D3', 'contract');
        $sheet->setCellValue('E3', 's1');
        $sheet->setCellValue('F3', 'nurse');
        $sheet->setCellValue('G3', '3000000');
        $sheet->setCellValue('H3', '2000000');
        $sheet->setCellValue('I3', '2023-03-01');
        $sheet->setCellValue('J3', 'active');
        $sheet->setCellValue('K3', 'RAWAT-INAP');
        $sheet->setCellValue('L3', '0.50');
        $sheet->setCellValue('M3', 'Yes');

        // Row 4: Same nurse second assignment
        $sheet->setCellValue('A4', 'P002');
        $sheet->setCellValue('B4', 'Ns. Budi, S.Kep');
        $sheet->setCellValue('C4', 'Perawat Pelaksana');
        $sheet->setCellValue('D4', 'contract');
        $sheet->setCellValue('E4', 's1');
        $sheet->setCellValue('F4', 'nurse');
        $sheet->setCellValue('G4', '3000000');
        $sheet->setCellValue('H4', '2000000');
        $sheet->setCellValue('I4', '2023-03-01');
        $sheet->setCellValue('J4', 'active');
        $sheet->setCellValue('K4', 'IGD');
        $sheet->setCellValue('L4', '0.50');
        $sheet->setCellValue('M4', 'No');

        // Instructions
        $sheet->setCellValue('O1', 'PETUNJUK PENGISIAN:');
        $sheet->getStyle('O1')->getFont()->setBold(true);
        
        $instructions = [
            'A. KOLOM WAJIB:',
            '- NIK_NIP: Nomor induk pegawai (wajib & unik)',
            '- Nama: Nama lengkap (wajib)',
            '',
            'B. KOLOM OPSIONAL:',
            '- Jabatan: Free text (Dokter, Perawat, dll)',
            '- Tipe_Kepegawaian: pns, pppk, tni, polri, bumn,',
            '  contract, honorary, outsource',
            '- Pendidikan: sd, smp, sma, d1, d2, d3, d4,',
            '  s1, s2, s3, specialist',
            '- Kategori_Profesi: doctor_specialist, doctor_general,',
            '  nurse, midwife, health_analyst, pharmacist,',
            '  nutritionist, radiographer, physiotherapist,',
            '  admin, non_medical',
            '- Gaji_Pokok: Angka (tanpa titik/koma)',
            '- Tunjangan: Angka (tanpa titik/koma)',
            '- Tanggal_Masuk: Format YYYY-MM-DD',
            '',
            'C. PENEMPATAN:',
            '- Status: active / inactive / resigned',
            '- Cost_Center_Code: Kode cost center (sesuai master)',
            '- FTE_Percentage: 0.01 - 1.00 (1 = 100%)',
            '- Is_Primary: Yes / No',
            '',
            'D. CATATAN PENTING:',
            '- Untuk split assignment, buat beberapa baris',
            '  dengan NIK sama tapi Cost Center berbeda',
            '- Total FTE per pegawai tidak boleh > 100%',
            '- Pastikan Cost Center Code sudah ada di master',
        ];
        
        foreach ($instructions as $index => $text) {
            $sheet->setCellValue('O' . ($index + 2), $text);
        }

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'employee_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import employees from Excel.
     */
    public function import(Request $request)
    {
        $this->blockObserver('membuat');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header
            array_shift($rows);

            $successCount = 0;
            $updatedCount = 0;
            $errors = [];

            // Get cost centers for lookup
            $costCenters = CostCenter::where('hospital_id', hospital('id'))->get()->keyBy('code');

            // Column mapping for new template (0-indexed)
            // A=0:NIK, B=1:Nama, C=2:Jabatan, D=3:Tipe, E=4:Pendidikan, F=5:Kategori,
            // G=6:GajiPokok, H=7:Tunjangan, I=8:TglMasuk, J=9:Status, 
            // K=10:CostCenterCode, L=11:FTE, M=12:IsPrimary

            // Group rows by employee number
            $employeeGroups = [];
            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }
                $nik = trim($row[0]);
                if (!isset($employeeGroups[$nik])) {
                    $employeeGroups[$nik] = [];
                }
                $employeeGroups[$nik][] = ['row' => $row, 'index' => $index];
            }

            DB::beginTransaction();

            foreach ($employeeGroups as $nik => $group) {
                try {
                    $firstRow = $group[0]['row'];

                    // Check/create employee
                    $employee = Employee::where('hospital_id', hospital('id'))
                        ->where('employee_number', $nik)
                        ->first();

                    $employeeData = [
                        'hospital_id' => hospital('id'),
                        'employee_number' => $nik,
                        'name' => trim($firstRow[1]),
                        'job_title' => !empty($firstRow[2]) ? trim($firstRow[2]) : null,
                        'employment_type' => !empty($firstRow[3]) ? strtolower(trim($firstRow[3])) : null,
                        'education_level' => !empty($firstRow[4]) ? strtolower(trim($firstRow[4])) : null,
                        'professional_category' => !empty($firstRow[5]) ? strtolower(trim($firstRow[5])) : null,
                        'base_salary' => !empty($firstRow[6]) ? floatval($firstRow[6]) : null,
                        'allowances' => !empty($firstRow[7]) ? floatval($firstRow[7]) : null,
                        'join_date' => !empty($firstRow[8]) ? $firstRow[8] : null,
                        'status' => !empty($firstRow[9]) ? strtolower(trim($firstRow[9])) : 'active',
                    ];

                    if ($employee) {
                        $employee->update($employeeData);
                        // Delete existing assignments
                        $employee->assignments()->delete();
                        $updatedCount++;
                    } else {
                        $employee = Employee::create($employeeData);
                        $successCount++;
                    }

                    // Create assignments
                    $totalFte = 0;
                    foreach ($group as $item) {
                        $row = $item['row'];
                        $ccCode = trim($row[10] ?? '');
                        $ftePercentage = floatval($row[11] ?? 1);
                        $isPrimary = strtolower(trim($row[12] ?? 'no')) === 'yes';

                        if (empty($ccCode) || !isset($costCenters[$ccCode])) {
                            continue;
                        }

                        $totalFte += $ftePercentage;

                        EmployeeAssignment::create([
                            'employee_id' => $employee->id,
                            'cost_center_id' => $costCenters[$ccCode]->id,
                            'fte_percentage' => $ftePercentage,
                            'effective_date' => $employee->join_date ?? now(),
                            'is_primary' => $isPrimary,
                        ]);
                    }

                    // Validate total FTE
                    if ($totalFte > 1.0) {
                        $errors[] = "NIK {$nik}: Total FTE melebihi 100%";
                    }

                } catch (\Exception $e) {
                    $errors[] = "NIK {$nik}: " . $e->getMessage();
                }
            }

            DB::commit();

            if (count($errors) > 0) {
                return redirect()->route('employees.index')
                    ->with('warning', "Import selesai. {$successCount} data baru, {$updatedCount} diupdate. " . count($errors) . " peringatan.");
            }

            return redirect()->route('employees.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} diupdate.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('employees.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Generate FTE to Driver Statistics.
     */
    public function generateFte(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');
        $hospitalId = hospital('id');

        // Find FTE driver
        $fteDriver = AllocationDriver::where('hospital_id', $hospitalId)
            ->where(function($q) {
                $q->where('name', 'LIKE', '%FTE%')
                  ->orWhere('name', 'LIKE', '%Karyawan%')
                  ->orWhere('name', 'LIKE', '%Pegawai%');
            })
            ->first();

        if (!$fteDriver) {
            return back()->with('error', 'Driver FTE/Jumlah Karyawan tidak ditemukan. Buat terlebih dahulu di Allocation Drivers.');
        }

        // Get FTE per cost center
        $fteData = EmployeeAssignment::getFtePerCostCenter($hospitalId, $month, $year);

        if ($fteData->isEmpty()) {
            return back()->with('warning', 'Tidak ada data pegawai aktif untuk periode ini.');
        }

        $created = 0;
        $updated = 0;

        foreach ($fteData as $item) {
            $existing = DriverStatistic::where('hospital_id', $hospitalId)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->where('cost_center_id', $item->cost_center_id)
                ->where('allocation_driver_id', $fteDriver->id)
                ->first();

            if ($existing) {
                $existing->update(['value' => $item->total_fte]);
                $updated++;
            } else {
                DriverStatistic::create([
                    'hospital_id' => $hospitalId,
                    'period_month' => $month,
                    'period_year' => $year,
                    'cost_center_id' => $item->cost_center_id,
                    'allocation_driver_id' => $fteDriver->id,
                    'value' => $item->total_fte,
                ]);
                $created++;
            }
        }

        return back()->with('success', "FTE berhasil di-generate ke Driver Statistics. {$created} data baru, {$updated} diupdate.");
    }

    /**
     * Show FTE summary page.
     */
    public function fteSummary(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $fteData = EmployeeAssignment::getFtePerCostCenter(hospital('id'), $month, $year);

        return view('employees.fte-summary', compact('fteData', 'month', 'year'));
    }
}
