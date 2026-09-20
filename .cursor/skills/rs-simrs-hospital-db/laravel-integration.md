# Integrasi Laravel — hospital_sims

## 1. Connection `config/database.php`

```php
'hospital_sims' => [
    'driver' => 'mysql',
    'host' => env('HOSPITAL_DB_HOST'),
    'port' => env('HOSPITAL_DB_PORT', '3306'),
    'database' => env('HOSPITAL_DB_DATABASE', 'simsvbaru'),
    'username' => env('HOSPITAL_DB_USERNAME'),
    'password' => env('HOSPITAL_DB_PASSWORD'),
    'charset' => env('HOSPITAL_DB_CHARSET', 'utf8'),
    'collation' => env('HOSPITAL_DB_COLLATION', 'utf8_general_ci'),
    'strict' => false,
    'options' => extension_loaded('pdo_mysql') && defined('Pdo\Mysql::ATTR_CONNECT_TIMEOUT')
        ? [\Pdo\Mysql::ATTR_CONNECT_TIMEOUT => (int) env('HOSPITAL_DB_CONNECT_TIMEOUT', 3)]
        : [],
],
```

## 2. Config fitur (modul atau `config/hospital.php`)

```php
'hospital_db' => [
    'enabled' => env('HOSPITAL_DB_ENABLED', false),
    'connection' => 'hospital_sims',
    'connect_timeout' => (int) env('HOSPITAL_DB_CONNECT_TIMEOUT', 3),
    'availability_cache_seconds' => (int) env('HOSPITAL_DB_AVAILABILITY_CACHE_SECONDS', 30),
],
```

## 3. Service — pola minimal

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

final class HospitalSimrsConnection
{
    public function connectionName(): string
    {
        return config('hospital.hospital_db.connection', 'hospital_sims');
    }

    public function isConfigured(): bool
    {
        if (! config('hospital.hospital_db.enabled')) {
            return false;
        }

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

    public function connection(): \Illuminate\Database\Connection
    {
        return DB::connection($this->connectionName());
    }

    private function availabilityCacheKey(): string
    {
        $name = $this->connectionName();

        return sprintf(
            'hospital_sims.available:%s:%s',
            config("database.connections.{$name}.host"),
            config("database.connections.{$name}.port", 3306),
        );
    }
}
```

Implementasi query rajal/ranap: salin `baseQuery()` dari Faskelola `HospitalOutpatientLookupService` / `HospitalInpatientLookupService`.

## 4. Controller API (contoh)

```php
public function search(Request $request, HospitalOutpatientLookupService $lookup): JsonResponse
{
    if (! $lookup->isAvailable()) {
        return response()->json(['message' => 'RS tidak tersedia'], 503);
    }

    $validated = $request->validate([
        'q' => 'required|string|min:2|max:100',
    ]);

    try {
        return response()->json($lookup->searchTodayRalan($validated['q']));
    } catch (Throwable) {
        $lookup->forgetAvailabilityCache();
        return response()->json(['message' => 'Koneksi RS gagal'], 503);
    }
}
```

Lindungi dengan `auth` + permission sesuai domain Anda.

## 5. Route (contoh)

```php
Route::middleware(['auth'])->group(function () {
    Route::get('hospital-outpatient/status', [HospitalOutpatientController::class, 'status']);
    Route::get('hospital-outpatient/search', [HospitalOutpatientController::class, 'search']);
    Route::post('hospital-outpatient/import', [HospitalOutpatientController::class, 'import']);
});
```

## 6. Frontend (fetch)

- Cek `status` sebelum aktifkan mode "Server RS".
- Search: `GET .../search?q=` dengan header `Accept: application/json`, credentials same-origin.
- Import: `POST .../import` JSON `{ "no_rawat": "..." }`, CSRF token jika web session.

Contoh UI: `modules/Emr/resources/views/appointments/create.blade.php` (toggle Faskelola | Server RS).

## 7. Testing (Pest)

- Mock `DB::connection('hospital_sims')` atau set config di test:
  - `config(['hospital.hospital_db.enabled' => true, 'database.connections.hospital_sims.host' => 'x', ...])`
- Assert `isConfigured()` tanpa koneksi nyata.
- Feature test controller dengan `Http::fake` atau partial mock service.

Lihat: `tests/Feature/Emr/HospitalOutpatientLookupTest.php`, `HospitalInpatientLookupTest.php`.

## 8. Referensi Faskelola (copy source)

| File | Peran |
|------|--------|
| `config/database.php` → `hospital_sims` | Koneksi |
| `modules/Emr/config/config.php` → `hospital_db` | Flag |
| `HospitalInpatientLookupService.php` | Ranap + shared availability |
| `HospitalOutpatientLookupService.php` | Rajal |
| `HospitalOutpatientController.php` | API rajal |
| `HospitalInpatientController.php` | API ranap |
