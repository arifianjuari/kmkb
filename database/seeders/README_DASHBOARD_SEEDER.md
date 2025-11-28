# Dashboard Data Seeder

Seeder ini menghasilkan data komprehensif untuk menampilkan chart dan informasi di dashboard KMKB.

## Data yang Dihasilkan

Seeder ini akan membuat data untuk **6 bulan terakhir** dengan variasi yang realistis:

### 1. Master Data

- **Cost Centers**: Revenue centers (Rawat Inap, Rawat Jalan, IGD, Lab, Radiologi, Farmasi, Operasi) dan Support centers (Administrasi, HRD, IT, Housekeeping)
- **Expense Categories**: Gaji & Tunjangan, Obat & Bahan Medis, Peralatan Medis, Listrik & Air, Pemeliharaan
- **Allocation Drivers**: Jumlah Pasien, Jam Layanan, Luas Ruangan, Jumlah Karyawan
- **Tariff Classes**: VIP, Kelas I, II, III
- **Cost References**: 8 layanan dengan tarif internal (Lab, Radiologi, Operasi, Farmasi)
- **Clinical Pathways**: 5 pathway dengan standard LOS (Appendektomi, Pneumonia, Sectio Caesarea, DM Type 2, Hipertensi)

### 2. Data Transaksional (6 Bulan Terakhir)

#### GL Expenses

- Data pengeluaran per cost center dan expense category
- Jumlah: 1-50 juta per kombinasi

#### Driver Statistics

- Data driver statistic per cost center
- Nilai bervariasi berdasarkan jenis driver

#### Allocation Results

- Hasil alokasi dari support centers ke revenue centers
- Jumlah alokasi: 500 ribu - 10 juta

#### Service Volumes

- Volume layanan per cost reference dan tariff class
- Quantity: 10-1000 per kombinasi

#### Unit Cost Calculations

- Perhitungan unit cost per cost reference
- Breakdown: Direct Material (40%), Direct Labor (30%), Indirect Overhead (30%)
- Version label: `UC_YYYY_MM`

#### Patient Cases

- **10-30 kasus per pathway per bulan**
- Variasi compliance: 40-100% (dengan beberapa kasus rendah)
- Variasi cost variance: -20% sampai +50% terhadap INA-CBG
- Mix JKN (50%) dan Non-JKN (50%)
- Actual LOS dengan variasi terhadap standard LOS

## Cara Menjalankan

### 1. Jalankan Migration (jika belum)

```bash
php artisan migrate
```

### 2. Jalankan Seeder

```bash
# Hanya seeder dashboard
php artisan db:seed --class=DashboardDataSeeder

# Atau jalankan semua seeder (termasuk dashboard)
php artisan db:seed
```

### 3. Refresh Database (opsional, hati-hati!)

```bash
# Hapus semua data dan seed ulang
php artisan migrate:fresh --seed
```

## Catatan Penting

1. **Standard LOS**: Seeder akan menambahkan field `standard_los` ke tabel `clinical_pathways` jika belum ada melalui migration.

2. **Data Overwrite**: Seeder menggunakan `updateOrCreate()`, jadi data yang sudah ada akan diupdate, bukan dibuat duplikat.

3. **Hospital Context**: Seeder akan menggunakan hospital pertama yang ditemukan, atau membuat hospital baru jika belum ada.

4. **User Context**: Seeder akan menggunakan user pertama dari hospital tersebut, atau membuat user baru jika belum ada.

5. **Periode Data**: Data di-generate untuk 6 bulan terakhir dari tanggal saat ini.

## Struktur Data untuk Chart

### Overview Tab

- KPI tiles: Total biaya, selisih vs INA-CBG, compliance, cost variance
- Chart Cost vs INA-CBG: Data 6 bulan terakhir
- Chart Compliance vs LOS: Top 5 pathway
- Table Top Pathways: 5 pathway dengan data compliance, cost, dan status

### Biaya & Tarif Tab

- Chart Top Cost Centers: Dari Allocation Results
- Chart Unit Cost Trend: Dari Unit Cost Calculations
- Table Tarif vs Unit Cost: Dari Cost References dengan selling_price vs total_unit_cost
- Table Unit Cost vs INA-CBG: Dari Patient Cases dengan INA-CBG

### Pathway & Mutu Tab

- Chart Compliance: Dari Patient Cases per pathway
- Chart LOS: Standard vs Actual dari Patient Cases
- Table Summary: Agregat per pathway
- Table Non-compliant Steps: (Placeholder - perlu data Case Details)

### Case Variance & JKN Tab

- Chart Distribution: Bucket variance dari Patient Cases
- KPI Tiles: Kasus dengan variance tinggi/rendah, total defisit
- Table Top Cases: 10 kasus dengan defisit terbesar
- Table By Pathway: Agregat variance per pathway

### Data & Proses Tab

- Status Cards: GL Expenses, Allocation Results, Unit Cost Calculations, Cost References
- Data Quality Checks: (Placeholder - perlu implementasi pre-allocation check)
- Process Logs: (Placeholder - perlu implementasi log system)

## Troubleshooting

### Error: Column 'standard_los' tidak ditemukan

Jalankan migration terlebih dahulu:

```bash
php artisan migrate
```

### Error: Foreign key constraint

Pastikan seeder master data dijalankan terlebih dahulu:

```bash
php artisan db:seed --class=HospitalsTableSeeder
php artisan db:seed --class=CostCentersTableSeeder
# ... dll
```

### Data tidak muncul di dashboard

1. Pastikan user sudah login dan memiliki hospital context
2. Pastikan data di-generate untuk periode yang sesuai dengan filter dashboard
3. Cek console browser untuk error JavaScript
4. Cek network tab untuk error API
