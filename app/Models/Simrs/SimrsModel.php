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
     * Resolve the active SIMRS connection name.
     */
    public static function connectionName(): string
    {
        return app(\App\Services\HospitalSimrsConnection::class)->connectionName();
    }

    public static function db()
    {
        return DB::connection(static::connectionName());
    }

    public static function fetchData($query)
    {
        return static::db()->select($query);
    }
}
