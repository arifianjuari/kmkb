<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Services\SimrsService;
use Illuminate\Http\Request;

class SimrsIntegrationController extends Controller
{
    public function settings(SimrsService $simrsService)
    {
        return view('setup.simrs-integration.settings', [
            'simrsConnectionStatus' => $simrsService->connectionStatus(true),
        ]);
    }
}








