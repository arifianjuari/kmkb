# Unit Costing Seeders

## Deskripsi

Seeder-seeder ini dibuat untuk menghasilkan data yang diperlukan dalam proses perhitungan unit cost. **Seeder ini TIDAK langsung membuat data di tabel `unit_cost_calculations`**, melainkan membuat data input yang akan digunakan oleh sistem untuk menghitung unit cost.

## Daftar Seeders

### 1. ExpenseCategoriesTableSeeder
**Tabel**: `expense_categories`

**Fungsi**: Membuat kategori pengeluaran (COA accounts) yang digunakan untuk mengklasifikasikan biaya.

**Data yang dibuat**:
- 34 kategori pengeluaran meliputi:
  - Gaji & Tunjangan (7 kategori)
  - BHP Medis (7 kategori)
  - BHP Non Medis (5 kategori)
  - Depresiasi (5 kategori)
  - Lain-lain (10 kategori)

**Dependencies**: `HospitalsTableSeeder`

---

### 2. AllocationDriversTableSeeder
**Tabel**: `allocation_drivers`

**Fungsi**: Membuat driver alokasi yang digunakan untuk mengalokasikan biaya dari support centers ke revenue centers.

**Data yang dibuat**:
- 10 allocation drivers:
  - Luas Lantai (m²)
  - Jumlah Karyawan (FTE)
  - Jumlah Pasien
  - Jumlah Tempat Tidur
  - Volume Laundry (kg)
  - Jam Layanan
  - Jumlah Kunjungan
  - Jumlah Tindakan
  - Konsumsi Listrik (kWh)
  - Konsumsi Air (m³)

**Dependencies**: `HospitalsTableSeeder`

---

### 3. GlExpensesTableSeeder
**Tabel**: `gl_expenses`

**Fungsi**: Membuat data pengeluaran riil per cost center dan expense category per periode.

**Data yang dibuat**:
- GL Expenses untuk 3 bulan terakhir
- Setiap cost center memiliki beberapa expense categories
- Amount dihitung berdasarkan tipe cost center dan kategori expense

**Dependencies**: 
- `HospitalsTableSeeder`
- `CostCentersTableSeeder`
- `ExpenseCategoriesTableSeeder`

**Output**: ~765 records untuk 3 periode

---

### 4. DriverStatisticsTableSeeder
**Tabel**: `driver_statistics`

**Fungsi**: Membuat data nilai driver per cost center per periode.

**Data yang dibuat**:
- Driver statistics untuk 3 bulan terakhir
- Setiap cost center memiliki nilai untuk setiap driver yang relevan
- Nilai disesuaikan berdasarkan tipe cost center

**Dependencies**:
- `HospitalsTableSeeder`
- `CostCentersTableSeeder`
- `AllocationDriversTableSeeder`

**Output**: ~957 records untuk 3 periode

---

### 5. AllocationMapsTableSeeder
**Tabel**: `allocation_maps`

**Fungsi**: Membuat mapping alokasi dari support centers ke revenue centers menggunakan driver tertentu.

**Data yang dibuat**:
- Mapping alokasi dengan step sequence:
  - Step 1: Alokasi berdasarkan Luas Lantai (MES, UMUM, LISTRIK, AC)
  - Step 2: Alokasi berdasarkan Jumlah Karyawan (SDM, KANTIN)
  - Step 3: Alokasi Laundry berdasarkan Volume Laundry
  - Step 4: Alokasi Listrik berdasarkan Konsumsi Listrik
  - Step 5: Alokasi Air berdasarkan Konsumsi Air
  - Step 6: Alokasi lainnya (KEBERSIHAN, KEAMANAN, dll) berdasarkan Luas Lantai

**Dependencies**:
- `HospitalsTableSeeder`
- `CostCentersTableSeeder`
- `AllocationDriversTableSeeder`

**Output**: ~13 records dengan step sequence 1-5

---

### 6. ServiceVolumesTableSeeder
**Tabel**: `service_volumes`

**Fungsi**: Membuat data volume layanan per cost reference per periode.

**Data yang dibuat**:
- Service volumes untuk 3 bulan terakhir
- Setiap cost reference memiliki volume per periode
- Beberapa cost references memiliki volume per tariff class

**Dependencies**:
- `HospitalsTableSeeder`
- `CostReferencesTableSeeder`
- `TariffClass` (opsional)

**Output**: ~48 records untuk 3 periode

---

## Cara Menggunakan

### Jalankan Semua Seeder Unit Costing

```bash
# Jalankan semua seeder (sudah ditambahkan ke DatabaseSeeder)
php artisan db:seed
```

### Jalankan Seeder Individual

```bash
# Expense Categories
php artisan db:seed --class=ExpenseCategoriesTableSeeder

# Allocation Drivers
php artisan db:seed --class=AllocationDriversTableSeeder

# GL Expenses
php artisan db:seed --class=GlExpensesTableSeeder

# Driver Statistics
php artisan db:seed --class=DriverStatisticsTableSeeder

# Allocation Maps
php artisan db:seed --class=AllocationMapsTableSeeder

# Service Volumes
php artisan db:seed --class=ServiceVolumesTableSeeder
```

## Urutan Dependencies

```
HospitalsTableSeeder
    ↓
CostCentersTableSeeder
    ↓
ExpenseCategoriesTableSeeder ──┐
    ↓                            │
AllocationDriversTableSeeder ────┤
    ↓                            │
CostReferencesTableSeeder ───────┤
    ↓                            │
GlExpensesTableSeeder ───────────┘
    ↓
DriverStatisticsTableSeeder
    ↓
AllocationMapsTableSeeder
    ↓
ServiceVolumesTableSeeder
```

## Data yang Dihasilkan

Setelah semua seeder dijalankan, Anda akan memiliki:

1. **34 Expense Categories** - Kategori pengeluaran untuk klasifikasi biaya
2. **10 Allocation Drivers** - Driver untuk alokasi biaya
3. **~765 GL Expenses** - Data pengeluaran riil per cost center (3 bulan)
4. **~957 Driver Statistics** - Data driver per cost center (3 bulan)
5. **~13 Allocation Maps** - Mapping alokasi dengan step sequence
6. **~48 Service Volumes** - Volume layanan per cost reference (3 bulan)

## Proses Perhitungan Unit Cost

Dengan data yang dihasilkan seeder ini, sistem dapat melakukan:

1. **Alokasi Biaya**: Menggunakan `allocation_maps` dan `driver_statistics` untuk mengalokasikan biaya dari support centers ke revenue centers
2. **Perhitungan Total Cost**: Menjumlahkan biaya langsung dan biaya teralokasi per cost center
3. **Perhitungan Unit Cost**: Membagi total cost dengan `service_volumes` untuk mendapatkan unit cost per service

## Catatan

- Seeder ini **TIDAK langsung membuat data di `unit_cost_calculations`**
- Data yang dibuat adalah **input** untuk proses perhitungan unit cost
- Proses perhitungan unit cost biasanya dilakukan melalui:
  - Command/Job khusus
  - API endpoint untuk trigger calculation
  - Scheduled task (cron job)

## Testing

Setelah seeder dijalankan, Anda dapat:

1. **Test Alokasi**: Verifikasi bahwa biaya support centers teralokasi dengan benar ke revenue centers
2. **Test Driver Statistics**: Pastikan nilai driver sesuai dengan cost center
3. **Test Service Volumes**: Verifikasi volume layanan per periode
4. **Test Calculation**: Trigger proses perhitungan unit cost dan verifikasi hasilnya

