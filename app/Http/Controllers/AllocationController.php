<?php

namespace App\Http\Controllers;

use App\Services\AllocationService;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    protected $allocationService;

    public function __construct(AllocationService $allocationService)
    {
        $this->allocationService = $allocationService;
    }

    /**
     * Show the form for running allocation
     */
    public function runForm(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // Get allocation summary for preview
        $summary = $this->allocationService->getSummary(hospital('id'), $year, $month);
        
        return view('allocation.run', compact('summary', 'year', 'month'));
    }

    /**
     * Run allocation calculation
     */
    public function run(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $hospitalId = hospital('id');
        $year = $validated['year'];
        $month = $validated['month'];

        // Run allocation
        $result = $this->allocationService->runAllocation($hospitalId, $year, $month);

        if ($result['success']) {
            return redirect()->route('allocation-results.index', [
                'year' => $year,
                'month' => $month,
            ])->with('success', $result['message'] . '. Total alokasi: ' . number_format($result['total_allocated'], 2));
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message'])
                ->with('errors', $result['errors'])
                ->with('warnings', $result['warnings']);
        }
    }
}

