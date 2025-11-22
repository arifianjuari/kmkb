<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class Kamar extends SimrsModel
{
    /**
     * Get Kamar data
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public static function getKamar($limit = 50, $offset = 0, $search = null)
    {
        $baseDataQuery = "SELECT
            k.kd_kamar,
            k.kd_bangsal,
            k.trf_kamar,
            k.kelas,
            k.statusdata
        FROM kamar k";
        
        $baseCountQuery = "SELECT COUNT(*) as total FROM kamar k";
        
        $params = [];
        $countParams = [];
        
        // Add statusdata filter
        $whereClause = " WHERE k.statusdata = '1'";
        
        if ($search) {
            $whereClause .= " AND (k.kd_kamar LIKE ? OR k.kd_bangsal LIKE ? OR k.kelas LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            // For count query, we need the same parameters
            $countParams = ["%{$search}%", "%{$search}%", "%{$search}%"];
        }
        
        $baseDataQuery .= $whereClause;
        $baseCountQuery .= $whereClause;
        
        $baseDataQuery .= " ORDER BY k.kd_kamar LIMIT ? OFFSET ?";
        $dataParams = array_merge($params, [$limit, $offset]);
        
        $data = DB::connection('simrs')->select($baseDataQuery, $dataParams);
        $count = DB::connection('simrs')->select($baseCountQuery, $countParams)[0]->total;
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }
}
