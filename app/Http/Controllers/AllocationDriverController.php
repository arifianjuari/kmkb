<?php

namespace App\Http\Controllers;

use App\Models\AllocationDriver;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AllocationDriverController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = AllocationDriver::where('hospital_id', hospital('id'));
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('unit_measurement', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        $allocationDrivers = $query->latest()->paginate(15)->appends($request->query());
        
        return view('allocation-drivers.index', compact('allocationDrivers', 'search'));
    }

    public function create()
    {
        return view('allocation-drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'unit_measurement' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        AllocationDriver::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
        ]));

        return redirect()->route('allocation-drivers.index')
            ->with('success', 'Allocation driver berhasil dibuat.');
    }

    public function show(AllocationDriver $allocationDriver)
    {
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $allocationDriver->load(['driverStatistics', 'allocationMaps']);
        
        return view('allocation-drivers.show', compact('allocationDriver'));
    }

    public function edit(AllocationDriver $allocationDriver)
    {
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('allocation-drivers.edit', compact('allocationDriver'));
    }

    public function update(Request $request, AllocationDriver $allocationDriver)
    {
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'unit_measurement' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $allocationDriver->update($validated);

        return redirect()->route('allocation-drivers.index')
            ->with('success', 'Allocation driver berhasil diperbarui.');
    }

    public function destroy(AllocationDriver $allocationDriver)
    {
        if ($allocationDriver->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        if ($allocationDriver->driverStatistics()->count() > 0) {
            return redirect()->route('allocation-drivers.index')
                ->with('error', 'Allocation driver tidak dapat dihapus karena masih digunakan di Driver Statistics.');
        }
        
        if ($allocationDriver->allocationMaps()->count() > 0) {
            return redirect()->route('allocation-drivers.index')
                ->with('error', 'Allocation driver tidak dapat dihapus karena masih digunakan di Allocation Maps.');
        }
        
        $allocationDriver->delete();

        return redirect()->route('allocation-drivers.index')
            ->with('success', 'Allocation driver berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        
        $query = AllocationDriver::where('hospital_id', hospital('id'))
            ->orderBy('name');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('unit_measurement', 'LIKE', "%{$search}%");
            });
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Name', 'Unit Measurement', 'Description'];
        $sheet->fromArray($headers, null, 'A1');

        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->name,
                    $item->unit_measurement,
                    $item->description ?? '-',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'allocation_drivers_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}


