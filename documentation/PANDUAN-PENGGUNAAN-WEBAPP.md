# Panduan Penggunaan WebApp Costing, Tariff, dan Clinical Pathway Management System

## Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Persiapan Awal](#persiapan-awal)
3. [Setup Master Data](#setup-master-data)
4. [Input Data Operasional](#input-data-operasional)
5. [Cost Allocation](#cost-allocation)
6. [Unit Costing](#unit-costing)
7. [Tariff Management](#tariff-management)
8. [Clinical Pathway Management](#clinical-pathway-management)
9. [Patient Case Management](#patient-case-management)
10. [Analisis dan Reporting](#analisis-dan-reporting)

---

## Pendahuluan

WebApp Costing, Tariff, dan Clinical Pathway Management System adalah aplikasi web komprehensif untuk mengelola costing, penetapan tariff, clinical pathway, dan monitoring patient cases di rumah sakit. Sistem ini mengintegrasikan komponen finansial, operasional, dan klinis dalam satu platform digital.

### Tujuan Sistem

- Standardisasi pengelolaan cost centers, expenses, dan cost allocation
- Menyediakan perhitungan unit cost dan tariff yang transparan dan dapat diaudit
- Mengintegrasikan costing dengan desain clinical pathway untuk evidence-based care
- Memungkinkan monitoring biaya perawatan pasien aktual vs standar pathway
- Meningkatkan decision support untuk manajemen rumah sakit dan komite medis

### Role dan Akses

- **Admin** – Akses penuh, pengaturan sistem, manajemen user
- **Financial Manager** – Manajemen GL, persetujuan tariff
- **Costing Analyst** – Allocation, unit cost, simulasi tariff
- **Medical Committee** – Review & persetujuan clinical pathway
- **Pathway Designer** – Membuat/mengedit clinical pathways
- **Case Manager** – Input patient cases dan actual costs
- **Auditor** – Akses read-only ke logs & output finansial

---

## Persiapan Awal

### 1. Login ke Sistem

1. Buka aplikasi web di browser
2. Masukkan **Email** dan **Password** Anda
3. Klik tombol **Login**
4. Jika Anda adalah **Superadmin**, sistem akan meminta Anda untuk memilih rumah sakit terlebih dahulu

### 2. Memilih Konteks Rumah Sakit (Superadmin)

1. Setelah login sebagai Superadmin, Anda akan diarahkan ke halaman **Hospital Selection**
2. Pilih rumah sakit dari daftar yang tersedia
3. Klik **Select Hospital**
4. Sistem akan menyimpan pilihan Anda dan mengarahkan ke Dashboard

### 3. Mengakses Dashboard

1. Setelah login, Anda akan melihat **Dashboard** dengan ringkasan eksekutif
2. Dashboard menampilkan:
   - Total Cost Overview
   - Unit Cost Trends
   - Tariff Summary
   - Pathway Compliance Performance
   - Cost Overruns & Anomalies
   - Recent Activities

---

## Setup Master Data

Master data adalah data referensi yang harus disiapkan terlebih dahulu sebelum melakukan proses costing dan tariff. Setup master data dilakukan secara berurutan sesuai dependensi.

### Langkah 1: Setup Cost Centers

**Akses:** Admin, Financial Manager, Costing Analyst

**Tujuan:** Mendefinisikan unit-unit cost center di rumah sakit

1. **Akses Menu:**
   - Klik menu **Master Data** di sidebar
   - Pilih **Cost Centers**

2. **Membuat Cost Center:**
   - Klik tombol **Add New Cost Center**
   - Isi form:
     - **Code**: Kode cost center (contoh: `IGD`, `LAB`, `ADM`)
     - **Name**: Nama lengkap cost center
     - **Type**: Pilih `Support` atau `Revenue`
       - **Support**: Cost center pendukung (contoh: Administrasi, Housekeeping)
       - **Revenue**: Cost center yang menghasilkan pendapatan (contoh: IGD, Rawat Inap)
     - **Parent Cost Center**: (Opsional) Pilih cost center parent jika ada hierarki
     - **Active**: Centang untuk mengaktifkan
   - Klik **Save Cost Center**

3. **Mengelola Cost Centers:**
   - **View**: Klik tombol **View** untuk melihat detail
   - **Edit**: Klik tombol **Edit** untuk mengubah data
   - **Delete**: Klik tombol **Delete** untuk menghapus (hanya jika tidak digunakan)
   - **Filter**: Gunakan filter berdasarkan Type dan Status
   - **Export**: Klik **Export Excel** untuk mengekspor data

**Catatan Penting:**
- Pastikan semua cost center support dibuat terlebih dahulu sebelum membuat cost center revenue
- Cost center dengan type `support` akan dialokasikan ke cost center lain
- Cost center dengan type `revenue` menerima alokasi dari cost center support

### Langkah 2: Setup Expense Categories

**Akses:** Admin, Financial Manager, Costing Analyst

**Tujuan:** Mendefinisikan kategori pengeluaran (COA accounts) untuk GL

1. **Akses Menu:**
   - Klik menu **Master Data** di sidebar
   - Pilih **Expense Categories**

2. **Membuat Expense Category:**
   - Klik tombol **Add New Expense Category**
   - Isi form:
     - **Account Code**: Kode akun COA (contoh: `5101`, `5201`)
     - **Account Name**: Nama akun (contoh: `Gaji Dokter`, `BHP Medis`)
     - **Cost Type**: Pilih salah satu:
       - **Fixed**: Biaya tetap (contoh: Gaji, Sewa)
       - **Variable**: Biaya variabel (contoh: BHP, Obat)
       - **Semi Variable**: Biaya semi variabel (contoh: Listrik, Air)
     - **Allocation Category**: Pilih kategori alokasi:
       - **Gaji**: Untuk kategori gaji dan tunjangan
       - **BHP Medis**: Bahan habis pakai medis
       - **BHP Non Medis**: Bahan habis pakai non medis
       - **Depresiasi**: Biaya depresiasi aset
       - **Lain-lain**: Kategori lainnya
     - **Active**: Centang untuk mengaktifkan
   - Klik **Save Expense Category**

3. **Mengelola Expense Categories:**
   - Gunakan filter berdasarkan Cost Type, Allocation Category, dan Status
   - Export data ke Excel jika diperlukan

### Langkah 3: Setup Allocation Drivers

**Akses:** Admin, Financial Manager, Costing Analyst

**Tujuan:** Mendefinisikan basis alokasi untuk cost allocation (contoh: luas lantai, jumlah karyawan)

1. **Akses Menu:**
   - Klik menu **Master Data** di sidebar
   - Pilih **Allocation Drivers**

2. **Membuat Allocation Driver:**
   - Klik tombol **Add New Allocation Driver**
   - Isi form:
     - **Name**: Nama driver (contoh: `Luas Lantai`, `Jumlah Karyawan`, `Kilogram Laundry`)
     - **Unit Measurement**: Satuan pengukuran (contoh: `m2`, `orang`, `kg`)
     - **Description**: Deskripsi (opsional)
   - Klik **Save Allocation Driver**

3. **Contoh Allocation Drivers:**
   - **Luas Lantai** (m2) - untuk alokasi biaya gedung
   - **Jumlah Karyawan** (orang) - untuk alokasi biaya administrasi
   - **Kilogram Laundry** (kg) - untuk alokasi biaya laundry
   - **Jam Layanan** (jam) - untuk alokasi biaya layanan

### Langkah 4: Setup Tariff Classes

**Akses:** Admin, Financial Manager

**Tujuan:** Mendefinisikan kelas tariff untuk layanan/kamar

1. **Akses Menu:**
   - Klik menu **Master Data** di sidebar
   - Pilih **Tariff Classes**

2. **Membuat Tariff Class:**
   - Klik tombol **Add New Tariff Class**
   - Isi form:
     - **Code**: Kode kelas (contoh: `KLS1`, `KLS3`, `VIP`)
     - **Name**: Nama kelas (contoh: `Kelas 1`, `Kelas 3`, `VIP`)
     - **Description**: Deskripsi (opsional)
     - **Active**: Centang untuk mengaktifkan
   - Klik **Save Tariff Class**

### Langkah 5: Setup Cost References

**Akses:** Admin, Financial Manager, Costing Analyst

**Tujuan:** Mendefinisikan katalog layanan/item (chargemaster) dengan metadata costing

1. **Akses Menu:**
   - Klik menu **Master Data** di sidebar
   - Pilih **Cost References**

2. **Membuat Cost Reference:**
   - Klik tombol **Add New Cost Reference**
   - Isi form:
     - **Service Code**: Kode layanan/prosedur
     - **Service Description**: Deskripsi layanan
     - **Standard Cost**: Biaya standar/perkiraan
     - **Unit**: Satuan (contoh: `test`, `kali`, `paket`)
     - **Source**: Sumber data (contoh: `SIMRS`, `MANUAL`)
   - Klik **Save Cost Reference**

3. **Mengupdate Cost Reference dengan Master Data:**
   - Edit Cost Reference yang sudah ada
   - Tambahkan informasi:
     - **Cost Center**: Pilih cost center yang terkait
     - **Expense Category**: Pilih kategori expense yang terkait
     - **Purchase Price**: Harga beli terakhir
     - **Selling Price Unit**: Harga jual per unit
     - **Selling Price Total**: Harga jual total (untuk bundle)
     - **Is Bundle**: Centang jika ini adalah bundle/package
     - **Active From/To**: Tanggal aktif layanan

4. **Import dari SIMRS:**
   - Gunakan fitur **SIMRS Sync** untuk mengimpor data dari SIMRS
   - Data yang diimpor akan otomatis terhubung dengan Cost References

### Langkah 6: Setup JKN CBG Codes (Opsional)

**Akses:** Semua role (read), Admin (CRUD)

**Tujuan:** Mengelola kode INA-CBG / case-based grouping

1. **Akses Menu:**
   - Klik menu **Master Data** di sidebar
   - Pilih **JKN CBG Codes**

2. **Membuat JKN CBG Code:**
   - Klik tombol **Add New JKN CBG Code**
   - Isi form:
     - **CBG Code**: Kode INA-CBG
     - **Description**: Deskripsi
     - **Base Tariff**: Tariff dasar nasional
   - Klik **Save**

---

## Input Data Operasional

Setelah master data selesai disetup, langkah berikutnya adalah menginput data operasional untuk periode tertentu.

### Langkah 1: Input GL Expenses

**Akses:** Financial Manager, Costing Analyst

**Tujuan:** Menginput pengeluaran aktual per cost center dan expense category per periode

1. **Akses Menu:**
   - Klik menu **GL & Expenses** di sidebar
   - Pilih **GL Expenses**

2. **Input Manual:**
   - Klik tombol **Add New GL Expense**
   - Isi form:
     - **Year**: Pilih tahun (contoh: 2025)
     - **Month**: Pilih bulan (1-12)
     - **Cost Center**: Pilih cost center
     - **Expense Category**: Pilih kategori expense
     - **Amount**: Masukkan jumlah (Rp)
   - Klik **Save GL Expense**

3. **Import dari Excel:**
   - Klik tombol **Import Excel**
   - Pilih **Year** dan **Month** untuk periode data
   - Upload file Excel dengan format:
     - **Kolom A**: Cost Center Code
     - **Kolom B**: Expense Category Code
     - **Kolom C**: Amount
   - Klik **Import GL Expenses**
   - Sistem akan memvalidasi dan mengimpor data

4. **Validasi Data:**
   - Pastikan semua cost center dan expense category sudah memiliki data
   - Gunakan filter untuk melihat data per periode, cost center, atau kategori
   - Export data untuk validasi eksternal

**Catatan Penting:**
- Pastikan data GL Expenses lengkap untuk semua cost center dan expense category
- Data harus diinput per bulan untuk akurasi costing
- Validasi bahwa total GL Expenses sesuai dengan laporan keuangan

### Langkah 2: Input Driver Statistics

**Akses:** Financial Manager, Costing Analyst

**Tujuan:** Menginput nilai driver untuk cost allocation per periode

1. **Akses Menu:**
   - Klik menu **GL & Expenses** di sidebar
   - Pilih **Driver Statistics**

2. **Input Manual:**
   - Klik tombol **Add New Driver Statistic**
   - Isi form:
     - **Year**: Pilih tahun
     - **Month**: Pilih bulan
     - **Cost Center**: Pilih cost center
     - **Allocation Driver**: Pilih driver (contoh: Luas Lantai)
     - **Value**: Masukkan nilai (contoh: 500 untuk 500 m2)
   - Klik **Save Driver Statistic**

3. **Import dari Excel:**
   - Klik tombol **Import Excel**
   - Pilih **Year** dan **Month**
   - Upload file Excel dengan format:
     - **Kolom A**: Cost Center Code
     - **Kolom B**: Allocation Driver Name
     - **Kolom C**: Value
   - Klik **Import Driver Statistics**

**Contoh Data:**
- Cost Center: IGD, Driver: Luas Lantai, Value: 200 m2
- Cost Center: LAB, Driver: Jumlah Karyawan, Value: 15 orang
- Cost Center: Housekeeping, Driver: Kilogram Laundry, Value: 1000 kg

### Langkah 3: Input Service Volumes

**Akses:** Financial Manager, Costing Analyst

**Tujuan:** Menginput volume layanan per cost reference dan tariff class per periode

1. **Akses Menu:**
   - Klik menu **GL & Expenses** di sidebar
   - Pilih **Service Volumes**
   - Atau akses melalui menu **Unit Costing** → **Service Volumes**

2. **Input Manual:**
   - Klik tombol **Add New Service Volume**
   - Isi form:
     - **Year**: Pilih tahun
     - **Month**: Pilih bulan
     - **Service**: Pilih cost reference (layanan)
     - **Tariff Class**: (Opsional) Pilih kelas tariff
     - **Total Quantity**: Masukkan total volume (contoh: 150 untuk 150 kali layanan)
   - Klik **Save Service Volume**

3. **Import dari Excel:**
   - Klik tombol **Import Excel**
   - Pilih **Year** dan **Month**
   - Upload file Excel dengan format:
     - **Kolom A**: Service Code
     - **Kolom B**: Tariff Class Code (opsional, bisa dikosongkan)
     - **Kolom C**: Total Quantity
   - Klik **Import Service Volumes**

**Catatan Penting:**
- Service Volumes diperlukan untuk menghitung unit cost per layanan
- Pastikan data volume lengkap untuk semua layanan yang akan dihitung unit cost-nya
- Data volume dapat berbeda per tariff class

---

## Cost Allocation

Cost allocation adalah proses mengalokasikan biaya dari cost center support ke cost center revenue menggunakan metode step-down.

### Langkah 1: Setup Allocation Maps

**Akses:** Costing Analyst, Financial Manager

**Tujuan:** Mendefinisikan mapping alokasi dari cost center support ke cost center lain

1. **Akses Menu:**
   - Klik menu **Allocation** di sidebar
   - Pilih **Allocation Maps**

2. **Membuat Allocation Map:**
   - Klik tombol **Add New Allocation Map**
   - Isi form:
     - **Source Cost Center**: Pilih cost center support yang akan dialokasikan
     - **Allocation Driver**: Pilih driver yang digunakan untuk alokasi
     - **Step Sequence**: Masukkan urutan step (1, 2, 3, dst.)
       - Step 1: Alokasi pertama
       - Step 2: Alokasi kedua (setelah step 1 selesai)
       - Dan seterusnya
   - Klik **Save Allocation Map**

**Contoh Allocation Map:**
- **Step 1**: Housekeeping → dialokasikan ke semua cost center menggunakan driver "Luas Lantai"
- **Step 2**: Administrasi → dialokasikan ke semua cost center menggunakan driver "Jumlah Karyawan"
- **Step 3**: Maintenance → dialokasikan ke semua cost center menggunakan driver "Luas Lantai"

**Catatan Penting:**
- Urutan step sangat penting dalam step-down method
- Cost center yang sudah dialokasikan di step sebelumnya tidak akan menerima alokasi lagi
- Pastikan semua cost center support sudah memiliki allocation map

### Langkah 2: Run Allocation

**Akses:** Costing Analyst, Financial Manager

**Tujuan:** Menjalankan engine cost allocation untuk periode tertentu

1. **Akses Menu:**
   - Klik menu **Allocation** di sidebar
   - Pilih **Run Allocation**

2. **Menjalankan Allocation:**
   - Pilih **Year** dan **Month** untuk periode yang akan dialokasikan
   - Review **Allocation Configuration**:
     - Pastikan semua allocation maps sudah benar
     - Pastikan GL Expenses sudah lengkap
     - Pastikan Driver Statistics sudah lengkap
   - Klik tombol **Run Allocation**
   - Sistem akan menampilkan progress
   - Tunggu hingga proses selesai

3. **Proses Allocation:**
   - Sistem akan menghitung total cost per cost center support dari GL Expenses
   - Untuk setiap step allocation:
     - Mengambil driver statistics untuk driver yang digunakan
     - Menghitung proporsi alokasi ke setiap target cost center
     - Menyimpan hasil alokasi ke tabel `allocation_results`
   - Cost center yang sudah dialokasikan tidak akan menerima alokasi di step berikutnya

4. **Validasi Hasil:**
   - Pastikan total biaya sebelum dan sesudah allocation sama
   - Review allocation results untuk memastikan alokasi masuk akal

### Langkah 3: Review Allocation Results

**Akses:** Costing Analyst, Financial Manager, Admin

**Tujuan:** Melihat hasil cost allocation

1. **Akses Menu:**
   - Klik menu **Allocation** di sidebar
   - Pilih **Allocation Results**

2. **Melihat Results:**
   - Filter berdasarkan **Year**, **Month**, dan **Version**
   - Tampilan menampilkan:
     - **Source Cost Center**: Cost center yang dialokasikan
     - **Target Cost Center**: Cost center yang menerima alokasi
     - **Allocation Step**: Step alokasi
     - **Allocated Amount**: Jumlah yang dialokasikan
   - Gunakan filter untuk melihat alokasi per source atau target

3. **Export Results:**
   - Klik **Export Excel** untuk mengekspor hasil alokasi
   - Gunakan untuk analisis lebih lanjut atau pelaporan

---

## Unit Costing

Unit costing adalah proses menghitung biaya per unit layanan berdasarkan total cost dan volume layanan.

### Langkah 1: Validasi Service Volumes

**Akses:** Costing Analyst, Financial Manager

**Tujuan:** Memastikan data service volumes sudah lengkap

1. **Akses Menu:**
   - Klik menu **Unit Costing** di sidebar
   - Pilih **Service Volumes**

2. **Validasi Data:**
   - Pastikan semua layanan yang akan dihitung unit cost-nya sudah memiliki data volume
   - Pastikan data volume sesuai dengan periode yang akan dihitung
   - Export data untuk validasi eksternal jika diperlukan

### Langkah 2: Calculate Unit Cost

**Akses:** Costing Analyst, Financial Manager

**Tujuan:** Menghitung unit cost per layanan untuk periode tertentu

1. **Akses Menu:**
   - Klik menu **Unit Costing** di sidebar
   - Pilih **Calculate Unit Cost**

2. **Menjalankan Perhitungan:**
   - Pilih **Year** dan **Month** untuk periode yang akan dihitung
   - Masukkan **Version Label** (contoh: `UC_2025_JAN`, `UC_2025_Q1`)
   - Review **Configuration**:
     - Pastikan GL Expenses sudah lengkap
     - Pastikan Allocation Results sudah ada
     - Pastikan Service Volumes sudah lengkap
   - Klik tombol **Run Unit Cost Calculation**
   - Sistem akan menampilkan progress
   - Tunggu hingga proses selesai

3. **Proses Perhitungan:**
   - Sistem menghitung **Total Cost per Cost Center**:
     - Direct cost dari GL Expenses
     - Overhead dari Allocation Results (jumlah allocated_amount yang diterima)
   - Mapping cost center ke layanan melalui Cost References
   - Distribusi cost ke setiap layanan berdasarkan Service Volumes:
     - **Direct Cost Material**: Dari expense category dengan allocation_category = 'bhp_medis' atau 'bhp_non_medis'
     - **Direct Cost Labor**: Dari expense category dengan allocation_category = 'gaji'
     - **Indirect Cost Overhead**: Dari allocation results
   - Menghitung **Total Unit Cost** = Direct Material + Direct Labor + Indirect Overhead
   - Menyimpan hasil ke tabel `unit_cost_calculations` dengan version label

### Langkah 3: Review Unit Cost Results

**Akses:** Costing Analyst, Financial Manager, Admin

**Tujuan:** Melihat hasil perhitungan unit cost

1. **Akses Menu:**
   - Klik menu **Unit Costing** di sidebar
   - Pilih **Unit Cost Results**

2. **Melihat Results:**
   - Filter berdasarkan **Version Label**, **Period**, dan **Service**
   - Tampilan menampilkan:
     - **Service Code & Description**: Layanan
     - **Period**: Periode perhitungan
     - **Direct Cost Material**: Biaya material langsung
     - **Direct Cost Labor**: Biaya tenaga kerja langsung
     - **Indirect Cost Overhead**: Biaya overhead tidak langsung
     - **Total Unit Cost**: Total biaya per unit
     - **Version Label**: Versi perhitungan
   - Klik **View** untuk melihat detail breakdown per cost center

3. **Audit Trail:**
   - Sistem menyediakan audit trail yang menunjukkan:
     - Cost center mana yang berkontribusi pada unit cost
     - Kategori expense yang digunakan
     - Alokasi yang diterima dari cost center support

4. **Compare Versions:**
   - Gunakan fitur compare untuk membandingkan unit cost antar versi
   - Analisis perubahan unit cost dari waktu ke waktu

5. **Export Results:**
   - Klik **Export Excel** atau **Export PDF** untuk mengekspor hasil
   - Gunakan untuk analisis lebih lanjut atau pelaporan

---

## Tariff Management

Tariff management adalah proses menetapkan tariff final untuk layanan berdasarkan unit cost dan margin.

### Langkah 1: Tariff Simulation

**Akses:** Financial Manager, Costing Analyst

**Tujuan:** Simulasi tariff dengan berbagai skenario margin

1. **Akses Menu:**
   - Klik menu **Tariff** di sidebar
   - Pilih **Tariff Simulation**

2. **Membuat Simulasi:**
   - Pilih **Unit Cost Version** yang akan digunakan
   - Set **Margin Percentage**:
     - **Global Margin**: Margin yang sama untuk semua layanan
     - **Per Service Margin**: Margin berbeda per layanan
   - Klik **Preview Tariff Calculation**
   - Sistem akan menampilkan:
     - Base Unit Cost
     - Margin Amount
     - Simulated Tariff Price

3. **Compare Scenarios:**
   - Buat beberapa skenario dengan margin berbeda
   - Bandingkan hasil simulasi
   - Pilih skenario yang paling sesuai

4. **Export Simulation:**
   - Export hasil simulasi ke Excel untuk review lebih lanjut

### Langkah 2: Create Final Tariffs

**Akses:** Financial Manager, Admin

**Tujuan:** Menetapkan tariff final berdasarkan SK/approval

1. **Akses Menu:**
   - Klik menu **Tariff** di sidebar
   - Pilih **Final Tariffs**

2. **Membuat Final Tariff:**
   - Klik tombol **Add New Final Tariff**
   - Isi form:
     - **Service**: Pilih cost reference (layanan)
     - **Tariff Class**: (Opsional) Pilih kelas tariff
     - **Unit Cost Calculation**: Pilih versi unit cost yang digunakan
     - **Base Unit Cost**: Otomatis terisi dari unit cost calculation
     - **Margin Percentage**: Masukkan margin (contoh: 0.20 untuk 20%)
     - **Jasa Sarana**: Komponen fasilitas (opsional)
     - **Jasa Pelayanan**: Komponen profesional (opsional)
     - **Final Tariff Price**: Otomatis terhitung atau bisa diubah manual
     - **SK Number**: Nomor SK/approval (contoh: `SK/RS/2025/001`)
     - **Effective Date**: Tanggal mulai berlaku
     - **Expired Date**: (Opsional) Tanggal berakhir
   - Klik **Save Final Tariff**

3. **Approval Workflow:**
   - Setelah dibuat, tariff dapat melalui proses approval
   - Status: Draft → Review → Approved
   - Hanya tariff yang approved yang dapat digunakan

4. **Mengelola Final Tariffs:**
   - **View**: Lihat detail tariff
   - **Edit**: Ubah tariff (jika belum digunakan)
   - **Deactivate**: Nonaktifkan tariff yang sudah expired
   - **Export**: Export ke Excel/PDF untuk publikasi

### Langkah 3: Tariff Explorer

**Akses:** Semua role (read)

**Tujuan:** Mencari dan melihat tariff yang berlaku

1. **Akses Menu:**
   - Klik menu **Tariff** di sidebar
   - Pilih **Tariff Explorer**

2. **Mencari Tariff:**
   - Gunakan search box untuk mencari berdasarkan Service Code atau Description
   - Filter berdasarkan **Tariff Class** dan **Effective Date**
   - Tampilan menampilkan:
     - Service Code & Description
     - Tariff Class
     - Final Tariff Price
     - Effective Date
     - SK Number

3. **View Tariff History:**
   - Klik pada layanan untuk melihat history tariff
   - Bandingkan tariff dari waktu ke waktu

4. **Compare dengan INA-CBG:**
   - Sistem dapat menampilkan perbandingan dengan tariff INA-CBG
   - Analisis selisih antara internal tariff dan INA-CBG

---

## Clinical Pathway Management

Clinical pathway management adalah proses membuat dan mengelola clinical pathway yang terintegrasi dengan costing dan tariff.

### Langkah 1: Create Clinical Pathway

**Akses:** Pathway Designer, Medical Committee

**Tujuan:** Membuat clinical pathway untuk diagnosis tertentu

1. **Akses Menu:**
   - Klik menu **Clinical Pathways** di sidebar
   - Pilih **Pathway List**

2. **Membuat Pathway:**
   - Klik tombol **Add New Pathway**
   - Isi form:
     - **Name**: Nama pathway (contoh: `Pathway Pneumonia Dewasa`)
     - **Description**: Deskripsi pathway
     - **Diagnosis Code**: Kode ICD-10 (contoh: `J18.9`)
     - **INA-CBG Code**: (Opsional) Kode INA-CBG terkait
     - **Expected LOS Days**: Expected length of stay (hari)
     - **Version**: Versi pathway (contoh: `1.0`)
     - **Effective Date**: Tanggal mulai berlaku
     - **Status**: Pilih `Draft`
   - Klik **Save Pathway**

### Langkah 2: Add Pathway Steps

**Akses:** Pathway Designer, Medical Committee

**Tujuan:** Menambahkan langkah-langkah dalam clinical pathway

1. **Akses Pathway Builder:**
   - Dari Pathway List, klik **Builder** pada pathway yang ingin diedit
   - Atau klik **Edit** kemudian pilih tab **Builder**

2. **Menambahkan Step:**
   - Klik tombol **Add Step**
   - Isi form:
     - **Category**: Kategori step (contoh: `Lab`, `Radiologi`, `Obat`, `Tindakan`)
     - **Description**: Deskripsi step
     - **Service Code**: Pilih atau ketik service code dari Cost References
       - Sistem akan auto-complete dari Cost References
       - Setelah dipilih, sistem akan auto-fill:
         - **Cost Reference**: Link ke cost reference
         - **Estimated Cost**: Dari unit cost terbaru (jika ada)
         - **Cost Center**: Dari cost reference
     - **Quantity**: Jumlah kali step dilakukan (contoh: `1`, `2`)
     - **Is Mandatory**: Centang jika step wajib dilakukan
     - **Criteria**: (Opsional) Kriteria klinis untuk step ini
     - **Step Order**: Urutan step (1, 2, 3, dst.)
   - Klik **Save Step**

3. **Mengatur Urutan Step:**
   - Gunakan tombol **Up/Down** untuk mengubah urutan
   - Atau drag and drop step untuk mengatur ulang

4. **Import Steps dari Template:**
   - Klik **Import from Template**
   - Upload file Excel dengan format template
   - Sistem akan mengimpor steps secara bulk

5. **Recalculate Pathway Summary:**
   - Setelah menambahkan/mengubah steps, klik **Recalculate Summary**
   - Sistem akan menghitung:
     - **Estimated Total Cost**: Jumlah total biaya estimasi
     - **Estimated Total Tariff**: Jumlah total tariff estimasi (jika final tariffs sudah ada)

### Langkah 3: Pathway Approval

**Akses:** Medical Committee, Admin

**Tujuan:** Menyetujui clinical pathway

1. **Review Pathway:**
   - Akses pathway yang statusnya `Review` atau `Draft`
   - Review semua steps dan estimasi biaya
   - Pastikan pathway sudah lengkap dan sesuai standar

2. **Approve Pathway:**
   - Klik tombol **Approve**
   - (Opsional) Tambahkan komentar
   - Klik **Confirm Approval**
   - Status pathway akan berubah menjadi `Approved`

3. **Reject Pathway:**
   - Jika pathway perlu direvisi, klik **Reject**
   - Tambahkan komentar alasan penolakan
   - Status pathway akan kembali ke `Draft`

### Langkah 4: View Pathway Summary

**Akses:** Semua role (read)

**Tujuan:** Melihat ringkasan pathway termasuk estimasi biaya dan tariff

1. **Akses Pathway:**
   - Dari Pathway List, klik **View** pada pathway yang diinginkan

2. **Pathway Summary:**
   - Tampilan menampilkan:
     - **Pathway Information**: Nama, diagnosis, versi, status
     - **Estimated Total Cost**: Total biaya estimasi
     - **Estimated Total Tariff**: Total tariff estimasi
     - **Expected LOS**: Expected length of stay
     - **Steps Breakdown**: Daftar semua steps dengan biaya per step

3. **Compare dengan Unit Cost Version:**
   - Pilih **Unit Cost Version** untuk melihat estimasi berdasarkan versi tertentu
   - Bandingkan estimasi antar versi

4. **Export Pathway:**
   - Klik **Export PDF** atau **Export DOCX** untuk mengekspor pathway
   - Dokumen dapat digunakan untuk dokumentasi atau presentasi

---

## Patient Case Management

Patient case management adalah proses mencatat kasus pasien aktual dan membandingkannya dengan clinical pathway.

### Langkah 1: Register Patient Case

**Akses:** Case Manager, Medical Records, Costing Analyst

**Tujuan:** Mencatat kasus pasien baru

1. **Akses Menu:**
   - Klik menu **Patient Cases** di sidebar
   - Pilih **Case List**

2. **Membuat Case:**
   - Klik tombol **Add New Case**
   - Isi form:
     - **Patient ID**: ID pasien dari HIS (opsional)
     - **Medical Record Number**: Nomor rekam medis
     - **Clinical Pathway**: (Opsional) Pilih pathway yang terkait
     - **Admission Date**: Tanggal masuk
     - **Primary Diagnosis**: Kode ICD-10 diagnosis utama
     - **INA-CBG Code**: (Opsional) Kode INA-CBG
     - **Reimbursement Scheme**: Skema pembayaran (contoh: `JKN`, `Umum`, `Asuransi`)
     - **Unit Cost Version**: Versi unit cost yang akan digunakan (contoh: `UC_2025_JAN`)
   - Klik **Save Case**

3. **Upload Cases dari Excel:**
   - Klik **Upload Cases**
   - Download template Excel terlebih dahulu
   - Isi template dengan data cases
   - Upload file Excel
   - Sistem akan mengimpor cases secara bulk

### Langkah 2: Fill Case Details

**Akses:** Case Manager, Medical Records

**Tujuan:** Mencatat detail layanan yang dilakukan pada pasien

1. **Akses Case:**
   - Dari Case List, klik **View** pada case yang ingin diisi detailnya
   - Atau klik **Edit** kemudian pilih tab **Case Details**

2. **Generate Planned Steps dari Pathway:**
   - Jika case terkait dengan clinical pathway, klik **Copy Steps from Pathway**
   - Sistem akan membuat case details berdasarkan pathway steps
   - Semua steps akan berstatus `planned` secara default

3. **Menambahkan Case Detail:**
   - Klik **Add Case Detail**
   - Isi form:
     - **Service Item**: Nama layanan (human-readable)
     - **Service Code**: Pilih atau ketik service code
       - Sistem akan auto-complete dari Cost References
       - Setelah dipilih, sistem akan auto-fill:
         - **Cost Reference**: Link ke cost reference
         - **Unit Cost Applied**: Dari unit cost version yang dipilih
         - **Tariff Applied**: Dari final tariffs yang berlaku
     - **Status**: Pilih `planned`, `done`, atau `skipped`
     - **Performed**: Centang jika layanan sudah dilakukan
     - **Quantity**: Jumlah layanan
     - **Actual Cost**: Biaya aktual (bisa sama dengan unit cost atau diubah manual)
     - **Service Date**: Tanggal layanan dilakukan
   - Klik **Save Case Detail**

4. **Mengupdate Status:**
   - Setelah layanan dilakukan, ubah status menjadi `done`
   - Centang checkbox **Performed**
   - Isi **Service Date** dan **Actual Cost** jika berbeda dari estimasi

5. **Recalculate Case Totals:**
   - Setelah menambahkan/mengubah case details, klik **Recalculate**
   - Sistem akan menghitung:
     - **Actual Total Cost**: Jumlah total biaya aktual
     - **Calculated Total Tariff**: Jumlah total tariff yang dihitung
     - **Compliance Percentage**: Persentase compliance dengan pathway
     - **Cost Variance**: Selisih antara actual cost dan estimated cost

### Langkah 3: View Case Analysis

**Akses:** Semua role (read)

**Tujuan:** Melihat analisis perbandingan planned vs actual

1. **Akses Case:**
   - Dari Case List, klik **View** pada case yang ingin dianalisis
   - Pilih tab **Analysis**

2. **Planned vs Actual Steps:**
   - Tampilan menampilkan:
     - **Planned Steps**: Steps yang direncanakan dari pathway
     - **Actual Steps**: Steps yang benar-benar dilakukan
     - **Skipped Steps**: Steps yang direncanakan tapi tidak dilakukan
     - **Additional Steps**: Steps yang dilakukan tapi tidak direncanakan

3. **Cost Comparison:**
   - **Estimated Cost**: Total biaya estimasi dari pathway
   - **Actual Cost**: Total biaya aktual
   - **Cost Variance**: Selisih antara actual dan estimated
   - **Variance Percentage**: Persentase variance

4. **Compliance Analysis:**
   - **Compliance Percentage**: Persentase compliance dengan pathway
   - Breakdown compliance per kategori step (Lab, Radiologi, Obat, dll.)
   - Highlight steps yang tidak sesuai pathway

5. **Tariff Comparison:**
   - **Calculated Total Tariff**: Total tariff yang dihitung
   - **INA-CBG Tariff**: Tariff INA-CBG (jika ada)
   - **Variance**: Selisih antara calculated dan INA-CBG

6. **Export Analysis:**
   - Klik **Export Analysis Report** untuk mengekspor laporan analisis
   - Format: PDF atau Excel

---

## Analisis dan Reporting

Sistem menyediakan berbagai laporan dan analisis untuk mendukung pengambilan keputusan.

### Laporan 1: Pathway Compliance

**Akses:** Semua role

**Tujuan:** Melihat tingkat compliance dengan clinical pathway

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Pathway Compliance**

2. **Filter Laporan:**
   - Pilih **Period** (Year, Month)
   - Pilih **Pathway** (spesifik atau semua)
   - Pilih **Department** (jika ada grouping)

3. **Tampilan Laporan:**
   - **Compliance by Pathway**: Persentase compliance per pathway
   - **Compliance Trend**: Trend compliance dari waktu ke waktu
   - **Compliance by Department**: Compliance per departemen
   - **Top Non-Compliant Steps**: Steps yang paling sering tidak dilakukan

4. **Export:**
   - Klik **Export Excel** atau **Export PDF**

### Laporan 2: Case Variance Analysis

**Akses:** Semua role

**Tujuan:** Menganalisis variance biaya aktual vs estimasi

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Case Variance Analysis**

2. **Filter Laporan:**
   - Pilih **Period**
   - Pilih **Pathway**
   - Pilih **Department**

3. **Tampilan Laporan:**
   - **Cost Variance by Pathway**: Variance per pathway
   - **Cost Variance Trend**: Trend variance dari waktu ke waktu
   - **Variance by Department**: Variance per departemen
   - **Top Variance Cases**: Cases dengan variance terbesar

4. **Analisis:**
   - Identifikasi pathway dengan variance tinggi
   - Analisis penyebab variance
   - Rekomendasi perbaikan

### Laporan 3: Pathway Performance

**Akses:** Semua role

**Tujuan:** Melihat performa clinical pathway

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Pathway Performance**

2. **Tampilan Laporan:**
   - **Pathway Performance Metrics**: Metrik performa per pathway
   - **LOS Analysis**: Analisis length of stay
   - **Cost Efficiency**: Efisiensi biaya per pathway
   - **Compliance vs Cost**: Hubungan antara compliance dan biaya

### Laporan 4: Cost Center Performance

**Akses:** Financial Manager, Admin

**Tujuan:** Melihat performa cost center

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Cost Center Performance**

2. **Tampilan Laporan:**
   - **Pre/Post Allocation Cost Report**: Biaya sebelum dan sesudah allocation
   - **Cost by Expense Category**: Breakdown biaya per kategori
   - **Trend Analysis**: Trend biaya dari waktu ke waktu

### Laporan 5: Allocation Results Summary

**Akses:** Costing Analyst, Financial Manager

**Tujuan:** Melihat ringkasan hasil cost allocation

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Allocation Results Summary**

2. **Tampilan Laporan:**
   - **Allocation Summary by Period**: Ringkasan alokasi per periode
   - **Allocation Flow Diagram**: Diagram alur alokasi
   - **Compare Versions**: Perbandingan hasil alokasi antar versi

### Laporan 6: Unit Cost Summary

**Akses:** Costing Analyst, Financial Manager

**Tujuan:** Melihat ringkasan unit cost

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Unit Cost Summary**

2. **Tampilan Laporan:**
   - **Unit Cost by Department**: Unit cost per departemen
   - **Unit Cost Trend**: Trend unit cost dari waktu ke waktu
   - **Breakdown Analysis**: Breakdown direct material, labor, dan overhead

### Laporan 7: Tariff Comparison

**Akses:** Financial Manager, Admin

**Tujuan:** Membandingkan tariff internal dengan INA-CBG

1. **Akses Menu:**
   - Klik menu **Reports** di sidebar
   - Pilih **Tariff Comparison**

2. **Tampilan Laporan:**
   - **Internal vs INA-CBG Comparison**: Perbandingan tariff
   - **Tariff by Class**: Tariff per kelas
   - **Margin Analysis**: Analisis margin

---

## Workflow Lengkap: Dari Setup hingga Analisis

Berikut adalah workflow lengkap dari awal setup hingga menghasilkan analisis penggunaan pathway:

### Fase 1: Setup Awal (Sekali)

1. **Login** sebagai Admin
2. **Setup Master Data:**
   - Cost Centers
   - Expense Categories
   - Allocation Drivers
   - Tariff Classes
   - Cost References
   - JKN CBG Codes (opsional)
3. **Setup Allocation Maps:**
   - Definisikan mapping alokasi dari support ke revenue centers

### Fase 2: Input Data Bulanan (Setiap Bulan)

1. **Input GL Expenses** untuk bulan tersebut
2. **Input Driver Statistics** untuk bulan tersebut
3. **Input Service Volumes** untuk bulan tersebut

### Fase 3: Cost Allocation (Setiap Bulan)

1. **Run Allocation** untuk bulan tersebut
2. **Review Allocation Results**
3. **Validasi** hasil alokasi

### Fase 4: Unit Costing (Setiap Bulan atau Quarterly)

1. **Calculate Unit Cost** untuk periode tersebut
2. **Review Unit Cost Results**
3. **Validasi** unit cost yang dihasilkan

### Fase 5: Tariff Management (Periodik)

1. **Tariff Simulation** dengan berbagai skenario margin
2. **Create Final Tariffs** berdasarkan SK/approval
3. **Publish Tariffs** untuk digunakan

### Fase 6: Clinical Pathway (Sekali atau Saat Update)

1. **Create Clinical Pathway** untuk diagnosis tertentu
2. **Add Pathway Steps** dengan link ke cost references
3. **Recalculate Pathway Summary** untuk mendapatkan estimasi biaya
4. **Pathway Approval** oleh Medical Committee

### Fase 7: Patient Case Management (Ongoing)

1. **Register Patient Case** yang terkait dengan pathway
2. **Fill Case Details** dengan layanan yang dilakukan
3. **Recalculate Case Totals** untuk mendapatkan variance
4. **View Case Analysis** untuk melihat compliance dan variance

### Fase 8: Analisis dan Reporting (Periodik)

1. **Pathway Compliance Report** untuk melihat tingkat compliance
2. **Case Variance Analysis** untuk menganalisis variance
3. **Pathway Performance** untuk melihat performa pathway
4. **Cost Center Performance** untuk melihat performa cost center
5. **Unit Cost Summary** untuk melihat trend unit cost
6. **Tariff Comparison** untuk membandingkan dengan INA-CBG

---

## Tips dan Best Practices

### 1. Master Data Management

- **Konsistensi**: Pastikan kode dan nama konsisten di seluruh sistem
- **Validasi**: Validasi data sebelum digunakan dalam perhitungan
- **Backup**: Backup master data secara berkala
- **Versioning**: Gunakan versioning untuk perubahan besar

### 2. Data Input

- **Ketepatan Waktu**: Input data tepat waktu setiap bulan
- **Validasi**: Validasi data dengan laporan keuangan
- **Completeness**: Pastikan semua data lengkap sebelum running allocation/costing
- **Documentation**: Dokumentasikan asumsi dan pengecualian

### 3. Cost Allocation

- **Review Maps**: Review allocation maps secara berkala
- **Validate Drivers**: Pastikan driver statistics akurat
- **Check Results**: Selalu cek hasil alokasi untuk memastikan masuk akal
- **Version Control**: Gunakan versioning untuk tracking perubahan

### 4. Unit Costing

- **Consistency**: Gunakan versi unit cost yang konsisten untuk periode tertentu
- **Review Breakdown**: Review breakdown direct/indirect cost
- **Compare Versions**: Bandingkan unit cost antar versi untuk analisis trend
- **Documentation**: Dokumentasikan asumsi perhitungan

### 5. Tariff Management

- **Approval Process**: Ikuti proses approval yang telah ditetapkan
- **Effective Dates**: Pastikan effective dates benar
- **Version Control**: Maintain history tariff untuk audit
- **Communication**: Komunikasikan perubahan tariff ke stakeholders

### 6. Clinical Pathway

- **Evidence-Based**: Pastikan pathway berdasarkan evidence-based medicine
- **Regular Review**: Review pathway secara berkala
- **Update Costs**: Update estimasi biaya saat unit cost berubah
- **Compliance Monitoring**: Monitor compliance secara berkala

### 7. Patient Case Management

- **Timeliness**: Input case details tepat waktu
- **Accuracy**: Pastikan data akurat dan lengkap
- **Link to Pathway**: Selalu link case ke pathway jika memungkinkan
- **Variance Analysis**: Analisis variance untuk perbaikan berkelanjutan

### 8. Reporting

- **Regular Reports**: Generate reports secara berkala
- **Trend Analysis**: Analisis trend dari waktu ke waktu
- **Action Items**: Identifikasi action items dari laporan
- **Stakeholder Communication**: Komunikasikan findings ke stakeholders

---

## Troubleshooting

### Masalah: Data tidak muncul setelah import

**Solusi:**
- Pastikan format file Excel sesuai template
- Pastikan kode (cost center, expense category, dll.) sudah ada di master data
- Cek error log untuk detail error
- Validasi data di file Excel sebelum import

### Masalah: Allocation results tidak masuk akal

**Solusi:**
- Review allocation maps, pastikan step sequence benar
- Validasi driver statistics, pastikan nilai benar
- Cek GL expenses, pastikan data lengkap
- Review logic allocation jika diperlukan

### Masalah: Unit cost terlalu tinggi/rendah

**Solusi:**
- Review service volumes, pastikan volume benar
- Cek allocation results, pastikan alokasi benar
- Review GL expenses, pastikan biaya benar
- Analisis breakdown direct/indirect cost

### Masalah: Pathway compliance rendah

**Solusi:**
- Review pathway steps, pastikan realistis
- Analisis cases dengan compliance rendah
- Identifikasi penyebab non-compliance
- Update pathway jika diperlukan

### Masalah: Cost variance tinggi

**Solusi:**
- Analisis variance per kategori
- Identifikasi penyebab variance
- Review unit cost yang digunakan
- Update estimasi pathway jika diperlukan

---

## Kesimpulan

WebApp Costing, Tariff, dan Clinical Pathway Management System adalah tool yang powerful untuk mengelola costing, tariff, dan clinical pathway di rumah sakit. Dengan mengikuti langkah-langkah di atas, Anda dapat:

1. Setup sistem dari awal
2. Input data operasional secara berkala
3. Menjalankan cost allocation dan unit costing
4. Menetapkan tariff yang tepat
5. Membuat dan mengelola clinical pathway
6. Mencatat dan menganalisis patient cases
7. Menghasilkan laporan dan analisis yang komprehensif

Dengan penggunaan yang konsisten dan tepat, sistem ini akan membantu rumah sakit dalam:
- Meningkatkan akurasi costing
- Menetapkan tariff yang kompetitif
- Meningkatkan compliance dengan clinical pathway
- Mengurangi variance biaya
- Meningkatkan efisiensi operasional

---

**Dokumen ini akan diupdate seiring dengan perkembangan fitur dan kebutuhan pengguna.**

