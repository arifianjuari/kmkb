<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TariffAnalyticsController extends Controller
{
    public function index()
    {
        return view('analytics.tariff-analytics', [
            'title' => 'Tariff Analytics',
            'message' => 'Fitur untuk analitik tarif (perbandingan internal vs INA-CBG, margin analysis) sedang dalam tahap pengembangan.'
        ]);
    }
}





