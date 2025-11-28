<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CostCenterController extends Controller
{
    use BlocksObserver;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');
        $isActive = $request->get('is_active');
        
        $query = CostCenter::where('hospital_id', hospital('id'));
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $costCenters = $query->with('parent')->latest()->paginate(15)->appends($request->query());
        
        return view('cost-centers.index', compact('costCenters', 'search', 'type', 'isActive'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->blockObserver('membuat');
        
        $parents = CostCenter::where('hospital_id', hospital('id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('cost-centers.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->blockObserver('membuat');
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'type' => 'required|in:support,revenue',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'is_active' => 'boolean',
        ]);

        // Ensure parent belongs to same hospital
        if ($validated['parent_id']) {
            $parent = CostCenter::where('id', $validated['parent_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent cost center tidak valid.'])->withInput();
            }
        }

        CostCenter::create(array_merge($validated, [
            'hospital_id' => hospital('id'),
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('cost-centers.index')
            ->with('success', 'Cost center berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CostCenter $costCenter)
    {
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $costCenter->load(['parent', 'children', 'costReferences', 'glExpenses']);
        
        return view('cost-centers.show', compact('costCenter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CostCenter $costCenter)
    {
        $this->blockObserver('mengubah');
        
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $parents = CostCenter::where('hospital_id', hospital('id'))
            ->where('id', '!=', $costCenter->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('cost-centers.edit', compact('costCenter', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CostCenter $costCenter)
    {
        $this->blockObserver('mengubah');
        
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'type' => 'required|in:support,revenue',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $costCenter->id) {
            return back()->withErrors(['parent_id' => 'Cost center tidak dapat menjadi parent dari dirinya sendiri.'])->withInput();
        }

        // Ensure parent belongs to same hospital
        if ($validated['parent_id']) {
            $parent = CostCenter::where('id', $validated['parent_id'])
                ->where('hospital_id', hospital('id'))
                ->first();
            
            if (!$parent) {
                return back()->withErrors(['parent_id' => 'Parent cost center tidak valid.'])->withInput();
            }
        }

        $costCenter->update(array_merge($validated, [
            'is_active' => $request->has('is_active') ? true : false,
        ]));

        return redirect()->route('cost-centers.index')
            ->with('success', 'Cost center berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostCenter $costCenter)
    {
        $this->blockObserver('menghapus');
        
        if ($costCenter->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        // Check if cost center is being used
        if ($costCenter->costReferences()->count() > 0) {
            return redirect()->route('cost-centers.index')
                ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan di Cost References.');
        }
        
        if ($costCenter->glExpenses()->count() > 0) {
            return redirect()->route('cost-centers.index')
                ->with('error', 'Cost center tidak dapat dihapus karena masih digunakan di GL Expenses.');
        }
        
        if ($costCenter->children()->count() > 0) {
            return redirect()->route('cost-centers.index')
                ->with('error', 'Cost center tidak dapat dihapus karena masih memiliki child cost centers.');
        }
        
        $costCenter->delete();

        return redirect()->route('cost-centers.index')
            ->with('success', 'Cost center berhasil dihapus.');
    }

    /**
     * Export cost centers to Excel.
     */
    public function export(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');
        $isActive = $request->get('is_active');
        
        $query = CostCenter::where('hospital_id', hospital('id'))
            ->with('parent')
            ->orderBy('code');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        
        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['Code', 'Name', 'Type', 'Parent', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Rows
        if ($data->count() > 0) {
            $rows = $data->map(function ($item) {
                return [
                    $item->code,
                    $item->name,
                    $item->type == 'support' ? 'Support' : 'Revenue',
                    $item->parent ? $item->parent->name : '-',
                    $item->is_active ? 'Yes' : 'No',
                ];
            })->toArray();
            $sheet->fromArray($rows, null, 'A2');
        }

        // Autosize
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'cost_centers_' . hospital('id') . '_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}



