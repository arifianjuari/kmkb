<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class Radiologi extends SimrsModel
{
    /**
     * Get Master Tarif Radiologi
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public static function getRadiologi($limit = 50, $offset = 0, $search = null)
    {
        $baseDataQuery = "SELECT
            jpr.kd_jenis_prw,
            jpr.nm_perawatan,
            jpr.bhp,
            jpr.tarif_tindakan_dokter,
            jpr.tarif_tindakan_petugas,
            jpr.kso,
            jpr.menejemen,
            jpr.total_byr
        FROM jns_perawatan_radiologi jpr";
        
        $baseCountQuery = "SELECT COUNT(*) as total FROM jns_perawatan_radiologi jpr";
        
        $params = [];
        
        if ($search) {
            $baseDataQuery .= " WHERE jpr.kd_jenis_prw LIKE ? OR jpr.nm_perawatan LIKE ?";
            $baseCountQuery .= " WHERE jpr.kd_jenis_prw LIKE ? OR jpr.nm_perawatan LIKE ?";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            // For count query, we need the same parameters
            $countParams = ["%{$search}%", "%{$search}%"];
        } else {
            $countParams = [];
        }
        
        $baseDataQuery .= " ORDER BY jpr.nm_perawatan LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$limit, $offset]);
        
        $data = DB::connection('simrs')->select($baseDataQuery, $dataParams);
        $count = DB::connection('simrs')->select($baseCountQuery, $countParams)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get Master Tarif Radiologi (same method for consistency)
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getJenisRadiologi($limit = 50, $offset = 0)
    {
        $dataQuery = "SELECT
            jpr.kd_jenis_prw,
            jpr.nm_perawatan,
            jpr.bhp,
            jpr.tarif_tindakan_dokter,
            jpr.tarif_tindakan_petugas,
            jpr.kso,
            jpr.menejemen,
            jpr.total_byr
        FROM jns_perawatan_radiologi jpr
        ORDER BY jpr.nm_perawatan
        LIMIT ? OFFSET ?";
        
        $countQuery = "SELECT COUNT(*) as total FROM jns_perawatan_radiologi";
        
        $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
        $count = DB::connection('simrs')->select($countQuery)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }
}
