<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class TindakanRawatJalan extends SimrsModel
{
    /**
     * Get Master Tarif Tindakan Rawat Jalan
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return \Illuminate\Support\Collection
     */
    public static function getTindakanDokter($limit = 50, $offset = 0, $search = null)
    {
        $baseDataQuery = "SELECT
            jp.kd_jenis_prw,
            jp.nm_perawatan,
            jp.total_byrdrpr,
            'Ralan' AS layanan
        FROM jns_perawatan jp";
        
        $baseCountQuery = "SELECT COUNT(*) as total FROM jns_perawatan jp";
        
        $params = [];
        
        if ($search) {
            $baseDataQuery .= " WHERE jp.kd_jenis_prw LIKE ? OR jp.nm_perawatan LIKE ?";
            $baseCountQuery .= " WHERE jp.kd_jenis_prw LIKE ? OR jp.nm_perawatan LIKE ?";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            // For count query, we need the same parameters
            $countParams = ["%{$search}%", "%{$search}%"];
        } else {
            $countParams = [];
        }
        
        $baseDataQuery .= " ORDER BY jp.nm_perawatan LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$limit, $offset]);
        
        $data = DB::connection('simrs')->select($baseDataQuery, $dataParams);
        $count = DB::connection('simrs')->select($baseCountQuery, $countParams)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get Master Tarif Tindakan Rawat Jalan (same as dokter since it's master data)
     *
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Support\Collection
     */
    public static function getTindakanPerawat($limit = 50, $offset = 0)
    {
        $dataQuery = "SELECT
            jp.kd_jenis_prw,
            jp.nm_perawatan,
            jp.total_byrdrpr,
            'Ralan' AS layanan
        FROM jns_perawatan jp
        ORDER BY jp.nm_perawatan
        LIMIT ? OFFSET ?";
        
        $countQuery = "SELECT COUNT(*) as total FROM jns_perawatan jp";
        
        $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
        $count = DB::connection('simrs')->select($countQuery)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get Master Tarif Tindakan Rawat Jalan (same as dokter since it's master data)
     *
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Support\Collection
     */
    public static function getTindakanDokterPerawat($limit = 50, $offset = 0)
    {
        $dataQuery = "SELECT
            jp.kd_jenis_prw,
            jp.nm_perawatan,
            jp.total_byrdrpr,
            'Ralan' AS layanan
        FROM jns_perawatan jp
        ORDER BY jp.nm_perawatan
        LIMIT ? OFFSET ?";
        
        $countQuery = "SELECT COUNT(*) as total FROM jns_perawatan jp";
        
        $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
        $count = DB::connection('simrs')->select($countQuery)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }
}
