<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BlocksObserver;
use App\Models\TariffClass;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TariffClassController extends Controller
{
    use BlocksObserver;
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
        $this->blockObserver('membuat');
        return view('tariff-classes.create');
    }

    public function store(Request $request)
    {
        $this->blockObserver('membuat');
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
        $this->blockObserver('mengubah');
        if ($tariffClass->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('tariff-classes.edit', compact('tariffClass'));
    }

    public function update(Request $request, TariffClass $tariffClass)
    {
        $this->blockObserver('mengubah');
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
        $this->blockObserver('menghapus');
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

    /**
     * Download template for importing tariff classes.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Code', 'Name', 'Description', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Add sample data
        $sheet->setCellValue('A2', 'KELAS-1');
        $sheet->setCellValue('B2', 'Kelas 1');
        $sheet->setCellValue('C2', 'Kelas perawatan 1');
        $sheet->setCellValue('D2', 'Yes');

        $sheet->setCellValue('A3', 'KELAS-2');
        $sheet->setCellValue('B3', 'Kelas 2');
        $sheet->setCellValue('C3', 'Kelas perawatan 2');
        $sheet->setCellValue('D3', 'Yes');

        // Add notes
        $sheet->setCellValue('F1', 'PETUNJUK:');
        $sheet->setCellValue('F2', '- Code: Kode charge class (wajib, unik)');
        $sheet->setCellValue('F3', '- Name: Nama charge class (wajib)');
        $sheet->setCellValue('F4', '- Description: Deskripsi (opsional)');
        $sheet->setCellValue('F5', '- Is Active: Yes/No (default: Yes)');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'tariff_class_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import tariff classes from Excel.
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

            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) {
                    continue; // Skip empty rows
                }

                $rowNumber = $index + 2;

                try {
                    $code = trim($row[0]);
                    $name = trim($row[1]);
                    $description = !empty($row[2]) ? trim($row[2]) : null;
                    $isActive = empty($row[3]) || strtolower(trim($row[3])) === 'yes';

                    // Check if tariff class already exists by code
                    $tariffClass = TariffClass::where('hospital_id', hospital('id'))
                        ->where('code', $code)
                        ->first();

                    if ($tariffClass) {
                        $tariffClass->update([
                            'name' => $name,
                            'description' => $description,
                            'is_active' => $isActive,
                        ]);
                        $updatedCount++;
                    } else {
                        TariffClass::create([
                            'hospital_id' => hospital('id'),
                            'code' => $code,
                            'name' => $name,
                            'description' => $description,
                            'is_active' => $isActive,
                        ]);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            if (count($errors) > 0) {
                return redirect()->route('tariff-classes.index')
                    ->with('warning', "Import selesai dengan catatan. {$successCount} data baru, {$updatedCount} data diupdate. " . count($errors) . " baris gagal: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : ''));
            }

            return redirect()->route('tariff-classes.index')
                ->with('success', "Import berhasil! {$successCount} data baru, {$updatedCount} data diupdate.");

        } catch (\Exception $e) {
            return redirect()->route('tariff-classes.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
