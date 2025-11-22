<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class Operasi extends SimrsModel
{
    /**
     * Get Master Tarif Operasi
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return \Illuminate\Support\Collection
     */
    public static function getOperasi($limit = 50, $offset = 0, $search = null)
    {
        // Base WHERE clause and bindings
        $whereSql = '';
        $bindings = [];
        if ($search) {
            $whereSql = " WHERE po.kode_paket LIKE ? OR po.nm_perawatan LIKE ?";
            $bindings[] = '%' . $search . '%';
            $bindings[] = '%' . $search . '%';
        }

        // Total count
        $countSql = "SELECT COUNT(*) AS total FROM paket_operasi po" . $whereSql;
        $countRow = DB::connection('simrs')->selectOne($countSql, $bindings);
        $total = $countRow ? (int)($countRow->total ?? 0) : 0;

        // Data query including columns expected by frontend (safe set) and layanan constant
        $dataSql = "SELECT
            po.kode_paket                                   AS kode_paket,
            po.kode_paket                                   AS kd_jenis_prw,
            po.nm_perawatan                                 AS nm_perawatan,
            NULL                                            AS kategori,
            COALESCE(po.operator1, 0)                       AS operator1,
            COALESCE(po.operator2, 0)                       AS operator2,
            COALESCE(po.operator3, 0)                       AS operator3,
            COALESCE(po.asisten_operator1, 0)               AS asisten_operator1,
            COALESCE(po.asisten_operator2, 0)               AS asisten_operator2,
            COALESCE(po.asisten_operator3, 0)               AS asisten_operator3,
            COALESCE(po.instrumen, 0)                       AS instrumen,
            COALESCE(po.dokter_anak, 0)                     AS dokter_anak,
            COALESCE(po.dokter_anestesi, 0)                 AS dokter_anestesi,
            COALESCE(po.asisten_anestesi, 0)                AS asisten_anestesi,
            COALESCE(po.asisten_anestesi2, 0)               AS asisten_anestesi2,
            COALESCE(po.perawat_luar, 0)                    AS perawat_luar,
            COALESCE(po.bagian_rs, 0)                       AS bagian_rs,
            COALESCE(po.omloop, 0)                          AS omloop,
            0                                               AS omloop4,
            0                                               AS omloop5,
            (
                COALESCE(po.operator1, 0) +
                COALESCE(po.operator2, 0) +
                COALESCE(po.operator3, 0) +
                COALESCE(po.asisten_operator1, 0) +
                COALESCE(po.asisten_operator2, 0) +
                COALESCE(po.asisten_operator3, 0) +
                COALESCE(po.instrumen, 0) +
                COALESCE(po.dokter_anak, 0) +
                COALESCE(po.dokter_anestesi, 0) +
                COALESCE(po.asisten_anestesi, 0) +
                COALESCE(po.asisten_anestesi2, 0) +
                COALESCE(po.perawat_luar, 0) +
                COALESCE(po.bagian_rs, 0) +
                COALESCE(po.omloop, 0) +
                0 +
                0
            ) AS tarif_total,
            'Op'                                           AS layanan,
            (
                COALESCE(po.operator1, 0) +
                COALESCE(po.operator2, 0) +
                COALESCE(po.operator3, 0) +
                COALESCE(po.asisten_operator1, 0) +
                COALESCE(po.asisten_operator2, 0) +
                COALESCE(po.asisten_operator3, 0) +
                COALESCE(po.instrumen, 0) +
                COALESCE(po.dokter_anak, 0) +
                COALESCE(po.dokter_anestesi, 0) +
                COALESCE(po.asisten_anestesi, 0) +
                COALESCE(po.asisten_anestesi2, 0) +
                COALESCE(po.perawat_luar, 0) +
                COALESCE(po.bagian_rs, 0) +
                COALESCE(po.omloop, 0) +
                0 +
                0
            ) AS total
        FROM paket_operasi po" . $whereSql . "
        ORDER BY po.nm_perawatan
        LIMIT ? OFFSET ?";

        $dataBindings = array_merge($bindings, [$limit, $offset]);
        $data = DB::connection('simrs')->select($dataSql, $dataBindings);

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * Get Master Tarif Operasi (same method for consistency)
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return \Illuminate\Support\Collection
     */
    public static function getDetailOperasi($limit = 50, $offset = 0, $search = null)
    {
        // This method is kept for consistency but returns the same data as getOperasi
        return self::getOperasi($limit, $offset, $search);
    }
}
