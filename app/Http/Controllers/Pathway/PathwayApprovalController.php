<?php

namespace App\Http\Controllers\Pathway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicalPathway;

class PathwayApprovalController extends Controller
{
    /**
     * Display approval index - list of pathways pending approval
     */
    public function index()
    {
        $q = request('q');
        $status = request('status', 'review'); // Default to review status
        
        $query = ClinicalPathway::where('hospital_id', hospital('id'))
            ->with(['creator'])
            ->withCount('steps')
            ->latest();

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('diagnosis_code', 'like', "%$q%")
                    ->orWhere('version', 'like', "%$q%");
            });
        }
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $pathways = $query->paginate(15)->withQueryString();
        
        return view('pathways.approval-index', compact('pathways', 'q', 'status'));
    }
    
    /**
     * Show approval page for specific pathway
     */
    public function show(ClinicalPathway $pathway)
    {
        if ($pathway->hospital_id !== hospital('id')) {
            abort(404);
        }
        
        $pathway->load(['steps.costReference', 'creator']);
        
        return view('pathways.approval', [
            'title' => 'Pathway Approval',
            'message' => 'Fitur untuk review dan approval pathway oleh Medical Committee sedang dalam tahap pengembangan.',
            'pathway' => $pathway
        ]);
    }
}

