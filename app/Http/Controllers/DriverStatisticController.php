<?php

namespace App\Http\Controllers;

use App\Models\DriverStatistic;
use App\Models\CostCenter;
use App\Models\AllocationDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DriverStatisticController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $periodMonth = $request->get('period_month');
        $periodYear = $request->get('period_year', date('Y'));
        $costCenterId = $request->get('cost_center_id');
        $allocationDriverId = $request->get('allocation_driver_id');
        $isStatic = $request->get('is_static');
        
        $baseQuery = DriverStatistic::where('hospital_id', hospital('id'))
            ->with(['costCenter', 'allocationDriver']);
        
        // Build query for filtering
        $query = clone $baseQuery;
        
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        
        if ($periodYear) {
            $query->where('period_year', $periodYear);
        }
        
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        if ($allocationDriverId) {
            $query->where('allocation_driver_id', $allocationDriverId);
        }
        
        if ($search) {
            $query->whereHas('costCenter', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            })->orWhereHas('allocationDriver', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by static/dynamic
        if ($isStatic !== null && $isStatic !== '') {
            $query->whereHas('allocationDriver', function($q) use ($isStatic) {
                $q->where('is_static', $isStatic);
            });
        }
        
        $driverStatistics = $query->latest()->paginate(20)->appends($request->query());
        
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $allocationDrivers = AllocationDriver::where('hospital_id', hospital('id'))->orderBy('name')->get();
        
        // Calculate counts for static type tabs (considering other filters but not is_static)
        $staticCountQuery = clone $baseQuery;
        if ($periodMonth) {
            $staticCountQuery->where('period_month', $periodMonth);
        }
        if ($periodYear) {
            $staticCountQuery->where('period_year', $periodYear);
        }
        if ($costCenterId) {
            $staticCountQuery->where('cost_center_id', $costCenterId);
        }
        if ($allocationDriverId) {
            $staticCountQuery->where('allocation_driver_id', $allocationDriverId);
        }
        if ($search) {
            $staticCountQuery->whereHas('costCenter', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            })->orWhereHas('allocationDriver', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        $staticTypeCounts = [
            'all' => $staticCountQuery->count(),
            'static' => (clone $staticCountQuery)->whereHas('allocationDriver', function($q) {
                $q->where('is_static', true);
            })->count(),
            'dynamic' => (clone $staticCountQuery)->whereHas('allocationDriver', function($q) {
                $q->where('is_static', false);
            })->count(),
        ];
        
        return view('driver-statistics.index', compact('driverStatistics', 'search', 'periodMonth', 'periodYear', 'costCenterId', 'allocationDriverId', 'isStatic', 'costCenters', 'allocationDrivers', 'staticTypeCounts'));
    }

    public function create()
    {
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $allocationDrivers = AllocationDriver::where('hospital_id', hospital('id'))->orderBy('name')->get();
        
        return view('driver-statistics.create', compact('costCenters', 'allocationDrivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'allocation_driver_id' => 'required|exists:allocation_drivers,id',
            'value' => 'required|numeric|min:0',
        ]);

        // Ensure cost center and allocation driver belong to same hospital
        $costCenter = CostCenter::where('id', $validated['cost_center_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$costCenter) {
            return back()->withErrors(['cost_center_id' => 'Cost center tidak valid.'])->withInput();
        }

        $allocationDriver = AllocationDriver::where('id', $validated['allocation_driver_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$allocationDriver) {
            return back()->withErrors(['allocation_driver_id' => 'Allocation driver tidak valid.'])->withInput();
        }

        DriverStatistic::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
        ]));

        return redirect()->route('driver-statistics.index')
            ->with('success', 'Driver statistic berhasil dibuat.');
    }

    public function show(DriverStatistic $driverStatistic)
    {
        if ($driverStatistic->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $driverStatistic->load(['costCenter', 'allocationDriver']);
        
        return view('driver-statistics.show', compact('driverStatistic'));
    }

    public function edit(DriverStatistic $driverStatistic)
    {
        if ($driverStatistic->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $allocationDrivers = AllocationDriver::where('hospital_id', hospital('id'))->orderBy('name')->get();
        
        return view('driver-statistics.edit', compact('driverStatistic', 'costCenters', 'allocationDrivers'));
    }

    public function update(Request $request, DriverStatistic $driverStatistic)
    {
        if ($driverStatistic->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'allocation_driver_id' => 'required|exists:allocation_drivers,id',
            'value' => 'required|numeric|min:0',
        ]);

        // Ensure cost center and allocation driver belong to same hospital
        $costCenter = CostCenter::where('id', $validated['cost_center_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$costCenter) {
            return back()->withErrors(['cost_center_id' => 'Cost center tidak valid.'])->withInput();
        }

        $allocationDriver = AllocationDriver::where('id', $validated['allocation_driver_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$allocationDriver) {
            return back()->withErrors(['allocation_driver_id' => 'Allocation driver tidak valid.'])->withInput();
        }

        $driverStatistic->update($validated);

        return redirect()->route('driver-statistics.index')
            ->with('success', 'Driver statistic berhasil diperbarui.');
    }

    public function destroy(DriverStatistic $driverStatistic)
    {
        if ($driverStatistic->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $driverStatistic->delete();

        return redirect()->route('driver-statistics.index')
            ->with('success', 'Driver statistic berhasil dihapus.');
    }

    public function bulkInputForm()
    {
        $costCenters = CostCenter::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $allocationDrivers = AllocationDriver::where('hospital_id', hospital('id'))->orderBy('name')->get();
        
        return view('driver-statistics.bulk-input', compact('costCenters', 'allocationDrivers'));
    }

    public function bulkInput(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'allocation_driver_id' => 'required|exists:allocation_drivers,id',
            'values' => 'required|array',
            'values.*.cost_center_id' => 'required|exists:cost_centers,id',
            'values.*.value' => 'required|numeric|min:0',
        ]);

        $allocationDriver = AllocationDriver::where('id', $validated['allocation_driver_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$allocationDriver) {
            return back()->withErrors(['allocation_driver_id' => 'Allocation driver tidak valid.'])->withInput();
        }

        $imported = 0;
        foreach ($validated['values'] as $valueData) {
            $costCenter = CostCenter::where('id', $valueData['cost_center_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$costCenter) continue;
            
            $existing = DriverStatistic::where('hospital_id', hospital('id'))
                ->where('period_month', $validated['period_month'])
                ->where('period_year', $validated['period_year'])
                ->where('cost_center_id', $costCenter->id)
                ->where('allocation_driver_id', $allocationDriver->id)
                ->first();
            
            if ($existing) {
                $existing->update(['value' => $valueData['value']]);
            } else {
                DriverStatistic::create([
                    'hospital_id' => hospital('id'),
                    'period_month' => $validated['period_month'],
                    'period_year' => $validated['period_year'],
                    'cost_center_id' => $costCenter->id,
                    'allocation_driver_id' => $allocationDriver->id,
                    'value' => $valueData['value'],
                ]);
            }
            $imported++;
        }

        return redirect()->route('driver-statistics.index')
            ->with('success', "Berhasil menyimpan {$imported} driver statistics.");
    }

    public function importForm()
    {
        return view('driver-statistics.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            array_shift($rows); // Skip header
            
            $imported = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue;
                
                try {
                    // Expected format: Cost Center Code, Allocation Driver Name, Value
                    $costCenterCode = trim($row[0] ?? '');
                    $allocationDriverName = trim($row[1] ?? '');
                    $value = floatval($row[2] ?? 0);
                    
                    if (empty($costCenterCode) || empty($allocationDriverName) || $value <= 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        continue;
                    }
                    
                    $costCenter = CostCenter::where('hospital_id', hospital('id'))
                        ->where('code', $costCenterCode)
                        ->first();
                    
                    if (!$costCenter) {
                        $errors[] = "Baris " . ($index + 2) . ": Cost center dengan code '{$costCenterCode}' tidak ditemukan";
                        continue;
                    }
                    
                    $allocationDriver = AllocationDriver::where('hospital_id', hospital('id'))
                        ->where('name', $allocationDriverName)
                        ->first();
                    
                    if (!$allocationDriver) {
                        $errors[] = "Baris " . ($index + 2) . ": Allocation driver dengan name '{$allocationDriverName}' tidak ditemukan";
                        continue;
                    }
                    
                    $existing = DriverStatistic::where('hospital_id', hospital('id'))
                        ->where('period_month', $request->period_month)
                        ->where('period_year', $request->period_year)
                        ->where('cost_center_id', $costCenter->id)
                        ->where('allocation_driver_id', $allocationDriver->id)
                        ->first();
                    
                    if ($existing) {
                        $existing->update(['value' => $value]);
                    } else {
                        DriverStatistic::create([
                            'hospital_id' => hospital('id'),
                            'period_month' => $request->period_month,
                            'period_year' => $request->period_year,
                            'cost_center_id' => $costCenter->id,
                            'allocation_driver_id' => $allocationDriver->id,
                            'value' => $value,
                        ]);
                    }
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            $message = "Berhasil mengimpor {$imported} data.";
            if (count($errors) > 0) {
                $message .= " Terdapat " . count($errors) . " error: " . implode(', ', array_slice($errors, 0, 5));
            }
            
            return redirect()->route('driver-statistics.index')
                ->with('success', $message)
                ->with('errors', $errors);
                
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error membaca file: ' . $e->getMessage()])->withInput();
        }
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $periodMonth = $request->get('period_month');
        $periodYear = $request->get('period_year', date('Y'));
        $costCenterId = $request->get('cost_center_id');
        $allocationDriverId = $request->get('allocation_driver_id');
        
        $query = DriverStatistic::where('hospital_id', hospital('id'))
            ->with(['costCenter', 'allocationDriver'])
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->orderBy('cost_center_id');
        
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        
        if ($periodYear) {
            $query->where('period_year', $periodYear);
        }
        
        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }
        
        if ($allocationDriverId) {
            $query->where('allocation_driver_id', $allocationDriverId);
        }
        
        if ($search) {
            $query->whereHas('costCenter', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            })->orWhereHas('allocationDriver', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Period', 'Cost Center', 'Allocation Driver', 'Value'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->period_month . '/' . $item->period_year,
                    $item->costCenter ? $item->costCenter->name . ' (' . $item->costCenter->code . ')' : '-',
                    $item->allocationDriver ? $item->allocationDriver->name . ' (' . $item->allocationDriver->unit_measurement . ')' : '-',
                    (float) $item->value,
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'driver_statistics_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Bulk delete driver statistics
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:driver_statistics,id',
        ]);

        try {
            $deleted = DriverStatistic::where('hospital_id', hospital('id'))
                ->whereIn('id', $request->ids)
                ->delete();

            return redirect()->route('driver-statistics.index')
                ->with('success', "Berhasil menghapus {$deleted} driver statistic(s).");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus driver statistics: ' . $e->getMessage());
        }
    }

    /**
     * Show form to copy static data from previous period
     */
    public function copyFromPreviousPeriodForm()
    {
        $hospitalId = hospital('id');
        
        // Get available periods (ordered by most recent first)
        $availablePeriods = DriverStatistic::where('hospital_id', $hospitalId)
            ->select('period_year', 'period_month')
            ->distinct()
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->period_year,
                    'month' => $item->period_month,
                    'label' => date('F Y', mktime(0, 0, 0, $item->period_month, 1, $item->period_year)),
                ];
            });

        // Get static drivers
        $staticDrivers = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('is_static', true)
            ->orderBy('name')
            ->get();

        return view('driver-statistics.copy-from-previous-period', compact('availablePeriods', 'staticDrivers'));
    }

    /**
     * Copy static data from previous period to current period
     */
    public function copyFromPreviousPeriod(Request $request)
    {
        $validated = $request->validate([
            'source_year' => 'required|integer|min:2000|max:2100',
            'source_month' => 'required|integer|between:1,12',
            'target_year' => 'required|integer|min:2000|max:2100',
            'target_month' => 'required|integer|between:1,12',
        ]);

        $hospitalId = hospital('id');
        $sourceYear = $validated['source_year'];
        $sourceMonth = $validated['source_month'];
        $targetYear = $validated['target_year'];
        $targetMonth = $validated['target_month'];

        // Check if source and target are different
        if ($sourceYear == $targetYear && $sourceMonth == $targetMonth) {
            return back()->withErrors(['source_month' => 'Source period dan target period tidak boleh sama.'])->withInput();
        }

        // Get static drivers
        $staticDriverIds = AllocationDriver::where('hospital_id', $hospitalId)
            ->where('is_static', true)
            ->pluck('id');

        if ($staticDriverIds->isEmpty()) {
            return back()->withErrors(['source_month' => 'Tidak ada static drivers yang ditemukan.'])->withInput();
        }

        // Get source data (only static drivers)
        $sourceData = DriverStatistic::where('hospital_id', $hospitalId)
            ->where('period_year', $sourceYear)
            ->where('period_month', $sourceMonth)
            ->whereIn('allocation_driver_id', $staticDriverIds)
            ->get();

        if ($sourceData->isEmpty()) {
            return back()->withErrors(['source_month' => 'Tidak ada data static untuk periode sumber yang dipilih.'])->withInput();
        }

        $copied = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($sourceData as $sourceStat) {
                // Check if target already exists
                $existing = DriverStatistic::where('hospital_id', $hospitalId)
                    ->where('period_year', $targetYear)
                    ->where('period_month', $targetMonth)
                    ->where('cost_center_id', $sourceStat->cost_center_id)
                    ->where('allocation_driver_id', $sourceStat->allocation_driver_id)
                    ->first();

                if ($existing) {
                    // Update existing
                    $existing->update(['value' => $sourceStat->value]);
                    $updated++;
                } else {
                    // Create new
                    DriverStatistic::create([
                        'hospital_id' => $hospitalId,
                        'period_year' => $targetYear,
                        'period_month' => $targetMonth,
                        'cost_center_id' => $sourceStat->cost_center_id,
                        'allocation_driver_id' => $sourceStat->allocation_driver_id,
                        'value' => $sourceStat->value,
                    ]);
                    $copied++;
                }
            }

            DB::commit();

            $message = "Berhasil copy {$copied} data baru dan update {$updated} data yang sudah ada dari periode " . date('F Y', mktime(0, 0, 0, $sourceMonth, 1, $sourceYear)) . " ke periode " . date('F Y', mktime(0, 0, 0, $targetMonth, 1, $targetYear)) . ".";

            return redirect()->route('driver-statistics.index', [
                'period_year' => $targetYear,
                'period_month' => $targetMonth,
            ])->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['source_month' => 'Error saat copy data: ' . $e->getMessage()])->withInput();
        }
    }
}






