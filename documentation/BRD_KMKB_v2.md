# **Dokumen Kebutuhan Bisnis (BRD)**

### **Proyek: Pengembangan Aplikasi Web Kendali Mutu Kendali Biaya (KMKB) Berbasis Clinical Pathway**

---

## 1. Tujuan Proyek dan Ruang Lingkup

### **1.1. Tujuan Proyek**

Proyek ini bertujuan membangun aplikasi web **Kendali Mutu Kendali Biaya (KMKB)** yang menyatukan modul costing, manajemen tarif, dan _clinical pathway_ dalam satu platform operasional rumah sakit. Tujuan bisnisnya:

- Meningkatkan akurasi penetapan biaya dan tarif melalui perhitungan unit cost berbasis data GL serta _service volume_.
- Menstandardisasi jalur klinis, memantau kepatuhan, dan mengendalikan variansi biaya terhadap pathway maupun tarif INA-CBG.
- Menyajikan _insight_ real-time bagi manajemen untuk pengambilan keputusan mutu-biaya.

### **1.2. Ruang Lingkup**

#### **Dalam Cakupan (In-Scope)**

- **Pilot Multi-tenant**: Implementasi awal untuk satu rumah sakit namun siap ekspansi multi RS melalui seleksi `hospital_id`.
- **Master Data Costing & Tariff**: Pengelolaan Cost Center, Expense Category, Allocation Driver, Tariff Class, Cost Reference, dan kode INA-CBG.
- **GL & Operational Data Intake**: Input manual maupun impor CSV/XLSX untuk GL Expenses, Driver Statistics, Service Volumes, serta konversi data SIMRS.
- **Cost Allocation & Unit Cost Engine**: Step-down allocation, perhitungan unit cost versi ganda, audit trail per cost center.
- **Tariff Management Suite**: Simulasi markup, penetapan final tariff bersertifikat SK, dan Tariff Explorer untuk perbandingan INA-CBG.
- **Clinical Pathway Builder & Repository**: Versi, duplikasi, impor langkah, ekspor PDF/DOCX, dan kalkulasi estimasi biaya pathway.
- **Patient Case Management**: Upload massal kasus, sinkronisasi langkah pathway → case detail, kalkulasi kepatuhan dan variansi biaya.
- **Dashboard & Reports**: KPI costing, compliance, cost variance, pathway performance, serta ekspor PDF/Excel.
- **SIMRS Data Hub**: Viewer data master SIMRS dan modul sinkronisasi batch (obat, tindakan, kamar, dsb).
- **System Administration & Audit**: Role-based access, manajemen user/hospital, audit log, dan pengaturan penyimpanan.

#### **Di Luar Cakupan (Out-of-Scope) Tahap Awal**

- Integrasi _real-time_ dua arah dengan HIS/SIMRS atau BPJS (current scope masih batch/pull).
- Modul klinis lanjutan seperti CPOE, e-prescribing, rekam medis elektronik penuh, atau manajemen tindakan harian.
- Notifikasi eksternal (SMS/WhatsApp) dan approval workflow lintas platform.
- Analitik prediktif berbasis ML, benchmarking antar RS, serta automasi costing lintas multi +100 fasilitas.
- Integrasi perangkat medis atau pembacaan data IoT.

---

## 2. Sasaran Pengguna dan Alur Kerja

### **2.1. Peran Pengguna (User Roles)**

| Peran                           | Kebutuhan Bisnis                                                                    | Modul Utama                                                        |
| ------------------------------- | ----------------------------------------------------------------------------------- | ------------------------------------------------------------------ |
| **Superadmin**                  | Membuat tenant rumah sakit, mengatur lisensi, melakukan _troubleshooting_ lintas RS | Hospitals, Users, Audit Logs                                       |
| **Admin RS / Administrator IT** | Mengatur akses pengguna, master data dasar, konfigurasi SIMRS dan storage           | Master Data, Users, SIMRS, Settings                                |
| **Financial Manager**           | Menyetujui GL, menjalankan allocation & unit cost, menyetujui tarif                 | GL & Expenses, Allocation, Unit Cost, Tariff Final                 |
| **Costing Analyst**             | Input data costing, menjalankan simulasi, analisis hasil                            | Master Data, GL, Allocation, Unit Cost, Tariff Simulation, Reports |
| **Pathway Designer / Tim Mutu** | Membangun, menggandakan, dan memperbarui pathway                                    | Clinical Pathways, Cost References, Reports                        |
| **Medical Committee**           | Review dan approval pathway, evaluasi varian klinis                                 | Clinical Pathways (approval), Reports                              |
| **Case Manager / Unit Klaim**   | Input kasus pasien, unggah Excel, pantau kepatuhan dan variansi                     | Patient Cases, Reports, Tariff Explorer                            |
| **Auditor & Manajemen**         | Membaca KPI, audit trail, dan laporan untuk pengambilan keputusan                   | Dashboard, Reports, Audit Logs                                     |

Catatan: peran lama seperti Tim Mutu dan Unit Klaim dipetakan ke Pathway Designer dan Case Manager agar selaras dengan implementasi modul akses role-based di aplikasi.

### **2.2. Alur Kerja End-to-End**

1. **Persiapan Hospital & User** – Superadmin memilih hospital aktif, Admin menyiapkan user role, tarif dasar, dan preferensi sistem.
2. **Master Data Costing** – Cost Center, Expense Category, Allocation Driver, Tariff Class, Cost Reference, serta JKN-CBG diinput atau diimpor dari template.
3. **Pengambilan Data Operasional** – GL Expenses, Driver Statistics, dan Service Volume diimpor per periode (tersedia form import + validasi). Data referensi dapat disejajarkan dengan sumber SIMRS.
4. **Cost Allocation** – Allocation map dengan step sequence disiapkan; modul run allocation mengeksekusi step-down engine dan menghasilkan Allocation Result versi tertentu.
5. **Unit Cost Calculation** – Data hasil allocation + service volume dihitung menjadi unit cost version. Audit trail breakdown direct/indirect disimpan.
6. **Tariff Simulation & Finalization** – Unit cost dipakai sebagai basis markup; modul final tariff mengikat hasil, menambahkan metadata SK, masa berlaku, dan integrasi dengan Tariff Explorer.
7. **Clinical Pathway Engineering** – Tim Mutu membuat/menyalin pathway, melampirkan langkah-langkah, mengaitkan cost reference dan estimasi biaya, lalu mengekspor atau mengirim ke komite.
8. **Patient Case Recording** – Unit Klaim menginput/upload kasus, menyalin langkah pathway ke detail kasus, dan mencatat layanan aktual berserta biaya.
9. **Compliance & Variance Analysis** – Engine menghitung persentase kepatuhan, gap layanan, dan selisih biaya vs pathway maupun INA-CBG, ditampilkan di halaman kasus dan laporan analitik.
10. **Dashboard & Reporting** – Semua peran membaca KPI, menjalankan ekspor PDF/Excel, dan menindaklanjuti temuan (misal update pathway atau retarif).

---

## 3. Fitur Fungsional

### **3.1. Gambaran Modul Inti**

- **Master Data Costing & Tariff**: CRUD lengkap + impor/ekspor untuk seluruh referensi biaya.
- **Financial Data Intake**: GL, driver statistic, service volume dengan template, validasi, dan histori impor.
- **Costing Engine**: Allocation + unit cost per versi periode.
- **Tariff Management**: Simulasi, finalisasi, eksplorasi, serta perbandingan INA-CBG.
- **Clinical Pathway**: Builder, versi, duplikasi, ekspor dokumen, dan kalkulasi estimasi biaya.
- **Patient Case & Compliance**: Input manual/upload, auto-copy langkah, KPI kepatuhan, dan variansi biaya.
- **Analytics & Dashboard**: Widget KPI, laporan compliance, cost variance, pathway performance, dan dashboard laporan.
- **SIMRS Data Hub**: Viewer dataset SIMRS (tindakan, lab, kamar, dsb) + modul sinkronisasi obat/layanan.
- **System Administration & Audit**: Hospitals, users, roles, audit log, migrasi storage, dan API readiness.

### **3.2. Detail Modul**

#### **3.2.1 Dashboard & Insight**

- Dashboard utama menampilkan ringkasan cost, compliance, tarif, dan aktivitas terakhir.
- Superadmin memiliki dashboard tersendiri untuk memantau tenant.
- Tersedia shortcut ke laporan dan modul prioritas berbasis peran.

#### **3.2.2 Master Data Costing & Klinik**

- **Cost Centers**: Hierarki support vs revenue center, ekspor, dan visual parent-child.
- **Expense Categories**: Penandaan jenis biaya (fixed/variable) dan kelompok alokasi.
- **Allocation Drivers**: Penyimpanan satuan driver, import/export, dan validasi nilai.
- **Tariff Classes**: Pengelompokan kelas layanan (VIP, Kelas I-III, dsb) untuk unit cost & tarif.
- **Cost References**: Pemetaan item biaya ke cost center, kategori, dan referensi SIMRS; mendukung bulk import, pencarian cepat, dan bulk delete.
- **JKN CBG Codes**: Pencarian tarif dasar INA-CBG, metadata diagnosa, dan endpoint pencarian untuk modul lain.

#### **3.2.3 GL & Expense Management**

- CRUD GL Expenses per periode dengan filter cost center/kategori.
- Form import CSV/XLSX, validasi duplikasi, serta laporan kategori yang belum terisi.
- Modul Driver Statistics dan Service Volumes mengikuti pola serupa (template, import/export, filter).

#### **3.2.4 Cost Allocation Engine**

- Pengelolaan allocation map dengan step sequence, driver yang dipakai, dan preview flow.
- Form **Run Allocation** menampilkan konfigurasi sebelum diproses, memberikan ringkasan hasil, jumlah step, dan peringatan selisih.
- Allocation Result dapat dilihat, difilter, diekspor, dan dibanding antar versi/periode.

#### **3.2.5 Unit Costing**

- Service volume diverifikasi sebelum kalkulasi.
- Modul kalkulasi memproduksi dataset unit cost ber-versi, menyimpan status proses, dan merekam ringkasan direct/indirect cost.
- Halaman hasil menampilkan breakdown per layanan, audit trail cost center, serta ekspor ke Excel/PDF.

#### **3.2.6 Tariff Management Suite**

- **Tariff Simulation**: Memilih versi unit cost, menerapkan margin global/per layanan, membuat beberapa skenario, dan mengekspor hasil.
- **Final Tariffs**: Menyimpan tarif resmi per layanan+kelas, metadata SK, masa berlaku, serta relasi ke hasil unit cost.
- **Tariff Explorer**: Antarmuka pencarian tarif, histori perubahan, dan komparasi internal vs INA-CBG.

#### **3.2.7 Clinical Pathway Management**

- CRUD pathway dengan status (Draft, Review, Approved, Archived), versi, dan fitur duplikasi.
- Pathway Builder mendukung reorder drag-and-drop, impor template langkah, dan pengaturan mandatory/optional.
- Tersedia ekspor PDF dan DOCX, serta tombol recalculation untuk merangkum estimasi biaya.

#### **3.2.8 Patient Case Management**

- Daftar kasus dengan filter pathway, periode, diagnosa.
- Form upload Excel untuk banyak kasus sekaligus.
- Auto-copy semua langkah pathway ke detail kasus; masing-masing langkah dapat ditandai dilakukan/tidak, menambahkan layanan tambahan di luar pathway, dan memberi anotasi.
- Sistem menghitung kepatuhan (%) dan variansi biaya terhadap estimasi pathway, unit cost, dan INA-CBG.

#### **3.2.9 Analytics & Reporting**

- Halaman Reports menyediakan:
  - Compliance Report (per pathway dan tren).
  - Cost Variance Report.
  - Pathway Performance (LOS, cost efficiency).
  - Dashboard laporan dengan komponen visual tambahan.
- Proses ekspor asynchronous (generate + download) untuk PDF/Excel.

#### **3.2.10 SIMRS Integration & Service Volume Current**

- Viewer data SIMRS: master barang, tindakan rawat jalan/inap, lab, radiologi, operasi, kamar.
- Modul sync obat/layanan dengan log status dan histori error.
- Halaman service-volume-current memantau data kapasitas aktual per layanan serta menyediakan ekspor per kategori.

#### **3.2.11 System Administration & Audit**

- Manajemen hospital (Superadmin), pemilihan RS aktif, migrasi storage, serta modul khusus audit log (filter user/aksi/model).
- Pengaturan password user, reset, dan assignment role.
- Audit log dapat dibersihkan sesuai kebijakan retensi.

#### **3.2.12 Integrasi & Otomasi**

- Endpoint pencarian Cost Reference, JKN-CBG, dan Tariff Explorer dapat dimanfaatkan modul eksternal.
- SIMRS sync menyiapkan pondasi API-ready untuk transfer data otomatis.
- Struktur kode service-layer (AllocationService, UnitCostService, TariffService) memudahkan pembuatan job background bila volume data meningkat.

### **3.3. Fitur Pendukung**

- **Knowledge Reference**: Modul referensi internal (guideline, regulasi) untuk membantu tim costing & mutu.
- **Migrate Storage Wizard**: Utilitas admin untuk memindahkan file evidence/pathway ke storage baru.
- **Template Management**: Download template Excel untuk semua modul import agar input standar.
- **Audit & Logging**: Semua transaksi penting tercatat di `audit_logs` dan dapat diaudit oleh peran khusus.

---

## 4. Struktur Data dan Basis Data

### **4.1. Model Entitas Relasional (ERD)**

Entitas utama aplikasi:

- **Hospital** – Multi-tenant key; seluruh tabel operasional memiliki `hospital_id`.
- **User, Role, Permission** – Manajemen akses berbasis peran; relasi ke hospital aktif.
- **CostCenter**, **ExpenseCategory**, **AllocationDriver**, **TariffClass** – Master costing.
- **GlExpense**, **DriverStatistic**, **ServiceVolume** – Data operasional per periode.
- **AllocationMap**, **AllocationResult** – Engine step-down.
- **UnitCostCalculation**, **UnitCostResult** – Versi hasil unit cost dan detail breakdown.
- **CostReference**, **Reference** – Metadata layanan & knowledge base.
- **FinalTariff**, **TariffSimulationResult** – Data tarif resmi dan hasil simulasi.
- **ClinicalPathway**, **PathwayStep**, **PathwayTariffSummary** – Repository pathway dan ringkasan biaya.
- **PatientCase**, **CaseDetail**, **CaseAnnotation** – Data episode pasien dan kegiatan per langkah.
- **JknCbgCode**, **Simrs\* tables** – Referensi INA-CBG dan data impor SIMRS.
- **AuditLog** – Jejak aktivitas.

### **4.2. Catatan Implementasi Terkini (November 2025)**

- Seluruh modul Master Data (Cost Center, Expense Category, Allocation Driver, Tariff Class, Cost Reference, JKN CBG) telah tersedia dengan fitur impor, ekspor, dan pencarian (`routes/web.php`).
- GL Expenses, Driver Statistics, Service Volumes menyediakan form import, validasi, serta ekspor.
- Allocation engine telah diimplementasikan penuh pada `AllocationService`, termasuk validasi prerequisite, step sequence, summary, dan logging proses.
- Unit cost calculation menggunakan `UnitCostCalculationService` untuk menggabungkan hasil allocation dan service volume, menyediakan versi berbeda.
- Modul Tariff Simulation, Final Tariff, dan Tariff Explorer hadir di UI (folder `resources/views/tariff-*`) dan mendukung ekspor.
- Clinical Pathway builder mendukung duplikasi, versi baru, impor template langkah, serta ekspor PDF/DOCX.
- Patient Case module mendukung upload Excel, copy langkah pathway otomatis, dan anotasi kasus.
- Reports (compliance, cost variance, pathway performance) serta dashboard laporan telah aktif dengan kemampuan ekspor.
- SIMRS integration menampilkan data raw (master barang, tindakan, kamar, dll) dan modul sync obat/layanan.
- Audit log, migrasi storage, dan hospital selector aktif untuk kebutuhan admin & superadmin.

### **4.3. Integrasi Data Eksternal**

- Koneksi SIMRS via konfigurasi database terpisah (diatur oleh Admin) dengan endpoint viewer & sync.
- Template import mengikuti struktur standar agar bisa dihasilkan dari sistem HIS atau spreadsheet internal.
- Tariff Explorer dapat dibandingkan dengan tarif resmi INA-CBG dari tabel JKN CBG.

---

## 5. Standar Acuan

- **ISO 7101:2023** – Standar sistem manajemen mutu pelayanan kesehatan.
- **Standar Akreditasi JCI** – Menjamin keselamatan pasien dan governance mutu.
- **INA-CBG & Regulasi BPJS** – Acuan perbandingan tarif dan metodologi costing.
- **Regulasi Kemenkes (KMK Clinical Pathway)** – Pedoman penyusunan pathway.
- **OWASP & ISO 27001** – Kerangka keamanan informasi (auth, CSRF, encryption, audit).

---

## 6. Komponen Teknis (Kebutuhan Non-Fungsional)

- **Stack Teknis**:
  - Backend: Laravel LTS (PHP 8.x), Artisan command untuk batch job.
  - Frontend: Blade + Tailwind CSS 3.x + Alpine.js, layout responsif & dark mode.
  - Database: MySQL/MariaDB dengan dukungan SQLite untuk testing.
- **Arsitektur**:
  - Modular service layer (AllocationService, UnitCostService, TariffService, ComplianceCalculator).
  - Multi-tenant via `hospital_id` + middleware `set.hospital`.
  - API-ready: route pencarian (cost reference, JKN CBG, tarif) dapat dibuka sebagai endpoint JSON.
- **Keamanan & Audit**:
  - Role-based access (Spatie Permission).
  - Proteksi CSRF, hashing password (bcrypt/argon2), verifikasi email.
  - Audit log setiap CRUD penting + pencatatan export.
- **Kinerja & Skalabilitas**:
  - Import menggunakan chunking + validasi baris, siap dipindahkan ke queue workers.
  - Allocation/unit cost berjalan dalam transaksi database untuk konsistensi.
  - Rekomendasi pemisahan storage (object storage) tersedia dalam panduan deployment.
- **Operasional**:
  - CLI & dokumen deployment (Laravel Cloud, object storage) telah disediakan.
  - Monitoring kesalahan melalui log, disertai panduan troubleshooting storage & gambar.

---

## 7. Roadmap Pengembangan

| Tahap                                             | Fokus                                                                                  | Status                    |
| ------------------------------------------------- | -------------------------------------------------------------------------------------- | ------------------------- |
| **Tahap 1 – MVP Costing & Pathway**               | Pathway builder, patient case, dashboard dasar                                         | ✅ Selesai                |
| **Tahap 2 – Master Data & Tariff Suite**          | Cost center, GL intake, allocation, unit cost, tariff simulation/final                 | ✅ Selesai                |
| **Tahap 3 – Integrasi & Pelaporan Lanjut**        | SIMRS viewer & sync, advanced reports, tariff explorer                                 | ✅ Selesai (iterasi awal) |
| **Tahap 4 – Penguatan Operasional**               | API token, system settings lanjutan, job queue untuk import besar, notifikasi internal | ⚙️ Sedang berjalan        |
| **Tahap 5 – Analitik Prediktif & Multi-RS Scale** | Benchmark lintas RS, predictive alert, otomatisasi retarif                             | ⏳ Direncanakan           |

Catatan: setiap tahap mengikuti pendekatan _agile_, sehingga backlog dapat diatur ulang sesuai temuan lapangan.

---

## 8. Kesimpulan

Aplikasi KMKB kini mencakup siklus penuh costing → tariff → pathway → monitoring kasus, lengkap dengan integrasi data dan kontrol akses multi-tenant. Dengan platform ini, rumah sakit dapat:

- Menstandarkan biaya dan tarif berbasis data aktual.
- Mengurangi variansi klinis melalui pathway yang dapat diaudit.
- Mengambil keputusan cepat lewat KPI, laporan, dan jejak audit terpadu.

Fase berikutnya berfokus pada otomasi lanjutan dan prediksi sehingga manfaat mutu-biaya dapat terus ditingkatkan.
