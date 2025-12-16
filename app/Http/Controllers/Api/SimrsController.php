<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SimrsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SimrsController extends Controller
{
    protected $simrsService;

    public function __construct(SimrsService $simrsService)
    {
        $this->simrsService = $simrsService;
    }

    /**
     * Test SIMRS database connection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            $connected = $this->simrsService->testConnection();
            
            if ($connected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully connected to SIMRS database'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to SIMRS database'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error testing SIMRS connection: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error testing SIMRS connection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get master barang (obat/BHP)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function masterBarang(Request $request)
    {
        try {
            $limit = $request->get('limit', 100);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getMasterBarang($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'count' => $result['total'],
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching master barang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching master barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tindakan rawat jalan data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tindakanRawatJalan(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getTindakanRawatJalan($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'total' => $result['total'],
                'count' => count($result['data']),
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tindakan rawat jalan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tindakan rawat jalan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tindakan rawat inap data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tindakanRawatInap(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getTindakanRawatInap($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'total' => $result['total'],
                'count' => count($result['data']),
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tindakan rawat inap: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tindakan rawat inap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get laboratorium data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function laboratorium(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getLaboratorium($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'total' => $result['total'],
                'count' => count($result['data']),
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching laboratorium: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching laboratorium: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get radiologi data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function radiologi(Request $request)
    {
        try {
            $limit = $request->get('limit', 30);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getRadiologi($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'count' => $result['total'],
                'total' => $result['total'],
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching radiologi data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching radiologi data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get jenis radiologi data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function jenisRadiologi(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $result = $this->simrsService->getJenisRadiologi($limit, $offset);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'count' => $result['total'],
                'total' => $result['total'],
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching jenis radiologi data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching jenis radiologi data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get operasi data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function operasi(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getOperasi($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'total' => $result['total'],
                'count' => $result['total'],
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching operasi data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching operasi data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kamar data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function kamar(Request $request)
    {
        try {
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $search = $request->get('search', null);
            $result = $this->simrsService->getKamar($limit, $offset, $search);
            
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'total' => $result['total'],
                'count' => $result['total'],
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching kamar data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching kamar data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all SIMRS data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allData()
    {
        try {
            $data = $this->simrsService->getAllSimrsData();
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all SIMRS data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching all SIMRS data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync selected master barang to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncMasterBarang(Request $request)
    {
        try {
            $items = $request->get('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $user = auth()->user();
            
            // Check if user has hospital_id or a selected hospital context
            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no associated hospital'
                ], 400);
            }
            
            $syncedCount = 0;
            
            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode_brng']) || !isset($item['nama_brng']) || !isset($item['harga_beli_dasar'])) {
                    Log::warning('Invalid item data for sync', ['item' => $item]);
                    continue;
                }
                
                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('simrs_kode_brng', $item['kode_brng'])
                    ->where('hospital_id', $hospitalId)
                    ->first();
                
                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama_brng'],
                        'purchase_price' => $item['harga_beli_dasar'],
                        'standard_cost' => isset($item['kelas3']) ? $item['kelas3'] : $item['harga_beli_dasar'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Master barang SIMRS → kategori barang
                        'category' => 'barang',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode_brng'],
                        'service_description' => $item['nama_brng'],
                        'purchase_price' => $item['harga_beli_dasar'],
                        'standard_cost' => isset($item['kelas3']) ? $item['kelas3'] : $item['harga_beli_dasar'],
                        'unit' => 'Barang',
                        'source' => 'SIMRS',
                        'hospital_id' => $hospitalId,
                        'simrs_kode_brng' => $item['kode_brng'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'barang',
                    ]);
                }
                
                $syncedCount++;
            }
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing master barang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing master barang: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync selected tindakan rawat jalan to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncTindakanRawatJalan(Request $request)
    {
        try {
            $items = $request->get('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $user = auth()->user();
            
            // Check if user has hospital_id or a selected hospital context
            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no associated hospital'
                ], 400);
            }
            
            $syncedCount = 0;
            
            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode']) || !isset($item['nama']) || !isset($item['harga'])) {
                    Log::warning('Invalid item data for sync', ['item' => $item]);
                    continue;
                }
                
                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('service_code', $item['kode'])
                    ->where('hospital_id', $hospitalId)
                    ->first();
                
                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Tindakan rawat jalan → kategori tindakan_rj
                        'category' => 'tindakan_rj',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode'],
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'unit' => 'Tindakan',
                        'source' => 'SIMRS - Tindakan Rawat Jalan',
                        'hospital_id' => $hospitalId,
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'tindakan_rj',
                    ]);
                }
                
                $syncedCount++;
            }
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing tindakan rawat jalan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing tindakan rawat jalan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync selected tindakan rawat inap to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncTindakanRawatInap(Request $request)
    {
        try {
            $items = $request->get('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $user = auth()->user();
            
            // Check if user has hospital_id or a selected hospital context
            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no associated hospital'
                ], 400);
            }
            
            $syncedCount = 0;
            
            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode']) || !isset($item['nama']) || !isset($item['harga'])) {
                    Log::warning('Invalid item data for sync', ['item' => $item]);
                    continue;
                }
                
                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('service_code', $item['kode'])
                    ->where('hospital_id', $hospitalId)
                    ->first();
                
                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Tindakan rawat inap → kategori tindakan_ri
                        'category' => 'tindakan_ri',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode'],
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'unit' => 'Tindakan',
                        'source' => 'SIMRS - Tindakan Rawat Inap',
                        'hospital_id' => $hospitalId,
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'tindakan_ri',
                    ]);
                }
                
                $syncedCount++;
            }
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing tindakan rawat inap: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing tindakan rawat inap: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync selected laboratorium to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncLaboratorium(Request $request)
    {
        try {
            $items = $request->get('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $user = auth()->user();
            
            // Check if user has hospital_id or a selected hospital context
            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no associated hospital'
                ], 400);
            }
            
            $syncedCount = 0;
            
            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode']) || !isset($item['nama']) || !isset($item['harga'])) {
                    Log::warning('Invalid item data for sync', ['item' => $item]);
                    continue;
                }
                
                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('service_code', $item['kode'])
                    ->where('hospital_id', $hospitalId)
                    ->first();
                
                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Laboratorium → kategori laboratorium
                        'category' => 'laboratorium',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode'],
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'unit' => 'Laboratorium',
                        'source' => 'SIMRS - Laboratorium',
                        'hospital_id' => $hospitalId,
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'laboratorium',
                    ]);
                }
                
                $syncedCount++;
            }
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing laboratorium: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing laboratorium: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync selected radiologi to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRadiologi(Request $request)
    {
        try {
            $items = $request->get('items', []);
            
            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $user = auth()->user();
            
            // Check if user has hospital_id or a selected hospital context
            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no associated hospital'
                ], 400);
            }
            
            $syncedCount = 0;
            
            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode']) || !isset($item['nama']) || !isset($item['harga'])) {
                    Log::warning('Invalid item data for sync', ['item' => $item]);
                    continue;
                }
                
                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('service_code', $item['kode'])
                    ->where('hospital_id', $hospitalId)
                    ->first();
                
                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Radiologi → kategori radiologi
                        'category' => 'radiologi',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode'],
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'unit' => 'Radiologi',
                        'source' => 'SIMRS - Radiologi',
                        'hospital_id' => $hospitalId,
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'radiologi',
                    ]);
                }
                
                $syncedCount++;
            }
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing radiologi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing radiologi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync selected operasi to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncOperasi(Request $request)
    {
        try {
            $items = $request->get('items', []);

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $hospitalId = session('hospital_id');
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hospital context is not set'
                ], 400);
            }

            $syncedCount = 0;

            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode']) || !isset($item['nama']) || !isset($item['harga'])) {
                    Log::warning('Invalid item data for sync (operasi)', ['item' => $item]);
                    continue;
                }

                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('service_code', $item['kode'])
                    ->where('hospital_id', $hospitalId)
                    ->first();

                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Operasi → kategori operasi
                        'category' => 'operasi',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode'],
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'unit' => 'Operasi',
                        'source' => 'SIMRS - Operasi',
                        'hospital_id' => $hospitalId,
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'operasi',
                    ]);
                }

                $syncedCount++;
            }

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing operasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing operasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync selected kamar to cost references
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncKamar(Request $request)
    {
        try {
            $items = $request->get('items', []);

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items provided for sync'
                ], 400);
            }

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $hospitalId = session('hospital_id', $user->hospital_id);
            if (!$hospitalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hospital context is not set'
                ], 400);
            }

            $syncedCount = 0;

            foreach ($items as $item) {
                // Validate required fields
                if (!isset($item['kode']) || !isset($item['nama']) || !isset($item['harga'])) {
                    Log::warning('Invalid item data for sync (kamar)', ['item' => $item]);
                    continue;
                }

                // Check if item already exists in cost references
                $existing = \App\Models\CostReference::where('service_code', $item['kode'])
                    ->where('hospital_id', $hospitalId)
                    ->first();

                if ($existing) {
                    // Update existing item
                    $existing->update([
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        // Kamar → kategori kamar
                        'category' => 'kamar',
                    ]);
                } else {
                    // Create new item
                    \App\Models\CostReference::create([
                        'service_code' => $item['kode'],
                        'service_description' => $item['nama'],
                        'purchase_price' => $item['harga'],
                        'standard_cost' => $item['harga'],
                        'unit' => 'Kamar',
                        'source' => 'SIMRS - Kamar',
                        'hospital_id' => $hospitalId,
                        'is_synced_from_simrs' => true,
                        'last_synced_at' => now(),
                        'category' => 'kamar',
                    ]);
                }

                $syncedCount++;
            }

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "$syncedCount items successfully synced to cost references"
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing kamar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing kamar: ' . $e->getMessage()
            ], 500);
        }
    }

}
