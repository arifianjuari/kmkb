<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StandardResourceUsage;
use App\Models\CostReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StandardResourceUsageController extends Controller
{
    /**
     * Get all BMHP for a specific service.
     *
     * @param  int  $serviceId
     * @return \Illuminate\Http\Response
     */
    public function getByService($serviceId)
    {
        try {
            $hospitalId = hospital('id');
            
            $standardResourceUsages = StandardResourceUsage::with(['bmhp'])
                ->where('hospital_id', $hospitalId)
                ->where('service_id', $serviceId)
                ->where('is_active', true)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $standardResourceUsages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load standard resource usages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync selected standard resource usages to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncToCostReferences(Request $request)
    {
        try {
            $items = $request->get('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada item yang dipilih untuk disinkronkan'
                ], 400);
            }
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }
            
            $user = auth()->user();
            
            // Check if user has hospital_id or a selected hospital context
            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki hospital yang terkait'
                ], 400);
            }
            
            $syncedCount = 0;
            
            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['code_service']) || !isset($item['nama_service']) || !isset($item['grand_total'])) {
                    Log::warning('Invalid item data for sync', ['item' => $item]);
                    continue;
                }
                
                // Check if item already exists in cost references
                $existing = CostReference::where('service_code', $item['code_service'])
                    ->where('hospital_id', $hospitalId)
                    ->first();
                
                // Prepare update/create data
                $data = [
                    'service_description' => $item['nama_service'],
                    'purchase_price' => $item['grand_total'],
                    'standard_cost' => $item['grand_total'],
                    'source' => 'Standard Resource Usage',
                ];
                
                // Add category if provided
                if (isset($item['category']) && !empty($item['category'])) {
                    $data['category'] = $item['category'];
                }
                
                if ($existing) {
                    // Update existing item
                    $existing->update($data);
                } else {
                    // Create new item
                    $data['service_code'] = $item['code_service'];
                    $data['unit'] = 'Tindakan';
                    $data['hospital_id'] = $hospitalId;
                    CostReference::create($data);
                }
                
                $syncedCount++;
            }
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount item berhasil disinkronkan ke Cost References"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing standard resource usages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error menyinkronkan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
