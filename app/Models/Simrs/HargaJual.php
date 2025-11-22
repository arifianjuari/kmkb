<?php

namespace App\Models\Simrs;

use Illuminate\Support\Facades\DB;

class HargaJual extends SimrsModel
{
    /**
     * Get harga jual yang benar-benar dipakai pasien
     *
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Support\Collection
     */
    public static function getHargaJual($limit = 50, $offset = 0)
    {
        $query = "SELECT 
            dpo.tgl_perawatan,
            dpo.no_rawat,
            dpo.kode_brng,
            db.nama_brng,
            db.h_beli AS harga_beli_dasar,
            ROUND(dpo.biaya_obat / dpo.jml, 2) AS harga_jual_satuan,
            dpo.jml,
            dpo.embalase,
            dpo.tuslah,
            dpo.total AS harga_jual_total
        FROM detail_pemberian_obat dpo
        JOIN databarang db ON db.kode_brng = dpo.kode_brng
        ORDER BY dpo.tgl_perawatan DESC
        LIMIT ? OFFSET ?";

        return DB::connection('simrs')->select($query, [$limit, $offset]);
    }
}
