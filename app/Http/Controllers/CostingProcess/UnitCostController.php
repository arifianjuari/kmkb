<?php

namespace App\Http\Controllers\CostingProcess;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitCostController extends Controller
{
    public function calculate()
    {
        return view('costing-process.unit-cost.calculate', [
            'title' => 'Calculate Unit Cost',
            'message' => 'Fitur untuk menghitung unit cost per service sedang dalam tahap pengembangan.'
        ]);
    }

    public function results()
    {
        return view('costing-process.unit-cost.results', [
            'title' => 'Unit Cost Results',
            'message' => 'Fitur untuk melihat hasil perhitungan unit cost sedang dalam tahap pengembangan.'
        ]);
    }

    public function compare()
    {
        return view('costing-process.unit-cost.compare', [
            'title' => 'Compare Unit Cost Versions',
            'message' => 'Fitur untuk membandingkan versi unit cost yang berbeda sedang dalam tahap pengembangan.'
        ]);
    }
}

