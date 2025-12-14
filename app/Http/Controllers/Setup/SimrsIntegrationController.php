<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SimrsIntegrationController extends Controller
{
    public function settings()
    {
        return view('setup.simrs-integration.settings', [
            'title' => 'SIMRS Connection Settings',
            'message' => 'Fitur untuk mengatur koneksi database SIMRS sedang dalam tahap pengembangan.'
        ]);
    }
}








