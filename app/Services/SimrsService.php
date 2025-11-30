<?php

namespace App\Services;

use App\Models\Simrs\MasterBarang;
use App\Models\Simrs\TindakanRawatJalan;
use App\Models\Simrs\TindakanRawatInap;
use App\Models\Simrs\Laboratorium;
use App\Models\Simrs\Radiologi;
use App\Models\Simrs\Operasi;
use App\Models\Simrs\Kamar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimrsService
{
    /**
     * Get all master barang (obat/BHP)
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getMasterBarang($limit = 100, $offset = 0, $search = null)
    {
        try {
            return MasterBarang::getMasterBarang($limit, $offset, $search);
        } catch (\Exception $e) {
            Log::error('Error fetching master barang from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    

    /**
     * Get all tindakan rawat jalan data
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getTindakanRawatJalan($limit = 50, $offset = 0, $search = null)
    {
        try {
            // Since we're now using master tariff data, we only need to call one method
            // as all three methods now return the same master data
            return TindakanRawatJalan::getTindakanDokter($limit, $offset, $search);
        } catch (\Exception $e) {
            Log::error('Error fetching tindakan rawat jalan from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get all tindakan rawat inap data
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getTindakanRawatInap($limit = 50, $offset = 0, $search = null)
    {
        try {
            // Since we're now using master tariff data, we only need to call one method
            // as all three methods now return the same master data
            return TindakanRawatInap::getTindakanDokter($limit, $offset, $search);
        } catch (\Exception $e) {
            Log::error('Error fetching tindakan rawat inap from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get laboratorium data
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getLaboratorium($limit = 50, $offset = 0, $search = null)
    {
        try {
            return Laboratorium::getLaboratorium($limit, $offset, $search);
        } catch (\Exception $e) {
            Log::error('Error fetching laboratorium data from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get radiologi data
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getRadiologi($limit = 50, $offset = 0, $search = null)
    {
        try {
            return Radiologi::getRadiologi($limit, $offset, $search);
        } catch (\Exception $e) {
            Log::error('Error fetching radiologi data from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get jenis radiologi data from SIMRS
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getJenisRadiologi($limit = 50, $offset = 0)
    {
        try {
            $result = Radiologi::getJenisRadiologi($limit, $offset);
            return [
                'data' => $result['data'] ?? [],
                'total' => $result['total'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching jenis radiologi data from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get operasi data from SIMRS
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getOperasi($limit = 50, $offset = 0, $search = null)
    {
        try {
            $result = Operasi::getOperasi($limit, $offset, $search);
            // Ensure consistent structure
            return [
                'data' => $result['data'] ?? [],
                'total' => $result['total'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching operasi data from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
            ];
        }
    }

    /**
     * Get kamar data
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function getKamar($limit = 50, $offset = 0, $search = null)
    {
        try {
            return Kamar::getKamar($limit, $offset, $search);
        } catch (\Exception $e) {
            Log::error('Error fetching kamar data from SIMRS: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get all SIMRS data in a structured format
     *
     * @return array
     */
    public function getAllSimrsData()
    {
        return [
            'master_barang' => $this->getMasterBarang(),
            'tindakan_rawat_jalan' => $this->getTindakanRawatJalan(),
            'tindakan_rawat_inap' => $this->getTindakanRawatInap(),
            'laboratorium' => $this->getLaboratorium(),
            'radiologi' => $this->getRadiologi(),
            'operasi' => $this->getOperasi(),
        ];
    }

    /**
     * Get candidates for cost centers (poliklinik, bangsal, departemen)
     *
     * @return array
     */
    public function getCostCenterCandidates()
    {
        try {
            // Get Poliklinik (Rawat Jalan)
            $poli = DB::connection('simrs')
                ->table('poliklinik')
                ->select('kd_poli as id', 'nm_poli as name', DB::raw("'Rawat Jalan' as type"))
                ->where('status', '1')
                ->get();

            // Get Bangsal (Rawat Inap)
            $bangsal = DB::connection('simrs')
                ->table('bangsal')
                ->select('kd_bangsal as id', 'nm_bangsal as name', DB::raw("'Rawat Inap' as type"))
                ->where('status', '1')
                ->get();

            // Get Departemen
            $departemen = DB::connection('simrs')
                ->table('departemen')
                ->select('dep_id as id', 'nama as name', DB::raw("'Departemen' as type"))
                ->get();

            return [
                'poliklinik' => $poli,
                'bangsal' => $bangsal,
                'departemen' => $departemen,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching cost center candidates from SIMRS: ' . $e->getMessage());
            return [
                'poliklinik' => [],
                'bangsal' => [],
                'departemen' => [],
            ];
        }
    }

    /**
     * Test connection to SIMRS database
     *
     * @return bool
     */
    public function testConnection()
    {
        try {
            DB::connection('simrs')->select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            Log::error('SIMRS database connection failed: ' . $e->getMessage());
            return false;
        }
    }
}
