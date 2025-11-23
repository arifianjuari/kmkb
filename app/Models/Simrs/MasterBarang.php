<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterBarang extends SimrsModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'databarang';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'kode_brng';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'dasar',
        'ralan',
        'kelas3',
        'isi',
        'expire',
        'status',
    ];

    /**
     * Get master item obat/BHP
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public static function getMasterBarang($limit = 100, $offset = 0, $search = null)
    {
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $dataQuery = "SELECT 
                kode_brng,
                nama_brng,
                dasar AS harga_beli_dasar,
                ralan,
                kelas3,
                isi,
                expire,
                status
            FROM databarang
            WHERE status = '1' AND (kode_brng LIKE ? OR nama_brng LIKE ?)
            ORDER BY nama_brng
            LIMIT ? OFFSET ?";
            
            $countQuery = "SELECT COUNT(*) as total FROM databarang WHERE status = '1' AND (kode_brng LIKE ? OR nama_brng LIKE ?)";
            
            $data = DB::connection('simrs')->select($dataQuery, [$searchTerm, $searchTerm, $limit, $offset]);
            $count = DB::connection('simrs')->select($countQuery, [$searchTerm, $searchTerm])[0]->total;
        } else {
            $dataQuery = "SELECT 
                kode_brng,
                nama_brng,
                dasar AS harga_beli_dasar,
                ralan,
                kelas3,
                isi,
                expire,
                status
            FROM databarang
            WHERE status = '1'
            ORDER BY nama_brng
            LIMIT ? OFFSET ?";
            
            $countQuery = "SELECT COUNT(*) as total FROM databarang WHERE status = '1'";
            
            $data = DB::connection('simrs')->select($dataQuery, [$limit, $offset]);
            $count = DB::connection('simrs')->select($countQuery)[0]->total;
        }
        
        return [
            'data' => $data,
            'total' => $count
        ];
    }
}
