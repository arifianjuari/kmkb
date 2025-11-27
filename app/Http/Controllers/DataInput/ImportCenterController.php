<?php

namespace App\Http\Controllers\DataInput;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportCenterController extends Controller
{
    public function index()
    {
        return view('data-input.import-center', [
            'title' => 'Import Center',
            'message' => 'Pusat import data untuk semua modul data input sedang dalam tahap pengembangan.'
        ]);
    }
}

