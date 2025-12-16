<?php

namespace App\Http\Controllers;

use App\Models\StandardResourceUsage;
use App\Models\CostReference;
use App\Http\Requests\StoreStandardResourceUsageRequest;
use App\Http\Requests\UpdateStandardResourceUsageRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StandardResourceUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hospitalId = hospital('id');
        $search = $request->get('search');
        $serviceId = $request->get('service_id');
        $bmhpId = $request->get('bmhp_id');
        $isActive = $request->get('is_active');
        $category = $request->get('category');

        $baseQuery = StandardResourceUsage::with(['service', 'bmhp', 'creator', 'updater'])
            ->where('hospital_id', $hospitalId);

        // Build query for filtering
        $query = clone $baseQuery;

        // Filter by service
        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        // Filter by BMHP
        if ($bmhpId) {
            $query->where('bmhp_id', $bmhpId);
        }

        // Filter by active status
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        // Filter by category
        if ($category) {
            $query->where(function($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('service', function($sq) use ($category) {
                      $sq->where('category', $category);
                  });
            });
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('service', function($sq) use ($search) {
                    $sq->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                })
                ->orWhere('service_name', 'LIKE', "%{$search}%")
                ->orWhere('service_code', 'LIKE', "%{$search}%")
                ->orWhereHas('bmhp', function($bq) use ($search) {
                    $bq->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            });
        }

        $standardResourceUsages = $query->latest()->paginate(15)->appends($request->query());

        // Calculate counts for category tabs (considering other filters but not category)
        // We need to count unique services per category
        $categoryCountQuery = clone $baseQuery;
        
        if ($search) {
            $categoryCountQuery->where(function($q) use ($search) {
                $q->whereHas('service', function($sq) use ($search) {
                    $sq->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                })
                ->orWhere('service_name', 'LIKE', "%{$search}%")
                ->orWhere('service_code', 'LIKE', "%{$search}%")
                ->orWhereHas('bmhp', function($bq) use ($search) {
                    $bq->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            });
        }
        
        if ($serviceId) {
            $categoryCountQuery->where('service_id', $serviceId);
        }
        
        if ($bmhpId) {
            $categoryCountQuery->where('bmhp_id', $bmhpId);
        }
        
        if ($isActive !== null) {
            $categoryCountQuery->where('is_active', $isActive);
        }

        // Get all records to count unique services per category
        $allRecords = $categoryCountQuery->with('service')->get();
        
        // Group by service identifier and get unique categories
        $serviceCategories = [];
        foreach ($allRecords as $record) {
            $serviceKey = $record->service_id ?: ($record->service_name . '|' . $record->service_code);
            if (!isset($serviceCategories[$serviceKey])) {
                $cat = $record->category ?? ($record->service->category ?? null);
                $serviceCategories[$serviceKey] = $cat;
            }
        }

        // Count services per category
        $categoryCounts = [
            'all' => count($serviceCategories),
            'barang' => count(array_filter($serviceCategories, fn($cat) => $cat === 'barang')),
            'tindakan_rj' => count(array_filter($serviceCategories, fn($cat) => $cat === 'tindakan_rj')),
            'tindakan_ri' => count(array_filter($serviceCategories, fn($cat) => $cat === 'tindakan_ri')),
            'laboratorium' => count(array_filter($serviceCategories, fn($cat) => $cat === 'laboratorium')),
            'radiologi' => count(array_filter($serviceCategories, fn($cat) => $cat === 'radiologi')),
            'operasi' => count(array_filter($serviceCategories, fn($cat) => $cat === 'operasi')),
            'kamar' => count(array_filter($serviceCategories, fn($cat) => $cat === 'kamar')),
        ];

        // Get services and BMHP for filters
        // Service list: ambil semua cost_references milik hospital (tanpa filter item_type agar selalu muncul)
        $services = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_description')
            ->get();

        // BMHP list: untuk saat ini juga ambil semua cost_references,
        // nanti bisa difilter item_type='bmhp' jika data sudah lengkap
        $bmhpList = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_description')
            ->get();

        return view('setup.service-catalog.standard-resource-usages.index', compact(
            'standardResourceUsages',
            'services',
            'bmhpList',
            'search',
            'serviceId',
            'bmhpId',
            'isActive',
            'category',
            'categoryCounts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hospitalId = hospital('id');
        // Ambil semua cost_references untuk pilihan service (berdasarkan Description)
        $services = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_description')
            ->get();

        // BMHP sementara juga dari semua cost_references
        $bmhpList = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_description')
            ->get();

        return view('setup.service-catalog.standard-resource-usages.form', [
            'isEditMode' => false,
            'serviceId' => null,
            'serviceData' => [
                'service_id' => null,
                'service_name' => '',
                'service_code' => '',
                'is_active' => true,
            ],
            'bmhpItems' => [],
            'services' => $services,
            'bmhpList' => $bmhpList,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStandardResourceUsageRequest $request)
    {
        $hospitalId = hospital('id');
        $validated = $request->validated();
        
        // Prepare service data
        $serviceData = [
            'service_id' => $validated['service_id'] ?? null,
            'service_name' => $validated['service_name'],
            'service_code' => $validated['service_code'] ?? '',
            'category' => $validated['category'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Jika memilih service dari master, pastikan service_code & service_name terisi dari CostReference
        if (!empty($serviceData['service_id'])) {
            $service = CostReference::find($serviceData['service_id']);
            if ($service) {
                $serviceData['service_code'] = $serviceData['service_code'] ?: $service->service_code;
                $serviceData['service_name'] = $serviceData['service_name'] ?: $service->service_description;
                // Jika category belum diisi, ambil dari service jika ada
                if (empty($serviceData['category']) && $service->category) {
                    $serviceData['category'] = $service->category;
                }
            }
        }

        // Get BMHP items
        $bmhpItems = $validated['bmhp_items'] ?? [];
        
        if (empty($bmhpItems)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['bmhp_items' => 'Minimal harus ada 1 BMHP.']);
        }

        // Save all BMHP items in a transaction
        \DB::transaction(function() use ($hospitalId, $serviceData, $bmhpItems) {
            foreach ($bmhpItems as $item) {
                StandardResourceUsage::create([
                    'hospital_id' => $hospitalId,
                    'service_id' => $serviceData['service_id'],
                    'service_name' => $serviceData['service_name'],
                    'service_code' => $serviceData['service_code'],
                    'category' => $serviceData['category'],
                    'bmhp_id' => $item['bmhp_id'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'is_active' => $serviceData['is_active'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('standard-resource-usages.index')
            ->with('success', 'Standard Resource Usage berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hospitalId = hospital('id');
        $standardResourceUsage = StandardResourceUsage::with(['service', 'bmhp', 'creator', 'updater'])
            ->where('hospital_id', $hospitalId)
            ->findOrFail($id);

        return view('setup.service-catalog.standard-resource-usages.show', compact('standardResourceUsage'));
    }

    /**
     * Show the form for editing the specified resource.
     * Edit berdasarkan service (bukan per baris BMHP)
     */
    public function edit(string $id)
    {
        $hospitalId = hospital('id');
        $standardResourceUsage = StandardResourceUsage::where('hospital_id', $hospitalId)
            ->findOrFail($id);

        // Identifikasi service berdasarkan service_id atau service_name/service_code
        $serviceIdentifier = $standardResourceUsage->service_id 
            ? ['service_id' => $standardResourceUsage->service_id]
            : [
                'service_name' => $standardResourceUsage->service_name,
                'service_code' => $standardResourceUsage->service_code,
            ];

        // Load semua BMHP untuk service ini
        $query = StandardResourceUsage::where('hospital_id', $hospitalId);
        
        if ($standardResourceUsage->service_id) {
            $query->where('service_id', $standardResourceUsage->service_id);
        } else {
            $query->whereNull('service_id')
                  ->where('service_name', $standardResourceUsage->service_name)
                  ->where('service_code', $standardResourceUsage->service_code);
        }

        $allBmhpItems = $query->get();
        
        // Prepare BMHP items data
        $bmhpItems = [];
        foreach ($allBmhpItems as $item) {
            $bmhpItems[] = [
                'bmhp_id' => $item->bmhp_id,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
            ];
        }

        // Prepare service data
        $serviceData = [
            'service_id' => $standardResourceUsage->service_id,
            'service_name' => $standardResourceUsage->service_name ?? optional($standardResourceUsage->service)->service_description,
            'service_code' => $standardResourceUsage->service_code ?? optional($standardResourceUsage->service)->service_code,
            'category' => $standardResourceUsage->category,
            'is_active' => $standardResourceUsage->is_active,
        ];

        // Ambil semua cost_references untuk pilihan service (berdasarkan Description)
        $services = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_description')
            ->get();

        // BMHP sementara juga dari semua cost_references
        $bmhpList = CostReference::where('hospital_id', $hospitalId)
            ->orderBy('service_description')
            ->get();

        return view('setup.service-catalog.standard-resource-usages.form', [
            'isEditMode' => true,
            'serviceId' => $id, // ID pertama untuk route update
            'serviceData' => $serviceData,
            'bmhpItems' => $bmhpItems,
            'services' => $services,
            'bmhpList' => $bmhpList,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Update semua BMHP untuk service tersebut (replace semua)
     */
    public function update(UpdateStandardResourceUsageRequest $request, string $id)
    {
        $hospitalId = hospital('id');
        $firstRecord = StandardResourceUsage::where('hospital_id', $hospitalId)
            ->findOrFail($id);

        $validated = $request->validated();
        
        // Prepare service data
        $serviceData = [
            'service_id' => $validated['service_id'] ?? null,
            'service_name' => $validated['service_name'],
            'service_code' => $validated['service_code'] ?? '',
            'category' => $validated['category'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Jika memilih service dari master, pastikan service_code & service_name terisi dari CostReference
        if (!empty($serviceData['service_id'])) {
            $service = CostReference::find($serviceData['service_id']);
            if ($service) {
                $serviceData['service_code'] = $serviceData['service_code'] ?: $service->service_code;
                $serviceData['service_name'] = $serviceData['service_name'] ?: $service->service_description;
                // Jika category belum diisi, ambil dari service jika ada
                if (empty($serviceData['category']) && $service->category) {
                    $serviceData['category'] = $service->category;
                }
            }
        }

        // Get BMHP items
        $bmhpItems = $validated['bmhp_items'] ?? [];
        
        if (empty($bmhpItems)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['bmhp_items' => 'Minimal harus ada 1 BMHP.']);
        }

        // Identifikasi semua record untuk service ini (untuk dihapus)
        $query = StandardResourceUsage::where('hospital_id', $hospitalId);
        
        if ($firstRecord->service_id) {
            $query->where('service_id', $firstRecord->service_id);
        } else {
            $query->whereNull('service_id')
                  ->where('service_name', $firstRecord->service_name)
                  ->where('service_code', $firstRecord->service_code);
        }

        // Replace semua BMHP dalam transaction
        \DB::transaction(function() use ($hospitalId, $serviceData, $bmhpItems, $query) {
            // Hapus semua record lama untuk service ini
            $query->delete();

            // Insert semua record baru
            foreach ($bmhpItems as $item) {
                StandardResourceUsage::create([
                    'hospital_id' => $hospitalId,
                    'service_id' => $serviceData['service_id'],
                    'service_name' => $serviceData['service_name'],
                    'service_code' => $serviceData['service_code'],
                    'category' => $serviceData['category'],
                    'bmhp_id' => $item['bmhp_id'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'is_active' => $serviceData['is_active'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('standard-resource-usages.index')
            ->with('success', 'Standard Resource Usage berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Hapus semua BMHP untuk service tersebut
     */
    public function destroy(string $id)
    {
        $hospitalId = hospital('id');
        $standardResourceUsage = StandardResourceUsage::where('hospital_id', $hospitalId)
            ->findOrFail($id);

        // Identifikasi semua record untuk service ini
        $query = StandardResourceUsage::where('hospital_id', $hospitalId);
        
        if ($standardResourceUsage->service_id) {
            $query->where('service_id', $standardResourceUsage->service_id);
        } else {
            $query->whereNull('service_id')
                  ->where('service_name', $standardResourceUsage->service_name)
                  ->where('service_code', $standardResourceUsage->service_code);
        }

        // Hapus semua BMHP untuk service ini
        $count = $query->count();
        $query->delete();

        return redirect()->route('standard-resource-usages.index')
            ->with('success', "Standard Resource Usage berhasil dihapus ({$count} BMHP untuk service tersebut).");
    }

    /**
     * Export standard resource usages to Excel for the current hospital.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $hospitalId = hospital('id');
        $search = $request->get('search');
        $serviceId = $request->get('service_id');
        $bmhpId = $request->get('bmhp_id');
        $isActive = $request->get('is_active');
        $category = $request->get('category');

        $query = StandardResourceUsage::with(['service', 'bmhp'])
            ->where('hospital_id', $hospitalId);

        // Filter by service
        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        // Filter by BMHP
        if ($bmhpId) {
            $query->where('bmhp_id', $bmhpId);
        }

        // Filter by active status
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }

        // Filter by category
        if ($category) {
            $query->where(function($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('service', function($sq) use ($category) {
                      $sq->where('category', $category);
                  });
            });
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('service', function($sq) use ($search) {
                    $sq->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                })
                ->orWhere('service_name', 'LIKE', "%{$search}%")
                ->orWhere('service_code', 'LIKE', "%{$search}%")
                ->orWhereHas('bmhp', function($bq) use ($search) {
                    $bq->where('service_code', 'LIKE', "%{$search}%")
                      ->orWhere('service_description', 'LIKE', "%{$search}%");
                });
            });
        }

        $data = $query->orderBy('service_name')
            ->orderBy('service_code')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'Service Code',
            'Service Name',
            'Category',
            'BMHP Code',
            'BMHP Name',
            'Quantity',
            'Unit',
            'BMHP Price',
            'Total Cost',
            'Status'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Category labels
        $categoryLabels = [
            'barang' => 'Obat/BHP',
            'tindakan_rj' => 'Tindakan Rawat Jalan',
            'tindakan_ri' => 'Tindakan Rawat Inap',
            'laboratorium' => 'Laboratorium',
            'radiologi' => 'Radiologi',
            'operasi' => 'Operasi',
            'kamar' => 'Kamar',
        ];

        // Rows
        if ($data->count() > 0) {
            $rows = [];
            foreach ($data as $item) {
                $serviceCode = $item->service->service_code ?? $item->service_code ?? '-';
                $serviceName = $item->service->service_description ?? $item->service_name ?? '-';
                $category = $item->category ?? ($item->service->category ?? null);
                $categoryLabel = $category ? ($categoryLabels[$category] ?? $category) : '-';
                $bmhpCode = $item->bmhp->service_code ?? '-';
                $bmhpName = $item->bmhp->service_description ?? '-';
                $bmhpPrice = $item->bmhp->purchase_price ?? $item->bmhp->standard_cost ?? 0;
                $totalCost = $item->getTotalCost();
                $status = $item->is_active ? 'Aktif' : 'Tidak Aktif';

                $rows[] = [
                    $serviceCode,
                    $serviceName,
                    $categoryLabel,
                    $bmhpCode,
                    $bmhpName,
                    (float) $item->quantity,
                    $item->unit,
                    (float) $bmhpPrice,
                    (float) $totalCost,
                    $status,
                ];
            }
            $sheet->fromArray($rows, null, 'A2');
        }

        // Format number columns
        $sheet->getStyle('F2:F' . max(2, $data->count() + 1))
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $sheet->getStyle('H2:I' . max(2, $data->count() + 1))
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        $filename = 'standard_resource_usages_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
