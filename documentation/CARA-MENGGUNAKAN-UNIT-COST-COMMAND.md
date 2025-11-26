# Cara Menggunakan Command Perhitungan Unit Cost

## Deskripsi

Command `unit-cost:calculate` digunakan untuk menghitung unit cost berdasarkan data GL Expenses, Allocation Results, dan Service Volumes yang sudah ada di database.

## Lokasi Command

Command ini berada di:
```
app/Console/Commands/CalculateUnitCost.php
```

## Cara Menjalankan Command

### 1. Melalui Terminal/Command Line

Buka terminal di direktori project dan jalankan:

```bash
php artisan unit-cost:calculate --year=2025 --month=1 --vlabel=UC_2025_JAN
```

### 2. Parameter yang Tersedia

| Parameter | Required | Deskripsi | Contoh |
|-----------|----------|-----------|--------|
| `--hospital` | No | Hospital ID (jika tidak diisi, akan menggunakan hospital pertama) | `--hospital=1` |
| `--year` | **Yes** | Tahun periode perhitungan | `--year=2025` |
| `--month` | **Yes** | Bulan periode perhitungan (1-12) | `--month=1` |
| `--vlabel` | **Yes** | Label versi unit cost | `--vlabel=UC_2025_JAN` |

### 3. Contoh Penggunaan

#### Contoh 1: Perhitungan untuk Januari 2025

```bash
php artisan unit-cost:calculate --year=2025 --month=1 --vlabel=UC_2025_JAN
```

#### Contoh 2: Perhitungan untuk Februari 2025 dengan Hospital ID tertentu

```bash
php artisan unit-cost:calculate --hospital=1 --year=2025 --month=2 --vlabel=UC_2025_FEB
```

#### Contoh 3: Perhitungan untuk Maret 2025

```bash
php artisan unit-cost:calculate --year=2025 --month=3 --vlabel=UC_2025_MAR
```

## Prasyarat

Sebelum menjalankan command, pastikan data berikut sudah ada:

1. **GL Expenses** - Data pengeluaran per cost center
   ```bash
   php artisan db:seed --class=GlExpensesTableSeeder
   ```

2. **Allocation Results** - Hasil alokasi biaya (jika belum ada, perlu menjalankan proses alokasi terlebih dahulu)

3. **Service Volumes** - Volume layanan per cost reference
   ```bash
   php artisan db:seed --class=ServiceVolumesTableSeeder
   ```

4. **Cost References** - Harus memiliki `cost_center_id` yang mengarah ke revenue center

## Proses Perhitungan

Command ini akan melakukan:

1. **Mengambil Direct Cost Material**
   - Dari GL Expenses dengan `allocation_category` = 'bhp_medis' atau 'bhp_non_medis'
   - Per cost center untuk periode tertentu

2. **Mengambil Direct Cost Labor**
   - Dari GL Expenses dengan `allocation_category` = 'gaji'
   - Per cost center untuk periode tertentu

3. **Mengambil Indirect Cost Overhead**
   - Dari Allocation Results (biaya yang dialokasikan dari support centers)
   - Per cost center untuk periode tertentu

4. **Mengambil Service Volume**
   - Dari Service Volumes per cost reference untuk periode tertentu

5. **Menghitung Unit Cost**
   - Direct Cost Material per unit = Direct Material / Service Volume
   - Direct Cost Labor per unit = Direct Labor / Service Volume
   - Indirect Cost Overhead per unit = Indirect Overhead / Service Volume
   - Total Unit Cost = (Direct Material + Direct Labor + Indirect Overhead) / Service Volume

6. **Menyimpan Hasil**
   - Menyimpan ke tabel `unit_cost_calculations` dengan version label

## Output Command

Setelah command berjalan, Anda akan melihat:

```
========================================
Unit Cost Calculation
========================================
Hospital: Default Hospital
Period: 2025-01
Version: UC_2025_JAN

Starting calculation...

✓ Calculation completed successfully!
  - Processed: 13 cost references

========================================
```

## Troubleshooting

### Error: "No revenue cost centers found"

**Solusi**: Pastikan ada cost centers dengan `type = 'revenue'`
```bash
php artisan db:seed --class=CostCentersTableSeeder
```

### Error: "No cost references found for revenue centers"

**Solusi**: Pastikan cost references memiliki `cost_center_id` yang mengarah ke revenue center

### Error: "Service volume is zero or not found"

**Solusi**: Pastikan ada service volumes untuk periode yang dihitung
```bash
php artisan db:seed --class=ServiceVolumesTableSeeder
```

### Error: "No allocation results found"

**Solusi**: Jika menggunakan alokasi, pastikan allocation results sudah ada. Jika tidak menggunakan alokasi, indirect overhead akan menjadi 0.

## Menjalankan untuk Multiple Periods

Untuk menghitung unit cost untuk beberapa periode sekaligus, Anda bisa membuat script sederhana:

```bash
#!/bin/bash
# calculate_multiple_periods.sh

php artisan unit-cost:calculate --year=2025 --month=1 --vlabel=UC_2025_JAN
php artisan unit-cost:calculate --year=2025 --month=2 --vlabel=UC_2025_FEB
php artisan unit-cost:calculate --year=2025 --month=3 --vlabel=UC_2025_MAR
```

Atau menggunakan loop:

```bash
for month in 1 2 3; do
    version="UC_2025_$(date -d "2025-$month-01" +%b | tr '[:lower:]' '[:upper:]')"
    php artisan unit-cost:calculate --year=2025 --month=$month --vlabel=$version
done
```

## Verifikasi Hasil

Setelah command berjalan, verifikasi hasil dengan:

```bash
# Cek jumlah unit cost calculations yang dibuat
php artisan tinker
>>> \App\Models\UnitCostCalculation::where('version_label', 'UC_2025_JAN')->count();

# Cek detail unit cost untuk service tertentu
>>> \App\Models\UnitCostCalculation::where('version_label', 'UC_2025_JAN')
    ->with('costReference')
    ->get()
    ->map(fn($uc) => [
        'service' => $uc->costReference->service_code,
        'unit_cost' => $uc->total_unit_cost,
        'material' => $uc->direct_cost_material,
        'labor' => $uc->direct_cost_labor,
        'overhead' => $uc->indirect_cost_overhead,
    ]);
```

## Catatan Penting

1. **Version Label**: Gunakan format yang konsisten, misalnya `UC_2025_JAN`, `UC_2025_FEB`, dll.

2. **Data Integrity**: Pastikan data GL Expenses, Allocation Results, dan Service Volumes sudah lengkap dan benar sebelum menjalankan command.

3. **Transaction**: Command menggunakan database transaction, jadi jika terjadi error, semua perubahan akan di-rollback.

4. **Idempotent**: Command ini idempotent - jika dijalankan ulang dengan parameter yang sama, akan update data yang sudah ada.

5. **Performance**: Untuk data yang besar, proses perhitungan mungkin memakan waktu beberapa menit.

## Integrasi dengan Scheduled Task (Cron)

Anda bisa menjadwalkan perhitungan unit cost secara otomatis dengan menambahkan ke `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Calculate unit cost setiap akhir bulan
    $schedule->command('unit-cost:calculate --year=' . date('Y') . ' --month=' . date('m') . ' --vlabel=UC_' . date('Y') . '_' . strtoupper(date('M')))
        ->monthlyOn(date('t'), '23:59');
}
```

