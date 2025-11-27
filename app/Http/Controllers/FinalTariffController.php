<?php

namespace App\Http\Controllers;

use App\Models\FinalTariff;
use App\Models\CostReference;
use App\Models\TariffClass;
use App\Models\UnitCostCalculation;
use App\Services\TariffService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FinalTariffController extends Controller
{
    protected $tariffService;

    public function __construct(TariffService $tariffService)
    {
        $this->tariffService = $tariffService;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $tariffClassId = $request->get('tariff_class_id');
        $skNumber = $request->get('sk_number');
        $effectiveDateFrom = $request->get('effective_date_from');
        $effectiveDateTo = $request->get('effective_date_to');
        $showExpired = $request->get('show_expired', false);
        
        $query = FinalTariff::where('hospital_id', hospital('id'))
            ->with(['costReference', 'tariffClass', 'unitCostCalculation']);
        
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }
        
        if ($skNumber) {
            $query->where('sk_number', 'LIKE', "%{$skNumber}%");
        }
        
        if ($effectiveDateFrom) {
            $query->where('effective_date', '>=', $effectiveDateFrom);
        }
        
        if ($effectiveDateTo) {
            $query->where('effective_date', '<=', $effectiveDateTo);
        }
        
        if (!$showExpired) {
            $query->active();
        }
        
        $finalTariffs = $query->orderBy('effective_date', 'desc')
            ->orderBy('cost_reference_id')
            ->paginate(20)
            ->appends($request->query());
        
        // Get tariff classes for filter
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('final-tariffs.index', compact(
            'finalTariffs',
            'search',
            'tariffClassId',
            'skNumber',
            'effectiveDateFrom',
            'effectiveDateTo',
            'showExpired',
            'tariffClasses'
        ));
    }

    public function create(Request $request)
    {
        // Get cost references
        $costReferences = CostReference::where('hospital_id', hospital('id'))
            ->orderBy('service_code')
            ->get();
        
        // Get tariff classes
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get available unit cost versions
        $versions = $this->tariffService->getAvailableVersions();
        
        // Pre-fill from request if coming from simulation
        $prefill = [
            'cost_reference_id' => $request->get('cost_reference_id'),
            'unit_cost_calculation_id' => $request->get('unit_cost_calculation_id'),
            'base_unit_cost' => $request->get('base_unit_cost'),
            'margin_percentage' => $request->get('margin_percentage', 0.20),
            'jasa_sarana' => $request->get('jasa_sarana', 0),
            'jasa_pelayanan' => $request->get('jasa_pelayanan', 0),
            'final_tariff_price' => $request->get('final_tariff_price'),
        ];
        
        return view('final-tariffs.create', compact(
            'costReferences',
            'tariffClasses',
            'versions',
            'prefill'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_reference_id' => 'required|exists:cost_references,id',
            'tariff_class_id' => 'nullable|exists:tariff_classes,id',
            'unit_cost_calculation_id' => 'required|exists:unit_cost_calculations,id',
            'sk_number' => 'required|string|max:100',
            'base_unit_cost' => 'required|numeric|min:0',
            'margin_percentage' => 'required|numeric|min:0|max:10',
            'jasa_sarana' => 'nullable|numeric|min:0',
            'jasa_pelayanan' => 'nullable|numeric|min:0',
            'final_tariff_price' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'expired_date' => 'nullable|date|after:effective_date',
        ]);

        // Validate unit cost calculation belongs to hospital
        $unitCost = UnitCostCalculation::findOrFail($validated['unit_cost_calculation_id']);
        if ($unitCost->hospital_id !== hospital('id')) {
            abort(404);
        }

        // Validate base unit cost matches unit cost calculation
        if (abs($unitCost->total_unit_cost - $validated['base_unit_cost']) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Base unit cost tidak sesuai dengan unit cost calculation.');
        }

        // Validate final tariff price calculation
        $calculatedPrice = $this->tariffService->calculateTariff(
            $validated['base_unit_cost'],
            $validated['margin_percentage'],
            $validated['jasa_sarana'] ?? 0,
            $validated['jasa_pelayanan'] ?? 0
        );

        if (abs($calculatedPrice - $validated['final_tariff_price']) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Final tariff price tidak sesuai dengan perhitungan. Harus: ' . number_format($calculatedPrice, 2));
        }

        // Validate final tariff price >= base unit cost
        if ($validated['final_tariff_price'] < $validated['base_unit_cost']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Final tariff price harus lebih besar atau sama dengan base unit cost.');
        }

        // Check for overlapping tariffs
        if ($this->tariffService->hasOverlappingTariff(
            $validated['cost_reference_id'],
            $validated['effective_date'],
            $validated['expired_date'] ?? null
        )) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Effective date overlap dengan tariff yang sudah ada untuk service ini.');
        }

        FinalTariff::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
        ]));

        return redirect()->route('final-tariffs.index')
            ->with('success', 'Final tariff berhasil dibuat.');
    }

    public function show(FinalTariff $finalTariff)
    {
        if ($finalTariff->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $finalTariff->load(['costReference', 'tariffClass', 'unitCostCalculation']);
        
        // Get tariff history for this service
        $history = $this->tariffService->getTariffHistory(
            $finalTariff->cost_reference_id,
            $finalTariff->tariff_class_id
        );
        
        return view('final-tariffs.show', compact('finalTariff', 'history'));
    }

    public function edit(FinalTariff $finalTariff)
    {
        if ($finalTariff->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Get cost references
        $costReferences = CostReference::where('hospital_id', hospital('id'))
            ->orderBy('service_code')
            ->get();
        
        // Get tariff classes
        $tariffClasses = TariffClass::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get available unit cost versions
        $versions = $this->tariffService->getAvailableVersions();
        
        $finalTariff->load(['costReference', 'tariffClass', 'unitCostCalculation']);
        
        return view('final-tariffs.edit', compact(
            'finalTariff',
            'costReferences',
            'tariffClasses',
            'versions'
        ));
    }

    public function update(Request $request, FinalTariff $finalTariff)
    {
        if ($finalTariff->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'cost_reference_id' => 'required|exists:cost_references,id',
            'tariff_class_id' => 'nullable|exists:tariff_classes,id',
            'unit_cost_calculation_id' => 'required|exists:unit_cost_calculations,id',
            'sk_number' => 'required|string|max:100',
            'base_unit_cost' => 'required|numeric|min:0',
            'margin_percentage' => 'required|numeric|min:0|max:10',
            'jasa_sarana' => 'nullable|numeric|min:0',
            'jasa_pelayanan' => 'nullable|numeric|min:0',
            'final_tariff_price' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'expired_date' => 'nullable|date|after:effective_date',
        ]);

        // Validate unit cost calculation belongs to hospital
        $unitCost = UnitCostCalculation::findOrFail($validated['unit_cost_calculation_id']);
        if ($unitCost->hospital_id !== hospital('id')) {
            abort(404);
        }

        // Validate base unit cost matches unit cost calculation
        if (abs($unitCost->total_unit_cost - $validated['base_unit_cost']) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Base unit cost tidak sesuai dengan unit cost calculation.');
        }

        // Validate final tariff price calculation
        $calculatedPrice = $this->tariffService->calculateTariff(
            $validated['base_unit_cost'],
            $validated['margin_percentage'],
            $validated['jasa_sarana'] ?? 0,
            $validated['jasa_pelayanan'] ?? 0
        );

        if (abs($calculatedPrice - $validated['final_tariff_price']) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Final tariff price tidak sesuai dengan perhitungan. Harus: ' . number_format($calculatedPrice, 2));
        }

        // Validate final tariff price >= base unit cost
        if ($validated['final_tariff_price'] < $validated['base_unit_cost']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Final tariff price harus lebih besar atau sama dengan base unit cost.');
        }

        // Check for overlapping tariffs (exclude current tariff)
        if ($this->tariffService->hasOverlappingTariff(
            $validated['cost_reference_id'],
            $validated['effective_date'],
            $validated['expired_date'] ?? null,
            $finalTariff->id
        )) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Effective date overlap dengan tariff yang sudah ada untuk service ini.');
        }

        $finalTariff->update($validated);

        return redirect()->route('final-tariffs.index')
            ->with('success', 'Final tariff berhasil diperbarui.');
    }

    public function destroy(FinalTariff $finalTariff)
    {
        if ($finalTariff->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Check if tariff is currently active
        if ($finalTariff->isActive()) {
            return redirect()->route('final-tariffs.index')
                ->with('error', 'Tariff yang sedang aktif tidak dapat dihapus. Silakan set expired date terlebih dahulu.');
        }
        
        $finalTariff->delete();

        return redirect()->route('final-tariffs.index')
            ->with('success', 'Final tariff berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $tariffClassId = $request->get('tariff_class_id');
        $skNumber = $request->get('sk_number');
        $effectiveDateFrom = $request->get('effective_date_from');
        $effectiveDateTo = $request->get('effective_date_to');
        $showExpired = $request->get('show_expired', false);
        
        $query = FinalTariff::where('hospital_id', hospital('id'))
            ->with(['costReference', 'tariffClass', 'unitCostCalculation']);
        
        if ($search) {
            $query->whereHas('costReference', function($q) use ($search) {
                $q->where('service_code', 'LIKE', "%{$search}%")
                  ->orWhere('service_description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($tariffClassId) {
            $query->where('tariff_class_id', $tariffClassId);
        }
        
        if ($skNumber) {
            $query->where('sk_number', 'LIKE', "%{$skNumber}%");
        }
        
        if ($effectiveDateFrom) {
            $query->where('effective_date', '>=', $effectiveDateFrom);
        }
        
        if ($effectiveDateTo) {
            $query->where('effective_date', '<=', $effectiveDateTo);
        }
        
        if (!$showExpired) {
            $query->active();
        }
        
        $data = $query->orderBy('effective_date', 'desc')
            ->orderBy('cost_reference_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Final Tariffs');

        // Headers
        $headers = [
            'Service Code',
            'Service Description',
            'Tariff Class',
            'SK Number',
            'Base Unit Cost',
            'Margin %',
            'Jasa Sarana',
            'Jasa Pelayanan',
            'Final Tariff Price',
            'Effective Date',
            'Expired Date',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Data
        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->costReference->service_code ?? '-',
                    $item->costReference->service_description ?? '-',
                    $item->tariffClass->name ?? '-',
                    $item->sk_number,
                    $item->base_unit_cost,
                    $item->margin_percentage * 100, // Convert to percentage
                    $item->jasa_sarana,
                    $item->jasa_pelayanan,
                    $item->final_tariff_price,
                    $item->effective_date->format('Y-m-d'),
                    $item->expired_date ? $item->expired_date->format('Y-m-d') : '-',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format header row
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Format number columns
        $sheet->getStyle('E2:I' . ($data->count() + 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $filename = 'final_tariffs_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

