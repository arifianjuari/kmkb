<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllocationSummaryController extends Controller
{
    public function index()
    {
        return view('analytics.allocation-summary', [
            'title' => 'Allocation Summary',
            'message' => 'Fitur untuk melihat ringkasan hasil allocation per periode sedang dalam tahap pengembangan.'
        ]);
    }
}




