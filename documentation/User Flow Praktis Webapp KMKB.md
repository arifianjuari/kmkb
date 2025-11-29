# User Flow KMKB Webapp

_(Langkah demi langkah, siapa melakukan apa, pakai menu apa)_

---

## 0. PERSIAPAN AWAL (ADMIN & KONTEKS RUMAH SAKIT)

### 0.1. Setup Rumah Sakit

- **Menu:** `System Administration → Hospitals` (untuk Superadmin, di navbar dropdown)
- **Peran:** Superadmin / Admin Aplikasi
- **Langkah:**
  1. Tambah data rumah sakit (nama, kode, alamat, jenis RS, dll).
  2. Set **hospital aktif** yang akan dikerjakan.
- **Sumber Data:** Profil resmi RS, SK pendirian.
- **Pemilik Data:** Direksi / Bagian Hukum / Tata Usaha.

### 0.2. Setup User & Role

- **Menu:** `System Administration → Users` (di navbar dropdown, untuk Admin)
- **Peran:** Admin RS / Admin IT
- **Langkah:**
  1. Buat akun untuk:
     - Financial Manager / Staf Keuangan
     - Costing Analyst
     - Tim Mutu & Komite Medis
     - Unit Klaim / Case Manager
     - Auditor Internal / SPI
  2. Assign role & akses menu sesuai fungsi.
- **Sumber Data:** Struktur organisasi, daftar pegawai.
- **Pemilik Data:** Direksi / Bagian SDM.

**Catatan:** Menu System Administration (Users, Roles & Permissions, Audit Logs) berada di navbar dropdown, bukan di sidebar.

---

## 1. SETUP (KONFIGURASI AWAL & DATA MASTER)

### 1.1. Cost Centers (Unit Biaya RS)

- **Menu:** `Setup → Costing Setup → Cost Centers`
- **Peran:** Costing Analyst / Financial Manager (disetujui Direksi)
- **Langkah:**
  1. Daftar semua unit:
     - Support: Administrasi, Keuangan, SDM, Laundry, CSSD, IPSRS, IT, Gizi, dsb.
     - Intermediate: Lab, Radiologi, Farmasi, Kamar Bedah, dsb.
     - Revenue: IGD, Poliklinik, Rawat Inap per ruangan, OK, dsb.
  2. Tandai jenis: `support` / `revenue`.
- **Sumber Data:** Struktur organisasi, bagan unit kerja.
- **Pemilik Data:** Direksi / Keuangan / SDM.

### 1.2. Expense Categories / COA (Jenis Biaya)

- **Menu:** `Setup → Costing Setup → Expense Categories`
- **Peran:** Financial Manager / Staf Akuntansi
- **Langkah:**
  1. Import / input daftar akun biaya (COA).
  2. Untuk tiap akun, tentukan:
     - Klasifikasi biaya: `Fixed`, `Variable`, `Semi Variable`.
     - Kelompok: gaji, BHP medis, BHP non medis, depresiasi, dll.
- **Sumber Data:** Buku COA, trial balance.
- **Pemilik Data:** Bagian Keuangan / Akuntansi.

### 1.3. Allocation Drivers (Dasar Alokasi)

- **Menu:** `Setup → Costing Setup → Allocation Drivers`
- **Peran:** Costing Analyst (dengan input Sarpras & SDM)
- **Langkah:**
  1. Definisikan driver yang dipakai, misalnya:
     - Luas lantai (m²)
     - Jumlah karyawan (FTE)
     - Kg laundry
     - Jam layanan
     - Jumlah resep
  2. Isi: nama driver, satuan, deskripsi.
- **Sumber Data:** Kebijakan costing RS, data SDM & Sarpras.
- **Pemilik Data:** Tim Costing (Finance + Manajemen).

### 1.4. Tariff Classes (Kelas Pelayanan)

- **Menu:** `Setup → Costing Setup → Tariff Classes`
- **Peran:** Financial Manager
- **Langkah:**
  1. Tambah kelas: Kelas 3, 2, 1, VIP, VVIP, dsb.
  2. Simpan dan pastikan digunakan di modul tarif.
- **Sumber Data:** SK Tarif internal RS/BLUD.
- **Pemilik Data:** Direksi / Komite Tarif.

### 1.5. Service Catalog (Service Items / Cost References)

- **Menu:** `Setup → Service Catalog → Service Items`
- **Peran:** Costing Analyst + input Unit Layanan & SIMRS
- **Langkah:**
  1. Import daftar:
     - Tindakan medik
     - Pemeriksaan penunjang
     - Jasa pelayanan
     - Obat & BHP (jika diperlukan)
  2. Untuk tiap item:
     - Isi kode, nama, satuan.
     - Hubungkan ke **Cost Center**.
     - Isi harga pokok / harga beli jika tersedia.
- **Sumber Data:** Master tindakan SIMRS, master obat & BHP, daftar tarif.
- **Pemilik Data:**
  - Konten klinis: Komite Medis / Tim Mutu.
  - Harga: Keuangan, Logistik, Farmasi.

**Submenu Service Catalog:**

- **Service Items** (`Setup → Service Catalog → Service Items`): Daftar layanan/tindakan (sebelumnya Cost References)
- **SIMRS-linked Items** (`Setup → Service Catalog → SIMRS-linked Items`): Item yang terhubung dengan SIMRS
- **Import/Export** (`Setup → Service Catalog → Import/Export`): Pusat import/export data service catalog

### 1.6. JKN / INA-CBG Codes (Opsional tapi Penting)

- **Menu:** `Setup → JKN / INA-CBG Codes`
- **Peran:** Financial Manager / Costing Analyst
- **Langkah:**
  1. Input kode INA-CBG dan deskripsi.
  2. Isi base tarif sesuai regulasi terakhir.
- **Sumber Data:** Permenkes/PMK tentang INA-CBG.
- **Pemilik Data:** Unit Klaim / Manajemen Keuangan.

**Submenu JKN / INA-CBG Codes:**

- **CBG List** (`Setup → JKN / INA-CBG Codes → CBG List`): Daftar kode INA-CBG
- **Base Tariff Reference** (`Setup → JKN / INA-CBG Codes → Base Tariff Reference`): Referensi tarif dasar INA-CBG

### 1.7. SIMRS Integration (Jika Pakai Integrasi)

- **Menu:** `Setup → SIMRS Integration`
- **Peran:** Admin IT / SIMRS + Costing Analyst
- **Langkah:**
  1. Isi konfigurasi koneksi database SIMRS.
  2. Mapping tabel SIMRS (tindakan, obat, kunjungan, dsb).
  3. Uji coba sync (import data uji).
- **Sumber Data:** Dokumentasi SIMRS, vendor.
- **Pemilik Data:** Unit IT / SIMRS.

**Submenu SIMRS Integration:**

- **Connection Settings** (`Setup → SIMRS Integration → Connection Settings`): Konfigurasi koneksi database SIMRS
- **Data Sources** (`Setup → SIMRS Integration → Data Sources`): Akses ke data master SIMRS (Master Barang, Tindakan, Lab, Radiologi, dll)
- **Sync Management** (`Setup → SIMRS Integration → Sync Management`): Manajemen sinkronisasi data dari SIMRS

---

## 2. INPUT DATA PERIODIK (BULANAN / TRIWULAN)

### 2.1. Input GL Expenses (Biaya per Cost Center)

- **Menu:** `Data Input → GL Expenses`
- **Peran:** Financial Manager / Staf Akuntansi
- **Langkah:**
  1. Pilih periode (tahun & bulan).
  2. Import / input total biaya per:
     - Cost center
     - Expense category
  3. Simpan sebagai dataset biaya periode tersebut.
- **Sumber Data:** Buku besar, trial balance.
- **Pemilik Data:** Bagian Keuangan.

### 2.2. Input Driver Statistics (Nilai Driver per Cost Center)

- **Menu:** `Data Input → Driver Statistics`
- **Peran:** Costing Analyst
- **Langkah:**
  1. Pilih periode.
  2. Untuk tiap cost center, isi nilai driver yang relevan:
     - m² ruang
     - FTE pegawai
     - Kg laundry per bulan
     - Jam layanan, jumlah resep, jumlah operasi, dll.
- **Sumber Data & Pemilik:**
  - Luas lantai → Sarpras (data bangunan).
  - FTE → SDM/HRD (HRIS).
  - Kg laundry → Instalasi Laundry.
  - Volume operasional lain → SIMRS / register unit.

### 2.3. Input Service Volumes (Volume Layanan)

- **Menu:** `Data Input → Service Volumes`
- **Peran:** Costing Analyst / Unit SIMRS / Unit Klaim
- **Langkah:**
  1. Pilih periode.
  2. Import/isi jumlah tindakan per service item, bisa per kelas tarif.
- **Sumber Data:** Laporan produksi dari SIMRS, rekap manual unit.
- **Pemilik Data:**
  - Unit layanan masing-masing (IGD, OK, Lab, Rad, dsb).
  - Konsolidasi: SIMRS / Unit Informasi / Keuangan.

### 2.4. Import Center (Pusat Import Data)

- **Menu:** `Data Input → Import Center`
- **Peran:** Financial Manager / Costing Analyst
- **Langkah:**
  1. Akses pusat import untuk semua modul data input.
  2. Pilih jenis data yang akan diimport (GL Expenses, Driver Statistics, Service Volumes).
  3. Upload file dan validasi data.
  4. Review hasil import dan perbaiki error jika ada.
- **Sumber Data:** File Excel/CSV dari berbagai unit.
- **Pemilik Data:** Tim Costing / Keuangan.

---

## 3. COSTING PROCESS (ALLOCATION → UNIT COST)

### 3.1. Pre-Allocation Check

- **Menu:** `Costing Process → Pre-Allocation Check`
- **Peran:** Costing Analyst
- **Langkah:**
  1. Jalankan semua pengecekan:
     - **GL Completeness**: Cek kelengkapan data GL expenses per cost center dan expense category
     - **Driver Completeness**: Cek kelengkapan nilai driver statistics per cost center
     - **Service Volume Completeness**: Cek kelengkapan volume layanan per service item
     - **Mapping Validation**: Validasi mapping cost center, expense category, dan allocation driver
  2. Perbaiki input jika ada warning/error.
- **Sumber Data:** Modul internal, hasil input sebelumnya.
- **Pemilik:** Costing Analyst.

### 3.2. Setup Allocation Maps (Sekali, lalu Review Berkala)

- **Menu:** `Costing Process → Allocation Engine → Allocation Maps`
- **Peran:** Costing Analyst (dengan persetujuan Finance & Manajemen)
- **Langkah:**
  1. Tentukan **source cost center** (support) dan **target**.
  2. Pilih **driver** untuk tiap alokasi:
     - Housekeeping → driver luas lantai.
     - Administrasi → driver FTE.
  3. Atur urutan step alokasi (step-down).
- **Sumber Data:** Kebijakan costing RS.
- **Pemilik:** Tim Costing.

### 3.3. Run Allocation

- **Menu:** `Costing Process → Allocation Engine → Run Allocation`
- **Peran:** Costing Analyst / Financial Manager
- **Langkah:**
  1. Pilih tahun & bulan.
  2. Pilih konfigurasi Allocation Maps.
  3. Jalankan proses alokasi.
  4. Simpan sebagai versi (misal: `ALLOC_2025_01`).
- **Input:** GL expenses + driver statistics + allocation maps.
- **Output:** Biaya teralokasi per cost center (post allocation).

### 3.4. Review Allocation Results

- **Menu:** `Costing Process → Allocation Engine → Allocation Results`
- **Peran:** Costing Analyst + Financial Manager
- **Langkah:**
  1. Bandingkan total biaya sebelum vs sesudah alokasi.
  2. Lihat aliran biaya (source → target).
  3. Export bila perlu untuk diskusi internal.
- **Pemilik Output:** Tim Costing / Manajemen Keuangan.

### 3.5. Calculate Unit Cost

- **Menu:** `Costing Process → Unit Cost Engine → Calculate Unit Cost`
- **Peran:** Costing Analyst
- **Langkah:**
  1. Pilih periode dan versi alokasi (`ALLOC_...`).
  2. Isikan nama versi unit cost (misal: `UC_2025_JAN`).
  3. Jalankan perhitungan unit cost berdasarkan:
     - Direct material (BHP).
     - Direct labor (gaji).
     - Overhead (hasil alokasi).
  4. Simpan hasil perhitungan.
- **Input:** Allocation results + GL + Service Volumes + Service Catalog.
- **Output:** Unit cost per layanan per periode.

### 3.6. Review Unit Cost Results

- **Menu:** `Costing Process → Unit Cost Engine → Unit Cost Results`
- **Peran:** Costing Analyst, Financial Manager, Auditor
- **Langkah:**
  1. Lihat daftar unit cost per tindakan.
  2. Drill down breakdown biaya.
  3. Bandingkan antar versi (misal tahun ke tahun).
  4. Export ke Excel/PDF.
- **Pemilik Output:** Manajemen Keuangan / Direksi.

### 3.7. Compare Unit Cost Versions

- **Menu:** `Costing Process → Unit Cost Engine → Compare Versions`
- **Peran:** Costing Analyst, Financial Manager, Auditor
- **Langkah:**
  1. Pilih beberapa versi unit cost untuk dibandingkan.
  2. Lihat perbandingan side-by-side.
  3. Analisis variance antar versi.
  4. Export laporan perbandingan.
- **Output:** Insight perubahan unit cost dari waktu ke waktu.

---

## 4. TARIF (SIMULASI → FINAL)

### 4.1. Tariff Simulation

- **Menu:** `Tariffs → Tariff Simulation`
- **Peran:** Financial Manager / Costing Analyst
- **Langkah:**
  1. Pilih versi unit cost yang akan dipakai.
  2. Tentukan margin (global atau per layanan).
  3. Buat beberapa skenario (konservatif, moderat, agresif).
- **Input:** Unit cost + margin.
- **Output:** Tarif usulan (belum final) untuk diskusi.

### 4.2. Tariff Structure Setup

- **Menu:** `Tariffs → Tariff Structure Setup`
- **Peran:** Financial Manager / Admin
- **Langkah:**
  1. Setup struktur tarif (Jasa Sarana, Jasa Pelayanan, dll).
  2. Definisikan komponen tarif dan aturan perhitungannya.
  3. Link komponen ke unit cost.
  4. Buat template komponen tarif.
- **Sumber Data:** Kebijakan tarif RS, struktur tarif yang berlaku.
- **Pemilik Data:** Manajemen RS / Komite Tarif.

### 4.3. Final Tariffs

- **Menu:** `Tariffs → Final Tariffs`
- **Peran:**
  - Penyusun: Financial Manager
  - Penyetuju: Direksi / Dewan Pengawas / BLUD
- **Langkah:**
  1. Pilih layanan + kelas tarif.
  2. Tarik base unit cost dari versi tertentu.
  3. Set margin, pisahkan jasa sarana & jasa pelayanan (jika diperlukan).
  4. Isi metadata:
     - Nomor SK
     - Tanggal berlaku
  5. Set status tarif: Draft → Review → Approved.
- **Sumber Data:** Unit cost, SK manajemen.
- **Pemilik Data:** Manajemen RS / BLUD.

### 4.4. Tariff Explorer

- **Menu:** `Tariffs → Tariff Explorer`
- **Peran:** Unit Klaim, Costing Analyst, Manajemen, Auditor
- **Langkah:**
  1. Cari tarif resmi RS per layanan & kelas.
  2. Lihat detail tarif dan breakdown komponennya.
  3. Bandingkan tarif antar kelas pelayanan.
- **Input:** Final tariffs.
- **Output:** Informasi tarif lengkap untuk referensi operasional.

### 4.5. Tariff vs INA-CBG Comparison

- **Menu:** `Tariffs → Tariff vs INA-CBG`
- **Peran:** Unit Klaim, Costing Analyst, Manajemen, Auditor
- **Langkah:**
  1. Pilih layanan dan kelas tarif.
  2. Bandingkan tarif RS vs tarif INA-CBG.
  3. Identifikasi selisih dan potensi risiko keuangan.
  4. Analisis margin untuk layanan JKN.
- **Input:** Final tariffs + data INA-CBG.
- **Output:** Informasi selisih tarif dan potensi risiko keuangan.

---

## 5. CLINICAL PATHWAY (DESAIN & BIAYA)

### 5.1. Membuat Clinical Pathway

- **Menu:** `Clinical Pathways → Pathway Repository → Add New`
- **Peran:** Pathway Designer / Tim Mutu + Dokter Spesialis
- **Langkah:**
  1. Tentukan nama pathway, diagnosis (ICD-10), dan INA-CBG terkait.
  2. Isi expected Length of Stay (LOS).
  3. Simpan sebagai Draft.
- **Sumber Data:** Clinical Practice Guidelines, regulasi Kemenkes, CPG perhimpunan profesi.
- **Pemilik Data:** Komite Medis / Tim Mutu.

### 5.2. Menyusun Step Pathway & Link ke Layanan

- **Menu:** `Clinical Pathways → Pathway Builder`
- **Peran:** Pathway Designer + input klinis dokter.
- **Langkah:**
  1. Tambahkan step per hari / fase: asesmen, lab, radiologi, obat, tindakan, konsul.
  2. Untuk tiap step, pilih **Service Item** dari Service Catalog.
  3. Isi quantity & tandai mandatory/optional.
- **Input:** Service Catalog, unit cost terbaru.
- **Output:** Pathway terstruktur dan dapat dihitung biayanya.

### 5.3. Hitung Ringkasan Biaya Pathway

- **Menu:** Tab `Summary` di detail pathway → tombol `Recalculate`
- **Peran:** Pathway Designer
- **Langkah:**
  1. Pilih versi unit cost yang akan dipakai.
  2. Jalankan kalkulasi total biaya pathway.
  3. Bandingkan dengan tarif INA-CBG untuk kasus tersebut.
- **Output:** Estimasi biaya ideal per episode per pathway.

### 5.4. Approval Pathway

- **Menu:** `Clinical Pathways → Pathway Approval` (di detail pathway)
- **Peran:** Komite Medis / Komite Mutu
- **Langkah:**
  1. Review isi langkah & biaya pathway.
  2. Beri komentar dan revisi jika perlu.
  3. Set status: Approved (siap dipakai kasus) atau Reject.
- **Pemilik:** Komite Medis / Tim Mutu.

### 5.5. Template Import/Export Pathway

- **Menu:** `Clinical Pathways → Template Import/Export`
- **Peran:** Pathway Designer / Tim Mutu
- **Langkah:**
  1. Download template pathway (blank atau dari pathway existing).
  2. Isi template dengan data pathway.
  3. Import template untuk membuat pathway baru atau update pathway existing.
  4. Export pathway sebagai template untuk digunakan kembali.
- **Sumber Data:** Pathway existing atau template kosong.
- **Pemilik Data:** Tim Mutu / Komite Medis.

---

## 6. PATIENT CASE (PLANNED vs ACTUAL)

### 6.1. Registrasi Kasus Pasien

- **Menu:** `Patient Cases → Case Registration`
- **Peran:** Case Manager / Unit Klaim / Rekam Medis
- **Langkah:**
  1. Input atau import data kasus:
     - MRN (rekam medis)
     - Nama pasien (opsional/pseudonim)
     - Tanggal masuk & keluar
     - Pathway yang dipakai
     - Diagnosis & INA-CBG
     - Skema pembayaran (JKN, umum, asuransi lain)
  2. Pilih versi unit cost yang akan dipakai untuk analisis.
- **Sumber Data:** SIMRS (ADM/discharge), berkas rekam medis.
- **Pemilik Data:** Instalasi Rekam Medis / Unit Klaim.

### 6.2. Copy Step dari Pathway & Isi Layanan Aktual

- **Menu:** `Patient Cases → Case Details`
- **Peran:** Case Manager / Coder / Unit Klaim
- **Langkah:**
  1. Klik `Copy Steps from Pathway` → planned steps terisi otomatis.
  2. Tandai:
     - Step yang terlaksana.
     - Step yang tidak dilakukan.
  3. Tambahkan layanan tambahan yang tidak ada dalam pathway (variasi actual).
  4. Isi quantity dan tanggal pelaksanaan.
- **Sumber Data:** SIMRS (log tindakan, obat), ringkasan pulang.
- **Pemilik Data:** Unit Klaim / Rekam Medis.

### 6.3. Case Costing (Perhitungan Biaya Kasus)

- **Menu:** `Patient Cases → Case Costing`
- **Peran:** Case Manager, Costing Analyst, Tim Mutu
- **Langkah:**
  1. Lihat breakdown biaya kasus:
     - Planned cost (berdasarkan pathway).
     - Actual cost (berdasarkan layanan aktual).
  2. Analisis komponen biaya per kategori.
  3. Bandingkan dengan tarif INA-CBG.
- **Output:** Profil biaya lengkap satu kasus.

### 6.4. Case Variance Analysis (Analisis Varians)

- **Menu:** `Patient Cases → Case Variance Analysis`
- **Peran:** Case Manager, Costing Analyst, Tim Mutu
- **Langkah:**
  1. Lihat analisis variance:
     - Compliance pathway (%).
     - Variance biaya (planned vs actual).
     - Identifikasi penyebab variance.
  2. Review kasus dengan variance tinggi.
  3. Export laporan variance untuk review manajemen.
- **Output:** Insight penyebab variance dan rekomendasi perbaikan.

---

## 7. ANALYTICS & IMPROVEMENT

### 7.1. Cost Center Performance

- **Menu:** `Analytics & Improvement → Cost Center Performance`
- **Peran:** Financial Manager, Costing Analyst, Auditor
- **Langkah:**
  1. Review biaya per cost center sebelum & sesudah alokasi.
  2. Analisis efisiensi operasional per unit.
  3. Identifikasi cost center dengan biaya tinggi.
- **Output:** Insight efisiensi unit & rekomendasi optimasi.

### 7.2. Allocation Summary

- **Menu:** `Analytics & Improvement → Allocation Summary`
- **Peran:** Financial Manager, Costing Analyst
- **Langkah:**
  1. Review hasil alokasi biaya support ke revenue center.
  2. Analisis aliran biaya dan proporsi alokasi.
  3. Validasi akurasi alokasi.
- **Output:** Ringkasan alokasi biaya untuk review manajemen.

### 7.3. Unit Cost Summary

- **Menu:** `Analytics & Improvement → Unit Cost Summary`
- **Peran:** Financial Manager, Costing Analyst, Auditor
- **Langkah:**
  1. Pantau tren unit cost per layanan/departemen.
  2. Bandingkan unit cost antar periode.
  3. Identifikasi layanan dengan unit cost tinggi.
- **Output:** Insight kecukupan tarif dan efisiensi layanan.

### 7.4. Tariff Analytics

- **Menu:** `Analytics & Improvement → Tariff Analytics`
- **Peran:** Direksi, Financial Manager, Unit Klaim
- **Langkah:**
  1. Analisis margin per layanan & kelas.
  2. Review profitabilitas layanan.
  3. Identifikasi layanan yang berisiko rugi.
- **Output:** Bahan keputusan revisi tarif & negosiasi kontrak.

### 7.5. Pathway Compliance

- **Menu:** `Analytics & Improvement → Pathway Compliance`
- **Peran:** Tim Mutu, Komite Medis, Direksi
- **Langkah:**
  1. Pilih periode & pathway.
  2. Lihat rata-rata compliance pathway.
  3. Identifikasi pathway yang perlu diperbaiki.
- **Output:** Dasar program peningkatan mutu dan efisiensi.

### 7.6. Case Variance

- **Menu:** `Analytics & Improvement → Case Variance`
- **Peran:** Tim Mutu, Komite Medis, Direksi
- **Langkah:**
  1. Analisis distribusi variance biaya kasus.
  2. Identifikasi pola variance.
  3. Review kasus dengan variance tinggi.
- **Output:** Insight penyebab variance dan rekomendasi perbaikan.

### 7.7. LOS Analysis (Length of Stay Analysis)

- **Menu:** `Analytics & Improvement → LOS Analysis`
- **Peran:** Tim Mutu, Komite Medis, Direksi
- **Langkah:**
  1. Analisis rata-rata LOS per pathway.
  2. Bandingkan LOS actual vs expected.
  3. Identifikasi pathway dengan LOS panjang.
- **Output:** Insight efisiensi penggunaan tempat tidur dan rekomendasi perbaikan.

### 7.8. Continuous Improvement

- **Menu:** `Analytics & Improvement → Continuous Improvement`
- **Peran:** Tim Mutu, Komite Medis, Direksi, Financial Manager
- **Langkah:**
  1. Review rekomendasi perbaikan dari berbagai analisis.
  2. Prioritaskan action items.
  3. Track progress implementasi perbaikan.
- **Output:** Dashboard perbaikan berkelanjutan dan action plan.

---

## 8. RINGKASAN SIAPA MENGISI APA

_(disesuaikan dengan SK Tim Penyusunan Unit Cost RS Bhayangkara Hasta Brata Batu)_

### 8.1. Pengarah & Koordinator

- **dr. ANANINGATI, Sp.OG(K) – Karumkit**

  - **Peran:** Penanggung jawab keseluruhan program unit cost & KMKB.
  - **Utama mengesahkan:**
    - Kebijakan penggunaan webapp KMKB.
    - Hasil akhir unit cost, tarif, dan clinical pathway yang diusulkan tim.

- **drg. AKHMADI PRABOWO, MMRS – Wakarumkit**

  - **Peran:** Koordinator tim penyusunan unit cost.
  - **Mengawal:**
    - Kelancaran pengumpulan data dari semua unit.
    - Koordinasi lintas bidang (medis, keuangan, SDM, sarpras, IT).

- **DODIK BINTORO, S.Psi – Kaur Wasbin**
  - **Peran:** Pengawas proses.
  - **Fokus:** Kepatuhan terhadap prosedur, dokumentasi, dan kelengkapan bukti perhitungan (audit trail).

---

### 8.2. Tim Data Keuangan & Costing

Anggota yang bertanggung jawab mengisi/memasok data keuangan ke modul:

- `Setup → Costing Setup → Expense Categories`
- `Data Input → GL Expenses`
- Bagian keuangan di `Analytics & Improvement` dan `Tariff Management`.

**Anggota:**

- **AGUS PURWANTO – PS. Kaurkeu**

  - Tugas SK: _Penyaji data laporan tahunan keuangan_.
  - Di webapp: menyiapkan **GL Expenses**, COA, dan biaya per cost center per periode.

- **NISA YULIANTI TENDEAN, S.Si – Staf Keuangan**
- **INTAN DEWI SA’ADAH, S.Ak – Staf Keuangan**
- **FENNI AGISTA PUSPITA, S.Ak – Staf Keuangan**
  - Tugas SK: _Penyajian data laporan unit keuangan_.
  - Di webapp: membantu pengolahan & upload:
    - Rincian biaya per akun dan cost center.
    - Rekap biaya per periode untuk kebutuhan alokasi & unit cost.

---

### 8.3. Tim Data Aset, Gedung & Pemeliharaan

Menyediakan data untuk:

- `Setup → Costing Setup → Cost Centers` (mapping aset ke unit).
- `Data Input → GL Expenses` (biaya aset & pemeliharaan).
- `Data Input → Driver Statistics` (luas lantai, dll).

**Anggota:**

- **GANDI ARI SETIOKO, A.Md.Kep – PS. Kaur Ren**

  - Tugas SK: _Penyajian data aset_.
  - Di webapp: master aset, nilai buku, depresiasi, serta pembagian aset ke cost center.

- **SIGIET SUBIYANTORO, AMTE.S.M. – Banum**

  - Tugas SK: _Penyajian data pemeliharaan alkes_.
  - Di webapp: biaya pemeliharaan alat kesehatan sebagai bagian dari overhead.

- **EKA FAJAR ANGGRAINI, ST – Kaur Jangum**

  - Tugas SK: _Penyajian data gedung dan bangunan_.
  - Di webapp:
    - Data luas lantai per unit (driver alokasi).
    - Nilai aset gedung untuk depresiasi.

- **WIWIT AGUS HARIADI – Banum**
  - Tugas SK: _Penyajian data laporan unit Ranmor_.
  - Di webapp: biaya dan statistik kendaraan (driver jika dipakai, misalnya km/jam operasional).

---

### 8.4. Tim Data SDM (Kepegawaian)

Menyediakan data untuk:

- `Data Input → Driver Statistics` (jumlah pegawai/FTE per cost center).
- Mendukung analisis **Direct Labor Cost**.

**Anggota:**

- **SRI WAHYUNI, A.Md.Keb – Kaurmin**
  - Tugas SK: _Penyajian data kepegawaian_.
  - Di webapp:
    - Jumlah pegawai per unit (FTE).
    - Klasifikasi tenaga (dokter, perawat, non-medis) untuk perhitungan gaji per unit.

---

### 8.5. Tim Data Rekam Medis & SIMRS

Menyediakan data untuk:

- `Data Input → Service Volumes` (volume tindakan per unit).
- `Patient Cases` (data kasus, INA-CBG, dll).
- Integrasi `Setup → SIMRS Integration`.

**Anggota:**

- **RINI ERNI SISWATI, A.Md.RMIK – Karu Rekam Medik**

  - Tugas SK: _Penyajian data laporan unit pendaftaran_.
  - Di webapp:
    - Data kunjungan, episode perawatan, dan ringkasan pasien.
    - Penghubung antara rekam medis dan data kasus di `Patient Cases`.

- **EKO ARI IRAWAN, S.Kom – Karu IT**
  - Tugas SK: _Pencocokan data dengan SIM RS_.
  - Di webapp:
    - Set up & maintain `SIMRS Integration`.
    - Membantu ekstraksi data tindakan, obat, LOS, dll dari SIMRS ke webapp.

---

### 8.6. Tim Data Unit Penunjang & Support

Menyuplai data untuk:

- `Data Input → Service Volumes` (volume layanan penunjang).
- Driver & biaya overhead di `Costing Process`.

**Anggota:**

- **SUKARI – Karu Laundry**

  - Tugas SK: _Penyajian data laporan unit Laundry_.
  - Di webapp: volume (kg laundry), biaya dan driver alokasi laundry.

- **RINDA SEPTIA ELTIANA, A.Md.Kes – Karu Laboratorium**

  - Data volume & jenis pemeriksaan lab.

- **YULIASIH SATYORINI, A.Md.Rad – Karu Radiologi**

  - Data volume & jenis pemeriksaan radiologi.

- **RISKA INDRAWATI, S.Gz – Karu Gizi**

  - Data produksi gizi (jumlah porsi/diet) & biaya terkait.

- **DINAR BUGI MAWARNI, S.Farm.Apt – Karu Farmasi**
  - Data obat/BHP yang relevan sebagai komponen biaya (jika dimasukkan sampai level item).

---

### 8.7. Tim Data Unit Pelayanan Klinis (Revenue Center)

Mengisi volume layanan klinis di:

- `Data Input → Service Volumes`
- `Patient Cases → Case Details` (actual tindakan per kasus)
- Membantu penyusunan Clinical Pathway.

**Anggota & Unit:**

- **dr. YEREMIA DWI PURNOMO – Dokter Umum**

  - Tugas SK: _Penyajian data laporan unit Cesmik_ (medical check-up).

- **MUKHAMAD HASAN, S.Kep.Ners – Karu Kamar Operasi**

  - Data operasi & tindakan bedah.

- **FERIKA IRMA NURWANDI, S.Kep.Ners – Karu Poliklinik**

  - Data kunjungan & tindakan poliklinik.

- **N.S. AKHMAD KHOLIL, S.Kep – Karu ICU**

  - Data layanan & LOS ICU.

- **RIZKI SILFIANA, S.Kep.Ners – Karu R. Inap Kemuning**
- **HERI KUSNADI, A.Md.Kep – Karu R. Inap Ken Arok**
- **WULAN VITASARI, A.Md.Kep – Karu Gayatri**
- **FARIDA HANUNG, S.Tr.Keb – Karu Kendedes**

  - Mewakili berbagai ruang rawat inap; menyajikan:
    - Jumlah hari rawat, jumlah pasien, jenis tindakan keperawatan, dsb.

- **ROSA ROSITA, A.Md.Kep – Karu IGD**

  - Data kunjungan & tindakan IGD.

- **RELA YOULANDA P., A.Md.Kep – Karu Hiperbarik**
  - Data layanan kamar hiperbarik.

Semua kepala unit klinis ini:

- Memberikan data volume & beban kerja unit masing-masing.
- Menjadi rujukan utama saat analisis hasil unit cost & Clinical Pathway yang terkait unitnya.

---

### 8.8. Ringkasan Peran per Kelompok Menu

- **Pengarah & Koordinator (Karumkit, Wakarumkit, Kaur Wasbin)**

  - Menyetujui kebijakan, hasil unit cost, tarif, dan pathway.

- **Keuangan (Kaur Keuangan + Staf Keuangan)**

  - Fokus di `Setup → Costing Setup → Expense Categories`, `Data Input → GL Expenses`, dan `Tariff Management`.

- **Aset & Sarpras (Kaur Ren, Banum, Kaur Jangum, Banum Ranmor)**

  - Menyajikan data aset, gedung, pemeliharaan ke `GL Expenses` dan `Driver Statistics`.

- **SDM (Kaurmin)**

  - Menyajikan data pegawai/FTE ke `Driver Statistics`.

- **Rekam Medis & IT (Karu Rekam Medik, Karu IT)**

  - Menjamin data kunjungan, kasus, dan integrasi `Setup → SIMRS Integration` & `Patient Cases`.

- **Unit Penunjang & Support (Laundry, Lab, Radiologi, Gizi, Farmasi)**

  - Mengisi `Service Volumes` & data driver layanan penunjang.

- **Unit Pelayanan Klinis (OK, Poliklinik, ICU, IRNA, IGD, Cesmik, Hiperbarik)**
  - Mengisi `Service Volumes`, mendukung pengisian `Patient Cases` dan validasi Clinical Pathway unit masing-masing.

---
