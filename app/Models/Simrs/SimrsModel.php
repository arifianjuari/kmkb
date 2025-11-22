<?php

namespace App\Models\Simrs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\DB;

class SimrsModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'simrs';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get data from SIM RS database using raw queries
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public static function fetchData($query)
    {
        return DB::connection('simrs')->select($query);
    }
}
