<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SimrsSyncController extends Controller
{
    /**
     * Show the sync page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('simrs.sync');
    }
    
    /**
     * Sync drugs from SIMRS
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncDrugs(Request $request)
    {
        try {
            // Get limit from request or default to 100
            $limit = $request->get('limit', 100);
            
            // Get hospital ID from request if provided
            $hospitalId = $request->get('hospital_id');
            
            // If no hospital ID provided in request, use the authenticated user's hospital ID
            if (!$hospitalId && auth()->user() && auth()->user()->hospital_id) {
                $hospitalId = auth()->user()->hospital_id;
            }
            
            // Prepare command parameters
            $commandParams = [
                '--limit' => $limit
            ];
            
            // Add hospital ID if available
            if ($hospitalId) {
                $commandParams['--hospital-id'] = $hospitalId;
            }
            
            // Run the sync command
            $exitCode = Artisan::call('cost-references:sync-from-simrs', $commandParams);
            
            // Get the output
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sinkronisasi obat berhasil dilakukan',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinkronisasi obat gagal',
                    'output' => $output
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in manual SIMRS sync: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
