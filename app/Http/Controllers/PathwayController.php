<?php

namespace App\Http\Controllers;

use App\Models\ClinicalPathway;
use App\Models\PathwayStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class PathwayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = request('q');
        $query = ClinicalPathway::where('hospital_id', hospital('id'))
            ->with(['creator', 'steps'])
            ->latest();

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('diagnosis_code', 'like', "%$q%")
                    ->orWhere('version', 'like', "%$q%")
                    ->orWhere('status', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        $pathways = $query->paginate(10)->withQueryString();
        return view('pathways.index', compact('pathways', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pathways.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'diagnosis_code' => 'required|string|max:50',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $pathway = new ClinicalPathway();
            $pathway->name = $request->name;
            $pathway->description = $request->description;
            $pathway->diagnosis_code = $request->diagnosis_code;
            $pathway->version = $request->version;
            $pathway->effective_date = $request->effective_date;
            $pathway->status = $request->status;
            $pathway->created_by = Auth::id();
            $pathway->hospital_id = hospital('id');
            $pathway->save();

            DB::commit();
            
            return redirect()->route('pathways.index')
                ->with('success', 'Clinical pathway created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create clinical pathway: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function show(ClinicalPathway $pathway)
    {
        // Ensure the pathway belongs to the current hospital
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $pathway->load(['steps', 'creator']);
        return view('pathways.show', compact('pathway'));
    }

    /**
     * Export the specified clinical pathway as DOCX.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function exportDocx(ClinicalPathway $pathway)
    {
        // Ensure the pathway belongs to the current hospital
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }

        // Load the steps with the pathway
        $pathway->load(['steps', 'creator']);

        // Create new Word document
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        // Create a section
        $section = $phpWord->addSection([
            'marginTop' => 600,
            'marginRight' => 600,
            'marginLeft' => 600,
            'marginBottom' => 600,
        ]);

        // Minimal mode to isolate content-related issues: /export-docx?minimal=1
        $minimal = request()->boolean('minimal');
        if ($minimal) {
            $section->addTitle('Clinical Pathway', 1);
            $section->addText($this->sanitizeForWord($pathway->name));
        } else {
            // Add title
            $section->addTitle('Clinical Pathway Details', 1);

        // Add pathway information table
        $section->addTextBreak(1);
        $section->addText('Pathway Information', ['bold' => true, 'size' => 14]);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);

        $table->addRow();
        $table->addCell(2000)->addText('Name');
        $table->addCell(5000)->addText($this->sanitizeForWord($pathway->name));

        $table->addRow();
        $table->addCell(2000)->addText('Diagnosis Code');
        $table->addCell(5000)->addText($this->sanitizeForWord($pathway->diagnosis_code));

        $table->addRow();
        $table->addCell(2000)->addText('Version');
        $table->addCell(5000)->addText($this->sanitizeForWord($pathway->version));

        $table->addRow();
        $table->addCell(2000)->addText('Effective Date');
        $table->addCell(5000)->addText($this->sanitizeForWord($pathway->effective_date->format('d M Y')));

        $table->addRow();
        $table->addCell(2000)->addText('Status');
        $table->addCell(5000)->addText($this->sanitizeForWord(ucfirst($pathway->status)));

        $table->addRow();
        $table->addCell(2000)->addText('Created By');
        $table->addCell(5000)->addText($this->sanitizeForWord($pathway->creator->name ?? 'N/A'));

        $table->addRow();
        $table->addCell(2000)->addText('Description');
        $table->addCell(5000)->addText($this->sanitizeForWord($pathway->description));

        // Add steps table
        $section->addTextBreak(2);
        $section->addText('Pathway Steps', ['bold' => true, 'size' => 14]);

        if ($pathway->steps->count() > 0) {
            $stepsTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
            ]);

            // Add table headers
            $stepsTable->addRow();
            $stepsTable->addCell(1000)->addText('Day', ['bold' => true]);
            $stepsTable->addCell(2000)->addText('Activity', ['bold' => true]);
            $stepsTable->addCell(3000)->addText('Description', ['bold' => true]);
            $stepsTable->addCell(2000)->addText('Criteria', ['bold' => true]);
            $stepsTable->addCell(1500)->addText('Standard Cost', ['bold' => true]);
            $stepsTable->addCell(1500)->addText('Total Cost', ['bold' => true]);

            // Add steps data
            foreach ($pathway->steps->sortBy('step_order') as $step) {
                $stepsTable->addRow();
                $stepsTable->addCell(1000)->addText($step->step_order);
                $stepsTable->addCell(2000)->addText($this->sanitizeForWord($step->service_code));
                $stepsTable->addCell(3000)->addText($this->sanitizeForWord($step->description));
                $stepsTable->addCell(2000)->addText($this->sanitizeForWord($step->criteria));
                $stepsTable->addCell(1500)->addText('Rp' . number_format($step->estimated_cost, 0, ',', '.'));
                $stepsTable->addCell(1500)->addText('Rp' . number_format(($step->estimated_cost ?? 0) * $step->quantity, 0, ',', '.'));
            }

            // Add total cost row
            $totalCost = $pathway->steps->sum(function($step) {
                return ($step->estimated_cost ?? 0) * $step->quantity;
            });

            $stepsTable->addRow();
            // Use paragraph style for alignment instead of unsupported cell style key
            $stepsTable->addCell(7000, ['gridSpan' => 5])->addText('Total Standard Cost:', ['bold' => true], ['alignment' => 'right']);
            $stepsTable->addCell(1500)->addText('Rp' . number_format($totalCost, 0, ',', '.'), ['bold' => true]);
        } else {
            $section->addText('No steps defined for this pathway yet.');
        }
        }

        // Generate a safe ASCII filename (avoid special chars and overly long names)
        $baseName = preg_replace('/[^A-Za-z0-9_\-]+/', '_', strtolower((string) $pathway->name));
        if ($baseName === '' || $baseName === null) {
            $baseName = 'clinical_pathway';
        }
        $baseName = substr($baseName, 0, 100);
        $versionSafe = preg_replace('/[^A-Za-z0-9_.-]/', '', (string) $pathway->version);
        $versionSafe = substr($versionSafe, 0, 20);
        $fileName = 'clinical_pathway_' . $baseName . '_v' . $versionSafe . '.docx';

        // Create a writer
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        // Save to system temporary directory to avoid cloud sync interference
        $dir = sys_get_temp_dir();
        $tmpName = 'cp-' . Str::uuid()->toString() . '.docx';
        $fullPath = $dir . DIRECTORY_SEPARATOR . $tmpName;

        // Ensure no active output buffers before writing/serving files
        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
        }

        try {
            $writer->save($fullPath);
        } catch (\Throwable $e) {
            Log::error('DOCX export save failed', ['error' => $e->getMessage()]);
            abort(500, 'Failed to generate DOCX export.');
        }

        // Debug: log size and header to verify a valid ZIP (PK.. = 504b0304)
        $size = @filesize($fullPath);
        $first4 = @bin2hex(@file_get_contents($fullPath, false, null, 0, 4));
        $sha256 = @hash_file('sha256', $fullPath) ?: null;
        Log::info('DOCX export created', ['file' => $fullPath, 'size' => $size, 'first4' => $first4, 'sha256' => $sha256]);

        // Validate ZIP structure minimally
        try {
            $zip = new \ZipArchive();
            $openResult = $zip->open($fullPath);
            if ($openResult === true) {
                $hasDocXml = ($zip->locateName('word/document.xml') !== false);
                $hasContentTypes = ($zip->locateName('[Content_Types].xml') !== false);
                $zip->close();
                Log::info('DOCX zip check', ['hasDocumentXml' => $hasDocXml, 'hasContentTypes' => $hasContentTypes]);
            } else {
                Log::error('DOCX zip open failed', ['code' => $openResult]);
            }
        } catch (\Throwable $e) {
            Log::error('DOCX zip validation error', ['error' => $e->getMessage()]);
        }

        // Prepare headers including content length
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Description' => 'File Transfer',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        if (is_file($fullPath)) {
            $headers['Content-Length'] = (string) filesize($fullPath);
        }

        return response()->download($fullPath, $fileName, $headers)->deleteFileAfterSend(true);
    }

    /**
     * Export the specified clinical pathway as PDF.
     */
    public function exportPdf(ClinicalPathway $pathway)
    {
        // Ensure the pathway belongs to the current hospital
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }

        $pathway->load(['steps', 'creator']);

        // Safe filename
        $baseName = preg_replace('/[^A-Za-z0-9_\-]+/', '_', strtolower((string) $pathway->name));
        if ($baseName === '' || $baseName === null) {
            $baseName = 'clinical_pathway';
        }
        $baseName = substr($baseName, 0, 100);
        $versionSafe = preg_replace('/[^A-Za-z0-9_.-]/', '', (string) $pathway->version);
        $versionSafe = substr($versionSafe, 0, 20);
        $fileName = 'clinical_pathway_' . $baseName . '_v' . $versionSafe . '.pdf';

        // Render view to PDF
        $pdf = Pdf::loadView('pathways.pdf', [
            'pathway' => $pathway,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($fileName);
    }

    /**
     * Remove characters that are invalid in WordprocessingML/XML to prevent DOCX corruption.
     */
    private function sanitizeForWord($text): string
    {
        if ($text === null) {
            return '';
        }
        // Ensure string
        $text = (string) $text;
        // Normalize to UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }
        // Strip control characters not allowed in XML (keep tab, newline, carriage return)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
        return $text;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function edit(ClinicalPathway $pathway)
    {
        // Ensure the pathway belongs to the current hospital
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        return view('pathways.edit', compact('pathway'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClinicalPathway $pathway)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'diagnosis_code' => 'required|string|max:50',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
        ]);

        $pathway->update($request->all());

        return redirect()->route('pathways.index')
            ->with('success', 'Clinical pathway updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClinicalPathway $pathway)
    {
        // Ensure the pathway belongs to the current hospital before deleting
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $pathway->delete();

        return redirect()->route('pathways.index')
            ->with('success', 'Clinical pathway deleted successfully.');
    }

    /**
     * Show the pathway builder interface.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function builder(ClinicalPathway $pathway)
    {
        $pathway->load('steps');
        $costReferences = \App\Models\CostReference::where('hospital_id', hospital('id'))->get();
        return view('pathways.builder', compact('pathway', 'costReferences'));
    }
    
    /**
     * Duplicate the specified pathway along with its steps.
     * New record will be set to draft and version incremented (simple X.Y.Z -> X.Y.(Z+1)).
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function duplicate(ClinicalPathway $pathway)
    {
        DB::beginTransaction();
        try {
            $pathway->load('steps');

            // Duplicate pathway
            $newPathway = $pathway->replicate();
            $newPathway->name = $pathway->name . ' (Copy)';
            $newPathway->version = $this->generateNewVersion($pathway->version);
            $newPathway->status = 'draft';
            $newPathway->created_by = Auth::id();
            $newPathway->hospital_id = hospital('id');
            $newPathway->save();

            // Duplicate steps
            foreach ($pathway->steps as $step) {
                $newStep = $step->replicate();
                $newStep->clinical_pathway_id = $newPathway->id;
                $newStep->save();
            }

            DB::commit();

            return redirect()->route('pathways.edit', $newPathway)
                ->with('success', 'Clinical pathway duplicated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to duplicate clinical pathway: ' . $e->getMessage());
        }
    }

    /**
     * Create a new version of the specified pathway (increment version, keep name).
     * New record will be set to draft and steps are replicated.
     *
     * @param  \App\Models\ClinicalPathway  $pathway
     * @return \Illuminate\Http\Response
     */
    public function newVersion(Request $request, ClinicalPathway $pathway)
    {
        DB::beginTransaction();
        try {
            $pathway->load('steps');

            $newPathway = $pathway->replicate();
            // Keep the same name, increment version per request, set status to draft
            $bump = in_array($request->input('bump'), ['major','minor','patch']) ? $request->input('bump') : 'patch';
            $newPathway->version = $this->generateNewVersion($pathway->version, $bump);
            $newPathway->status = 'draft';
            $newPathway->created_by = Auth::id();
            // Optionally adjust effective_date (keep as-is for now)
            $newPathway->save();

            foreach ($pathway->steps as $step) {
                $newStep = $step->replicate();
                $newStep->clinical_pathway_id = $newPathway->id;
                $newStep->save();
            }

            DB::commit();

            return redirect()->route('pathways.edit', $newPathway)
                ->with('success', 'New pathway version created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create new version: ' . $e->getMessage());
        }
    }

    /**
     * Generate a new version number based on current version string.
     * If format is X.Y.Z, increment Z. Otherwise append '-copy'.
     */
    private function generateNewVersion(string $currentVersion, string $bump = 'patch'): string
    {
        if (preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $currentVersion, $m)) {
            $major = (int)$m[1];
            $minor = (int)$m[2];
            $patch = (int)$m[3];
            if ($bump === 'major') {
                $major += 1; $minor = 0; $patch = 0;
            } elseif ($bump === 'minor') {
                $minor += 1; $patch = 0;
            } else { // patch
                $patch += 1;
            }
            return $major . '.' . $minor . '.' . $patch;
        }
        return $currentVersion . '-copy';
    }
}
