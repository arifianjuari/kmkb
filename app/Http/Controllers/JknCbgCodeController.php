<?php

namespace App\Http\Controllers;

use App\Models\JknCbgCode;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class JknCbgCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = JknCbgCode::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        $cbgCodes = $query->orderBy('code')->paginate(20)->appends($request->query());
        
        return view('jkn_cbg_codes.index', compact('cbgCodes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jkn_cbg_codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:jkn_cbg_codes',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'service_type' => 'nullable|in:Rawat Inap,Rawat Jalan',
                'severity_level' => 'nullable|integer|min:1|max:3',
                'grouping_version' => 'nullable|string|max:50',
                'tariff' => 'required|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $cbgCode = JknCbgCode::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'service_type' => $request->service_type,
                'severity_level' => $request->severity_level,
                'grouping_version' => $request->grouping_version,
                'tariff' => $request->tariff,
                'is_active' => filter_var($request->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);

            return redirect()->route('jkn-cbg-codes.index')
                ->with('success', 'CBG Code berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan CBG Code: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JknCbgCode $jknCbgCode)
    {
        return view('jkn_cbg_codes.edit', compact('jknCbgCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JknCbgCode $jknCbgCode)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:jkn_cbg_codes,code,'.$jknCbgCode->id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'service_type' => 'nullable|in:Rawat Inap,Rawat Jalan',
                'severity_level' => 'nullable|integer|min:1|max:3',
                'grouping_version' => 'nullable|string|max:50',
                'tariff' => 'required|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $jknCbgCode->update([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'service_type' => $request->service_type,
                'severity_level' => $request->severity_level,
                'grouping_version' => $request->grouping_version,
                'tariff' => $request->tariff,
                'is_active' => filter_var($request->is_active ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);

            return redirect()->route('jkn-cbg-codes.index')
                ->with('success', 'CBG Code berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui CBG Code: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JknCbgCode $jknCbgCode)
    {
        $jknCbgCode->delete();

        return redirect()->route('jkn-cbg-codes.index')
            ->with('success', 'CBG Code berhasil dihapus.');
    }

    /**
     * Search for CBG codes (for autocomplete in patient case form)
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $cbgCodes = JknCbgCode::active()
            ->where('code', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->orderBy('code')
            ->limit(20)
            ->get();

        return response()->json($cbgCodes);
    }

    /**
     * Get tariff for a specific CBG code (for auto-filling in patient case form)
     */
    public function getTariff(Request $request)
    {
        $code = $request->get('code');
        
        $cbgCode = JknCbgCode::active()
            ->where('code', $code)
            ->first();

        if ($cbgCode) {
            return response()->json([
                'tariff' => $cbgCode->tariff,
                'name' => $cbgCode->name
            ]);
        }

        return response()->json([
            'tariff' => null,
            'name' => null
        ]);
    }

    /**
     * Base Tariff Reference view
     */
    public function baseTariff(Request $request)
    {
        $search = $request->get('search');
        $serviceType = $request->get('service_type');
        $isActive = $request->get('is_active');
        
        $query = JknCbgCode::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        if ($serviceType) {
            $query->where('service_type', $serviceType);
        }
        
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }
        
        $cbgCodes = $query->orderBy('code')->paginate(20)->appends($request->query());
        
        // Get statistics
        $totalCodes = JknCbgCode::count();
        $activeCodes = JknCbgCode::where('is_active', true)->count();
        $totalTariff = JknCbgCode::where('is_active', true)->sum('tariff');
        $avgTariff = JknCbgCode::where('is_active', true)->avg('tariff');
        
        return view('setup.jkn-cbg-codes.base-tariff', compact(
            'cbgCodes',
            'search',
            'serviceType',
            'isActive',
            'totalCodes',
            'activeCodes',
            'totalTariff',
            'avgTariff'
        ));
    }

    /**
     * Export CBG codes to Excel
     */
    public function export(Request $request)
    {
        $search = $request->get('search');
        
        $query = JknCbgCode::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('code')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Code', 'Name', 'Description', 'Service Type', 'Severity Level', 'Grouping Version', 'Tariff', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->code,
                    $item->name,
                    $item->description ?? '',
                    $item->service_type ?? '',
                    $item->severity_level ?? '',
                    $item->grouping_version ?? '',
                    $item->tariff,
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'jkn_cbg_codes_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import CBG codes from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                return redirect()->back()->with('error', 'Excel import belum tersedia. Mohon instal paket phpoffice/phpspreadsheet lalu coba lagi.');
            }

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            $imported = 0;
            $updated = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows
                
                try {
                    $code = trim($row[0] ?? '');
                    $name = trim($row[1] ?? '');
                    $description = trim($row[2] ?? '');
                    $serviceType = trim($row[3] ?? '');
                    $severityLevel = !empty($row[4]) ? (int)$row[4] : null;
                    $groupingVersion = trim($row[5] ?? '');
                    $tariff = floatval($row[6] ?? 0);
                    $isActive = isset($row[7]) ? (strtolower(trim($row[7])) === 'yes' || $row[7] === '1' || $row[7] === 1) : true;
                    
                    if (empty($code) || empty($name)) {
                        $errors[] = "Baris " . ($index + 2) . ": Code dan Name wajib diisi";
                        continue;
                    }
                    
                    if ($tariff < 0) {
                        $errors[] = "Baris " . ($index + 2) . ": Tariff tidak boleh negatif";
                        continue;
                    }
                    
                    if ($severityLevel !== null && ($severityLevel < 1 || $severityLevel > 3)) {
                        $errors[] = "Baris " . ($index + 2) . ": Severity Level harus antara 1-3";
                        continue;
                    }
                    
                    if ($serviceType && !in_array($serviceType, ['Rawat Inap', 'Rawat Jalan'])) {
                        $errors[] = "Baris " . ($index + 2) . ": Service Type harus 'Rawat Inap' atau 'Rawat Jalan'";
                        continue;
                    }
                    
                    $existing = JknCbgCode::where('code', $code)->first();
                    
                    if ($existing) {
                        $existing->update([
                            'name' => $name,
                            'description' => $description ?: null,
                            'service_type' => $serviceType ?: null,
                            'severity_level' => $severityLevel,
                            'grouping_version' => $groupingVersion ?: null,
                            'tariff' => $tariff,
                            'is_active' => $isActive,
                        ]);
                        $updated++;
                    } else {
                        JknCbgCode::create([
                            'code' => $code,
                            'name' => $name,
                            'description' => $description ?: null,
                            'service_type' => $serviceType ?: null,
                            'severity_level' => $severityLevel,
                            'grouping_version' => $groupingVersion ?: null,
                            'tariff' => $tariff,
                            'is_active' => $isActive,
                        ]);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            $message = "Berhasil mengimpor {$imported} data baru dan memperbarui {$updated} data.";
            if (count($errors) > 0) {
                $message .= " Terdapat " . count($errors) . " error: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " dan " . (count($errors) - 5) . " error lainnya.";
                }
            }
            
            return redirect()->route('jkn-cbg-codes.index')
                ->with('success', $message)
                ->with('errors', $errors);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error membaca file: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Bulk delete CBG codes
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:jkn_cbg_codes,id',
        ]);

        try {
            $deleted = JknCbgCode::whereIn('id', $request->ids)->delete();

            return redirect()->route('jkn-cbg-codes.index')
                ->with('success', "Berhasil menghapus {$deleted} CBG code(s).");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus CBG codes: ' . $e->getMessage());
        }
    }
}
