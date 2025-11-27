<?php

namespace App\Http\Controllers\Pathway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClinicalPathway;

class PathwayApprovalController extends Controller
{
    public function show(ClinicalPathway $pathway)
    {
        return view('pathways.approval', [
            'title' => 'Pathway Approval',
            'message' => 'Fitur untuk review dan approval pathway oleh Medical Committee sedang dalam tahap pengembangan.',
            'pathway' => $pathway
        ]);
    }
}

