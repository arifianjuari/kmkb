<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitCostSummaryController extends Controller
{
    public function index()
    {
        return view('analytics.unit-cost-summary', [
            'title' => 'Unit Cost Summary',
            'message' => 'Fitur untuk melihat ringkasan unit cost per department dan trend analysis sedang dalam tahap pengembangan.'
        ]);
    }
}





