<?php

namespace App\Http\Controllers;

use App\Models\TariffClass;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TariffClassController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $isActive = $request->get('is_active');
        
        $query = TariffClass::where('hospital_id', hospital('id'));
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $tariffClasses = $query->latest()->paginate(15)->appends($request->query());
        
        return view('tariff-classes.index', compact('tariffClasses', 'search', 'isActive'));
    }

    public function create()
    {
        return view('tariff-classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        TariffClass::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('tariff-classes.index')
            ->with('success', 'Tariff class berhasil dibuat.');
    }

    public function show(TariffClass $tariffClass)
    {
        if ($tariffClass->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $tariffClass->load(['serviceVolumes', 'finalTariffs']);
        
        return view('tariff-classes.show', compact('tariffClass'));
    }

    public function edit(TariffClass $tariffClass)
    {
        if ($tariffClass->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('tariff-classes.edit', compact('tariffClass'));
    }

    public function update(Request $request, TariffClass $tariffClass)
    {
        if ($tariffClass->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $tariffClass->update(array_merge($validated, [
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('tariff-classes.index')
            ->with('success', 'Tariff class berhasil diperbarui.');
    }

    public function destroy(TariffClass $tariffClass)
    {
        if ($tariffClass->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if ($tariffClass->serviceVolumes()->count() > 0) {
            return redirect()->route('tariff-classes.index')
                ->with('error', 'Tariff class tidak dapat dihapus karena masih digunakan di Service Volumes.');
        }
        
        if ($tariffClass->finalTariffs()->count() > 0) {
            return redirect()->route('tariff-classes.index')
                ->with('error', 'Tariff class tidak dapat dihapus karena masih digunakan di Final Tariffs.');
        }
        
        $tariffClass->delete();

        return redirect()->route('tariff-classes.index')
            ->with('success', 'Tariff class berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $isActive = $request->get('is_active');
        
        $query = TariffClass::where('hospital_id', hospital('id'))
            ->orderBy('code');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Code', 'Name', 'Description', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->code,
                    $item->name,
                    $item->description ?? '-',
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'tariff_classes_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}



