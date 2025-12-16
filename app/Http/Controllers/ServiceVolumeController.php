<?php

namespace App\Http\Controllers;

use App\Models\ServiceVolume;
use App\Models\CostReference;
use App\Models\TariffClass;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ServiceVolumeController extends Controller
{
    // Category options matching cost-references
    public const CATEGORY_OPTIONS = [
        'barang' => 'Obat/BHP',
        'tindakan_rj' => 'Tindakan Rawat Jalan',
        'tindakan_ri' => 'Tindakan Rawat Inap',
        'laboratorium' => 'Laboratorium',
        'radiologi' => 'Radiologi',
        'operasi' => 'Operasi',
        'kamar' => 'Kamar',
    ];

    public function index(Request $request)
    {
        $search = $request->get('search');
        $periodYear = $request->get('period_year', date('Y'));
        $category = $request->get('category');
        
        // Base query for counts (Search only, NO Category filter yet)
        $baseQuery = CostReference::where('hospital_id', hospital('id'))
            ->when($search, function($q) use ($search) {
                return $q->where(function($sub) use ($search) {
                    $sub->where('service_code', 'LIKE', "%{$search}%")
                        ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            });

        // Calculate counts for tabs
        $categoryCounts = [
            'all' => $baseQuery->count(),
            'barang' => (clone $baseQuery)->where('category', 'barang')->count(),
            'tindakan_rj' => (clone $baseQuery)->where('category', 'tindakan_rj')->count(),
            'tindakan_ri' => (clone $baseQuery)->where('category', 'tindakan_ri')->count(),
            'laboratorium' => (clone $baseQuery)->where('category', 'laboratorium')->count(),
            'radiologi' => (clone $baseQuery)->where('category', 'radiologi')->count(),
            'operasi' => (clone $baseQuery)->where('category', 'operasi')->count(),
            'kamar' => (clone $baseQuery)->where('category', 'kamar')->count(),
        ];

        // Clone base query for the main list and apply category filter
        $query = clone $baseQuery;
        
        if ($category) {
            $query->where('category', $category);
        }

        // 1. Get paginated services
        $services = $query->orderBy('category')
            ->orderBy('service_description')
            ->paginate(50) // Higher pagination for matrix view
            ->appends($request->query());

        // 2. Fetch volumes for these services for the selected year
        $serviceIds = $services->pluck('id');
        $volumes = ServiceVolume::where('hospital_id', hospital('id'))
            ->whereIn('cost_reference_id', $serviceIds)
            ->where('period_year', $periodYear)
            ->selectRaw('cost_reference_id, period_month, SUM(total_quantity) as total_quantity')
            ->groupBy('cost_reference_id', 'period_month')
            ->get();

        // 3. Map volumes to structure: [cost_reference_id][month] = quantity
        $volumeMap = [];
        foreach ($volumes as $vol) {
            $volumeMap[$vol->cost_reference_id][$vol->period_month] = $vol->total_quantity;
        }
        
        $costReferences = CostReference::where('hospital_id', hospital('id'))->orderBy('service_code')->get(); // kept for filters/modals if needed
        $categoryOptions = self::CATEGORY_OPTIONS;
        
        return view('service-volumes.index', compact(
            'services', 
            'volumeMap', 
            'search', 
            'periodYear', 
            'category', 
            'categoryOptions',
            'categoryCounts'
        ));
    }

    public function create()
    {
        $costReferences = CostReference::where('hospital_id', hospital('id'))->orderBy('service_code')->get();
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $categoryOptions = self::CATEGORY_OPTIONS;
        
        return view('service-volumes.create', compact('costReferences', 'tariffClasses', 'categoryOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_reference_id' => 'required|exists:cost_references,id',
            'tariff_class_id' => 'nullable|exists:tariff_classes,id',
            'category' => 'nullable|string|in:barang,tindakan_rj,tindakan_ri,laboratorium,radiologi,operasi,kamar',
            'total_quantity' => 'required|numeric|min:0',
        ]);

        // Ensure cost reference belongs to same hospital
        $costReference = CostReference::where('id', $validated['cost_reference_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$costReference) {
            return back()->withErrors(['cost_reference_id' => 'Cost reference tidak valid.'])->withInput();
        }

        if ($validated['tariff_class_id']) {
            $tariffClass = TariffClass::where('id', $validated['tariff_class_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$tariffClass) {
                return back()->withErrors(['tariff_class_id' => 'Tariff class tidak valid.'])->withInput();
            }
        }

        ServiceVolume::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
        ]));

        return redirect()->route('service-volumes.index')
            ->with('success', 'Service volume berhasil dibuat.');
    }

    public function show(ServiceVolume $serviceVolume)
    {
        if ($serviceVolume->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $serviceVolume->load(['costReference', 'tariffClass']);
        
        return view('service-volumes.show', compact('serviceVolume'));
    }

    public function edit(ServiceVolume $serviceVolume)
    {
        if ($serviceVolume->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $costReferences = CostReference::where('hospital_id', hospital('id'))->orderBy('service_code')->get();
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))->where('is_active', true)->orderBy('name')->get();
        $categoryOptions = self::CATEGORY_OPTIONS;
        
        return view('service-volumes.edit', compact('serviceVolume', 'costReferences', 'tariffClasses', 'categoryOptions'));
    }

    public function update(Request $request, ServiceVolume $serviceVolume)
    {
        if ($serviceVolume->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_reference_id' => 'required|exists:cost_references,id',
            'tariff_class_id' => 'nullable|exists:tariff_classes,id',
            'category' => 'nullable|string|in:barang,tindakan_rj,tindakan_ri,laboratorium,radiologi,operasi,kamar',
            'total_quantity' => 'required|numeric|min:0',
        ]);

        // Ensure cost reference belongs to same hospital
        $costReference = CostReference::where('id', $validated['cost_reference_id'])
            ->where('hospital_id', hospital('id'))
            ->first();
        
        if (!$costReference) {
            return back()->withErrors(['cost_reference_id' => 'Cost reference tidak valid.'])->withInput();
        }

        if ($validated['tariff_class_id']) {
            $tariffClass = TariffClass::where('id', $validated['tariff_class_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$tariffClass) {
                return back()->withErrors(['tariff_class_id' => 'Tariff class tidak valid.'])->withInput();
            }
        }

        $serviceVolume->update($validated);

        return redirect()->route('service-volumes.index')
            ->with('success', 'Service volume berhasil diperbarui.');
    }

    public function destroy(ServiceVolume $serviceVolume)
    {
        if ($serviceVolume->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $serviceVolume->delete();

        return redirect()->route('service-volumes.index')
            ->with('success', 'Service volume berhasil dihapus.');
    }

    public function importForm()
    {
        return view('service-volumes.import');
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
                    // Expected format: Service Code, Tariff Class Code (optional), Total Quantity
                    $serviceCode = trim($row[0] ?? '');
                    $tariffClassCode = trim($row[1] ?? '');
                    $totalQuantity = floatval($row[2] ?? 0);
                    
                    if (empty($serviceCode) || $totalQuantity <= 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        continue;
                    }
                    
                    $costReference = CostReference::where('hospital_id', hospital('id'))
                        ->where('service_code', $serviceCode)
                        ->first();
                    
                    if (!$costReference) {
                        $errors[] = "Baris " . ($index + 2) . ": Cost reference dengan code '{$serviceCode}' tidak ditemukan";
                        continue;
                    }
                    
                    $tariffClassId = null;
                    if (!empty($tariffClassCode)) {
                        $tariffClass = TariffClass::where('hospital_id', hospital('id'))
                            ->where('code', $tariffClassCode)
                            ->first();
                        
                        if (!$tariffClass) {
                            $errors[] = "Baris " . ($index + 2) . ": Tariff class dengan code '{$tariffClassCode}' tidak ditemukan";
                            continue;
                        }
                        $tariffClassId = $tariffClass->id;
                    }
                    
                    $existing = ServiceVolume::where('hospital_id', hospital('id'))
                        ->where('period_month', $request->period_month)
                        ->where('period_year', $request->period_year)
                        ->where('cost_reference_id', $costReference->id)
                        ->where('tariff_class_id', $tariffClassId)
                        ->first();
                    
                    if ($existing) {
                        $existing->update(['total_quantity' => $totalQuantity]);
                    } else {
                        ServiceVolume::create([
                            'hospital_id' => hospital('id'),
                            'period_month' => $request->period_month,
                            'period_year' => $request->period_year,
                            'cost_reference_id' => $costReference->id,
                            'tariff_class_id' => $tariffClassId,
                            'total_quantity' => $totalQuantity,
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
            
            return redirect()->route('service-volumes.index')
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
        $costReferenceId = $request->get('cost_reference_id');
        $tariffClassId = $request->get('tariff_class_id');
        
        $query = ServiceVolume::where('hospital_id', hospital('id'))
            ->with(['costReference', 'tariffClass'])
            ->orderBy('period_year')
            ->orderBy('period_month')
            ->orderBy('cost_reference_id');
        
        if ($periodMonth) {
            $query->where('period_month', $periodMonth);
        }
        
        if ($periodYear) {
            $query->where('period_year', $periodYear);
        }
        
        if ($costReferenceId) {
            $query->where('cost_reference_id', $costReferenceId);
        }
        
        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }
        
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Period', 'Service Code', 'Service Description', 'Tariff Class', 'Total Quantity'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->period_month . '/' . $item->period_year,
                    $item->costReference ? $item->costReference->service_code : '-',
                    $item->costReference ? $item->costReference->service_description : '-',
                    $item->tariffClass ? $item->tariffClass->name : '-',
                    (float) $item->total_quantity,
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'service_volumes_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Bulk delete service volumes
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:service_volumes,id',
        ]);

        try {
            $deleted = ServiceVolume::where('hospital_id', hospital('id'))
                ->whereIn('id', $request->ids)
                ->delete();

            return redirect()->route('service-volumes.index')
                ->with('success', "Berhasil menghapus {$deleted} service volume(s).");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus service volumes: ' . $e->getMessage());
        }
    }
}






