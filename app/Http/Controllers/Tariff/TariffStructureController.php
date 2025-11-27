<?php

namespace App\Http\Controllers\Tariff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TariffStructureController extends Controller
{
    public function index()
    {
        return view('tariffs.structure', [
            'title' => 'Tariff Structure Setup',
            'message' => 'Fitur untuk mengatur struktur tarif (Jasa Sarana, Jasa Pelayanan, dll) sedang dalam tahap pengembangan.'
        ]);
    }
}

