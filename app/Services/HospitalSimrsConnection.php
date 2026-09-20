<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

final class HospitalSimrsConnection
{
    public function connectionName(): string
    {
        if (config('hospital.hospital_db.enabled')) {
            return config('hospital.hospital_db.connection', 'hospital_sims');
        }

        return config('hospital.hospital_db.legacy_connection', 'simrs');
    }

    public function isConfigured(): bool
    {
        $name = $this->connectionName();

        return filled(config("database.connections.{$name}.host"))
            && filled(config("database.connections.{$name}.database"))
            && filled(config("database.connections.{$name}.username"));
    }

    public function isAvailable(bool $forceFresh = false): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $key = $this->availabilityCacheKey();
        if ($forceFresh) {
            Cache::forget($key);
        }

        $ttl = max(1, (int) config('hospital.hospital_db.availability_cache_seconds', 30));

        return Cache::remember($key, $ttl, function (): bool {
            $name = $this->connectionName();
            try {
                DB::connection($name)->selectOne('SELECT 1 AS ok');

                return true;
            } catch (Throwable) {
                return false;
            } finally {
                DB::purge($name);
            }
        });
    }

    public function forgetAvailabilityCache(): void
    {
        Cache::forget($this->availabilityCacheKey());
    }

    public function connection(): Connection
    {
        return DB::connection($this->connectionName());
    }

    public function configurationMessage(): string
    {
        if (config('hospital.hospital_db.enabled')) {
            return 'Database SIMRS belum dikonfigurasi. Isi HOSPITAL_DB_HOST, HOSPITAL_DB_DATABASE, HOSPITAL_DB_USERNAME, dan HOSPITAL_DB_PASSWORD di .env, lalu jalankan php artisan config:clear.';
        }

        return 'Database SIMRS belum dikonfigurasi. Isi SIMRS_DB_* atau aktifkan HOSPITAL_DB_ENABLED dengan variabel HOSPITAL_DB_* di .env, lalu jalankan php artisan config:clear.';
    }

    public function availabilityMessage(): string
    {
        $name = $this->connectionName();
        $host = config("database.connections.{$name}.host");
        $port = config("database.connections.{$name}.port", 3306);
        $database = config("database.connections.{$name}.database");

        return "Tidak dapat terhubung ke SIMRS ({$host}:{$port}/{$database}). Pastikan VPN/jaringan RS aktif, kredensial benar, dan nama database sesuai (biasanya simsvbaru).";
    }

    private function availabilityCacheKey(): string
    {
        $name = $this->connectionName();

        return sprintf(
            'simrs.available:%s:%s:%s',
            config("database.connections.{$name}.host"),
            config("database.connections.{$name}.port", 3306),
            config("database.connections.{$name}.database"),
        );
    }
}
