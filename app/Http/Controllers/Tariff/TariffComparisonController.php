<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TariffComparisonController extends Controller
{
    public function index()
    {
        return view('tariffs.comparison', [
            'title' => 'Tariff vs INA-CBG Comparison',
            'message' => 'Fitur untuk membandingkan tarif internal dengan INA-CBG sedang dalam tahap pengembangan.'
        ]);
    }
}








