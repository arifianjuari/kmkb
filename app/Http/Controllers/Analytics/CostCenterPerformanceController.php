<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CostCenterPerformanceController extends Controller
{
    public function index()
    {
        return view('analytics.cost-center-performance', [
            'title' => 'Cost Center Performance',
            'message' => 'Fitur untuk melihat performa cost center (pre/post allocation) sedang dalam tahap pengembangan.'
        ]);
    }
}






