<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class Laboratorium extends SimrsModel
{
    /**
     * Get Master Tarif Laboratorium
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public static function getLaboratorium($limit = 50, $offset = 0, $search = null)
    {
        $baseDataQuery = "SELECT
            jpl.kd_jenis_prw,
            jpl.nm_perawatan,
            jpl.total_byr,
            'Lab' AS layanan
        FROM jns_perawatan_lab jpl";
        
        $baseCountQuery = "SELECT COUNT(*) as total FROM jns_perawatan_lab jpl";
        
        $params = [];
        
        if ($search) {
            $baseDataQuery .= " WHERE jpl.kd_jenis_prw LIKE ? OR jpl.nm_perawatan LIKE ?";
            $baseCountQuery .= " WHERE jpl.kd_jenis_prw LIKE ? OR jpl.nm_perawatan LIKE ?";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            // For count query, we need the same parameters
            $countParams = ["%{$search}%", "%{$search}%"];
        } else {
            $countParams = [];
        }
        
        $baseDataQuery .= " ORDER BY jpl.nm_perawatan LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$limit, $offset]);
        
        $data = DB::connection('simrs')->select($baseDataQuery, $dataParams);
        $count = DB::connection('simrs')->select($baseCountQuery, $countParams)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get Master Tarif Laboratorium (same method for consistency)
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getDetailLaboratorium($limit = 50, $offset = 0)
    {
        $dataQuery = "SELECT
            jpl.kd_jenis_prw,
            jpl.nm_perawatan,
            jpl.total_byr,
            'Lab' AS layanan
        FROM jns_perawatan_lab jpl
        ORDER BY jpl.nm_perawatan
        LIMIT ? OFFSET ?";
        
        $countQuery = "SELECT COUNT(*) as total FROM jns_perawatan_lab";
        
        $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
        $count = DB::connection('simrs')->select($countQuery)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }
}
