# Product Requirements Document (PRD)
## WebApp Costing, Tariff, and Clinical Pathway Management System

---

### 1. Overview
Platform multi-tenant untuk rumah sakit yang menggabungkan modul costing, manajemen tarif, clinical pathway, serta monitoring kasus pasien dalam satu aplikasi Laravel + Tailwind. Sistem memanfaatkan data GL, driver statistik, service volume, dan referensi SIMRS untuk menghasilkan unit cost terverifikasi, simulasi tarif, kontrol pathway, hingga pelaporan KPI mutu-biaya secara real-time.

### 2. Goals
- Menstandardisasi pengelolaan cost center, expense category, allocation driver, dan tariff class lintas rumah sakit.
- Menyediakan kalkulasi unit cost dan tarif final yang transparan, dapat diaudit, dan terdokumentasi per versi.
- Menjembatani perencanaan pathway klinis dengan data costing sehingga gap mutu-biaya dapat dianalisis cepat.
- Mempermudah tim klaim memonitor kepatuhan pathway serta selisih biaya aktual vs estimasi vs INA-CBG.
- Mempercepat iterasi retarif dan approval pathway melalui dashboard KPI serta laporan otomatis.
- Memastikan kesiapan integrasi dengan SIMRS dan layanan pihak ketiga melalui API-ready endpoints.

### 3. Key Modules
1. **Dashboard & Executive Insight**
2. **Master Data Costing & Tariff References**
3. **GL & Operational Data Intake (GL Expenses, Driver Statistics, Service Volumes)**
4. **Cost Allocation Engine (Step-down)**
5. **Unit Costing Engine & Versioning**
6. **Tariff Management Suite (Simulation, Final Tariff, Tariff Explorer)**
7. **Clinical Pathway Management & Builder**
8. **Patient Case & Compliance Tracking**
9. **Analytics & Reporting Center**
10. **SIMRS Integration & Service Volume Current Hub**
11. **System Administration, Hospitals, Users, Roles, Audit Logs**
12. **Knowledge References, Templates, dan Utility (Storage Migration, Export Jobs)**

### 4. User Roles & Permissions

| Peran                           | Kebutuhan Bisnis                                                                    | Modul Utama                                                        |
| ------------------------------- | ----------------------------------------------------------------------------------- | ------------------------------------------------------------------ |
| **Superadmin**                  | Mengelola tenant/hospital, lisensi, dan tindakan troubleshooting lintas RS          | Hospitals, Dashboard Superadmin, Audit Logs                        |
| **Admin RS / Administrator IT** | Setup master data dasar, konfigurasi SIMRS/storage, kelola pengguna                 | Master Data, SIMRS, Users, System Settings                         |
| **Financial Manager**           | Review GL, menjalankan allocation & unit cost, menyetujui tarif final               | GL & Expenses, Allocation, Unit Cost, Final Tariffs, Reports       |
| **Costing Analyst**             | Input costing data, menjalankan simulasi, analisis hasil                            | Master Data, GL Intake, Allocation, Unit Cost, Tariff Simulation   |
| **Pathway Designer / Tim Mutu** | Membangun/versi pathway, menyusun estimasi biaya, ekspor dokumen                    | Clinical Pathways, Cost References, Reports                        |
| **Medical Committee**           | Menilai pathway, mencatat catatan approval, memonitor variansi klinis               | Clinical Pathway Approval, Reports                                 |
| **Case Manager / Unit Klaim**   | Input/unggah kasus, catat layanan aktual, pantau kepatuhan & variansi biaya         | Patient Cases, Tariff Explorer, Reports                            |
| **Auditor & Manajemen**         | Membaca KPI, audit trail, pelaporan strategis                                       | Dashboard, Analytics, Audit Logs                                   |

### 5. Functional Requirements

#### 5.1 Dashboard & Executive Insight
- Tile KPI untuk total biaya, unit cost trend, tarif internal vs INA-CBG, compliance pathway, serta cost variance.
- Widget khusus superadmin untuk monitoring hospital aktif dan status data.
- Shortcut aksi cepat (import data, jalankan allocation, buat pathway baru).

#### 5.2 Master Data Costing & Tariff
- CRUD + pagination + pencarian untuk Cost Centers, Expense Categories, Allocation Drivers, Tariff Classes.
- Cost References mendukung bulk import (template Excel), bulk delete, link ke cost center & expense category, serta pencarian cepat.
- JKN CBG Codes dapat dicari oleh semua role, sedangkan CRUD hanya untuk admin.
- Validasi wajib: keunikan kode, status aktif, dan relasi antar master sesuai hospital.

#### 5.3 GL & Operational Data Intake
- **GL Expenses**: input manual, import CSV/XLSX, mapping ke cost center & expense category, validasi periode, export.
- **Driver Statistics**: form import & bulk input per periode-driver, validasi nilai > 0, export untuk audit.
- **Service Volumes**: data output operasional dengan filter service/tariff class, import template, export per periode.
- Laporan pendukung: daftar kategori biaya yang belum memiliki GL, log impor, serta status data setiap periode.

#### 5.4 Cost Allocation Engine
- **Allocation Maps**: definisi step sequence, source cost center, allocation driver, preview flow.
- **Run Allocation**: memilih periode, validasi prereq (GL & driver), tampilan progres, log hasil, summary total alokasi.
- **Allocation Results**: filter versi/periode, breakdown per source-target, export Excel/PDF, komparasi antar versi.

#### 5.5 Unit Costing Engine
- Pemilihan periode dan label versi unit cost.
- Perhitungan otomatis menggabungkan hasil allocation + service volume untuk menghasilkan direct, indirect, overhead cost per layanan.
- Penyimpanan status proses, pesan peringatan, serta audit trail breakdown per cost center.
- Halaman hasil dilengkapi filter service/tariff class, detail cost components, serta tombol export.

#### 5.6 Tariff Management Suite
- **Tariff Simulation**: pilih versi unit cost, set margin global/per layanan, buat beberapa skenario, preview margin vs INA-CBG, export.
- **Final Tariffs**: CRUD tarif resmi dengan metadata SK, tanggal efektif & kadaluarsa, relasi ke unit cost version, approval status, export.
- **Tariff Explorer**: pencarian cepat, filter tariff class, histori perubahan, komparasi internal vs INA-CBG.

#### 5.7 Clinical Pathway Management
- Pathway list dengan status (Draft, Review, Approved, Archived), filter diagnosa, versi, duplikasi.
- Pathway Builder drag-and-drop langkah, tandai mandatory/optional, kaitkan cost reference, auto estimasi biaya.
- Impor langkah via template, reorder massal, recalculation summary, export PDF/DOCX.
- Workflow approval oleh Medical Committee dengan komentar dan histori keputusan.

#### 5.8 Patient Case & Compliance Tracking
- CRUD kasus dengan link ke pathway, dukungan upload Excel massal.
- Auto-copy langkah pathway menjadi case detail; masing-masing langkah dapat ditandai dilakukan/tidak dan ditambahkan layanan variatif.
- Kalkulasi kepatuhan (%) per kasus, daftar varian, selisih biaya aktual vs estimasi pathway dan vs INA-CBG.
- Fitur anotasi kasus, catatan justifikasi varian, serta export laporan kasus.

#### 5.9 Analytics & Reporting
- **Compliance Report**: persentase per pathway, tren bulanan, breakdown per unit layanan.
- **Cost Variance Report**: actual vs pathway vs INA-CBG, mapping ke modul costing.
- **Pathway Performance**: LOS, cost efficiency, varian tindakan.
- **Allocation & Unit Cost Summary**: ringkasan versi, komparasi antar periode.
- **Tariff Dashboard**: margin per kelas, status SK, pipeline retarif.
- Sistem export asynchronous (generate + download) untuk PDF dan Excel.

#### 5.10 SIMRS Integration & Service Volume Current Hub
- Viewer dataset SIMRS: master barang, tindakan rawat jalan/inap, laboratorium, radiologi, operasi, kamar.
- Modul sinkronisasi manual (drugs/tarif) dengan log histori, status sukses/gagal, serta retry action.
- Halaman service-volume-current untuk memantau volume terkini per kategori dan mengekspor dataset.

#### 5.11 System Administration & Audit
- **Hospitals**: superadmin membuat/mengedit RS, memilih hospital aktif via selector khusus.
- **Users & Roles**: CRUD user, assign multiple role, force reset password, set status aktif.
- **Audit Logs**: filter berdasar user, model, aksi, periode; export; fitur clear logs (admin).
- **Migrate Storage Utility**: membantu perpindahan file evidence ke storage baru.
- **API Tokens/System Settings**: persiapan integrasi pihak ketiga, pengaturan currency/periode fiskal (backlog phase 4).

#### 5.12 Knowledge References & Templates
- Modul Reference menyimpan guideline internal, regulasi, dan catatan implementasi.
- Semua modul import menyediakan template Excel yang dapat diunduh langsung dari UI.

### 6. Nonfunctional Requirements
- **Security**: role-based access (Spatie Permission), CSRF protection, password hashing bcrypt/argon2, audit logging.
- **Performance**: dapat memproses ribuan baris GL/driver/service-volume per impor dengan chunking & queue-ready workflow.
- **Scalability**: multi-tenant berbasis `hospital_id`, siap dipindah ke worker/background job untuk kalkulasi berat.
- **Availability**: target uptime >99% untuk modul operasional; menyediakan panduan failover storage.
- **Interoperability**: API-ready endpoints (cost reference search, tariff lookup, SIMRS sync) dan dokumentasi integrasi.
- **Observability**: logging terpusat, status job export, dan panduan troubleshooting image/storage.

### 7. Data Model Overview
Entitas utama yang harus tersedia pada database produksi:
- `hospitals`, `users`, `roles`, `permissions`, `audit_logs`
- `cost_centers`, `expense_categories`, `allocation_drivers`, `tariff_classes`
- `cost_references`, `references`, `jkn_cbg_codes`
- `gl_expenses`, `driver_statistics`, `service_volumes`
- `allocation_maps`, `allocation_results`
- `unit_cost_calculations`, `unit_cost_results`
- `tariff_simulations`, `final_tariffs`, `tariff_history`
- `clinical_pathways`, `pathway_steps`, `pathway_tariff_summaries`
- `patient_cases`, `case_details`, `case_annotations`
- `simrs_*` tables (master barang, tindakan, kamar, dsb) dan log sinkronisasi

Relasi mengikuti ERD dengan kunci `hospital_id` sebagai isolasi tenant dan versioning (allocation, unit cost, tariff, pathway).

### 8. Success Metrics
- 70% pengurangan waktu kompilasi costing periode dibanding proses manual sebelumnya.
- Kalkulasi unit cost & final tariff tersedia maksimal H+5 setelah GL periode ditutup.
- >20% peningkatan kepatuhan pathway pada kasus yang terdigitalisasi.
- 100% aktivitas penting terekam di audit log dan dapat ditelusuri ulang.
- <5% selisih data antara referensi SIMRS dan master internal setelah sinkronisasi.
- Kepuasan manajemen (survey) minimal 4/5 untuk dashboard & laporan KPI.

