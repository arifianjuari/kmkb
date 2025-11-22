<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class TindakanRawatInap extends SimrsModel
{
    /**
     * Get Master Tarif Tindakan Rawat Inap
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return \Illuminate\Support\Collection
     */
    public static function getTindakanDokter($limit = 50, $offset = 0, $search = null)
    {
        $baseDataQuery = "SELECT
            jpi.kd_jenis_prw,
            jpi.nm_perawatan,
            jpi.total_byrdrpr,
            'Ranap' AS layanan
        FROM jns_perawatan_inap jpi";
        
        $baseCountQuery = "SELECT COUNT(*) as total FROM jns_perawatan_inap jpi";
        
        $params = [];
        
        if ($search) {
            $baseDataQuery .= " WHERE jpi.kd_jenis_prw LIKE ? OR jpi.nm_perawatan LIKE ?";
            $baseCountQuery .= " WHERE jpi.kd_jenis_prw LIKE ? OR jpi.nm_perawatan LIKE ?";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            // For count query, we need the same parameters
            $countParams = ["%{$search}%", "%{$search}%"];
        } else {
            $countParams = [];
        }
        
        $baseDataQuery .= " ORDER BY jpi.nm_perawatan LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$limit, $offset]);
        
        $data = DB::connection('simrs')->select($baseDataQuery, $dataParams);
        $count = DB::connection('simrs')->select($baseCountQuery, $countParams)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get Master Tarif Tindakan Rawat Inap (same as dokter since it's master data)
     *
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Support\Collection
     */
    public static function getTindakanPerawat($limit = 50, $offset = 0)
    {
        $dataQuery = "SELECT
            jpi.kd_jenis_prw,
            jpi.nm_perawatan,
            jpi.total_byrdrpr,
            'Ranap' AS layanan
        FROM jns_perawatan_inap jpi
        ORDER BY jpi.nm_perawatan
        LIMIT ? OFFSET ?";
        
        $countQuery = "SELECT COUNT(*) as total FROM jns_perawatan_inap jpi";
        
        $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
        $count = DB::connection('simrs')->select($countQuery)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get Master Tarif Tindakan Rawat Inap (same as dokter since it's master data)
     *
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Support\Collection
     */
    public static function getTindakanDokterPerawat($limit = 50, $offset = 0)
    {
        $dataQuery = "SELECT
            jpi.kd_jenis_prw,
            jpi.nm_perawatan,
            jpi.total_byrdrpr,
            'Ranap' AS layanan
        FROM jns_perawatan_inap jpi
        ORDER BY jpi.nm_perawatan
        LIMIT ? OFFSET ?";
        
        $countQuery = "SELECT COUNT(*) as total FROM jns_perawatan_inap jpi";
        
        $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
        $count = DB::connection('simrs')->select($countQuery)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }
}
