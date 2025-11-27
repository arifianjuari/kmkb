<?php

namespace App\Http\Controllers\CostingProcess;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreAllocationCheckController extends Controller
{
    public function glCompleteness()
    {
        return view('costing-process.pre-allocation-check.gl-completeness', [
            'title' => 'GL Completeness Check',
            'message' => 'Fitur untuk memeriksa kelengkapan data GL Expenses sedang dalam tahap pengembangan.'
        ]);
    }

    public function driverCompleteness()
    {
        return view('costing-process.pre-allocation-check.driver-completeness', [
            'title' => 'Driver Completeness Check',
            'message' => 'Fitur untuk memeriksa kelengkapan data Driver Statistics sedang dalam tahap pengembangan.'
        ]);
    }

    public function serviceVolumeCompleteness()
    {
        return view('costing-process.pre-allocation-check.service-volume-completeness', [
            'title' => 'Service Volume Completeness Check',
            'message' => 'Fitur untuk memeriksa kelengkapan data Service Volumes sedang dalam tahap pengembangan.'
        ]);
    }

    public function mappingValidation()
    {
        return view('costing-process.pre-allocation-check.mapping-validation', [
            'title' => 'Mapping Validation',
            'message' => 'Fitur untuk memvalidasi mapping cost center, expense category, dan allocation driver sedang dalam tahap pengembangan.'
        ]);
    }
}

