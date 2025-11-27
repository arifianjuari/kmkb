<?php

namespace App\Http\Controllers\PatientCase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientCase;

class CaseCostingController extends Controller
{
    public function show(PatientCase $case)
    {
        return view('cases.costing', [
            'title' => 'Case Costing',
            'message' => 'Fitur untuk melihat breakdown biaya kasus dan perbandingan dengan pathway estimate sedang dalam tahap pengembangan.',
            'case' => $case
        ]);
    }
}

