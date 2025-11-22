# Rencana Implementasi Aplikasi KMKB

Dokumen ini berisi breakdown tahapan implementasi aplikasi KMKB berdasarkan BRD_KMKB.md

## Tahap 1: MVP (Minimum Viable Product) - Estimasi waktu: ±3-4 bulan

### 1.1. Persiapan Awal
- [x] Setup environment development (PHP, MySQL, Apache/Nginx)
- [x] Setup framework (Laravel/CodeIgniter/Symfony)
- [x] Setup version control (Git)
- [x] Setup struktur direktori project
- [x] Setup database schema awal (skema & foreign key final; seeder awal cost_references tersedia)

### 1.2. Modul User Management & Security
- [x] Implementasi sistem login/logout
- [x] Implementasi multi-role user (admin, mutu, klaim, manajemen)
- [x] Implementasi session management
- [x] Implementasi form validation
- [x] Implementasi audit trail logging dasar
- [x] Seeder default users per role (admin, mutu, klaim, manajemen)

### 1.3. Modul Pathway Builder
- [x] Desain UI/UX untuk pembuatan pathway
- [x] Implementasi CRUD clinical pathway
- [x] Implementasi penyimpanan langkah-langkah pathway (linear)
- [x] Implementasi versi pathway
- [x] Validasi data pathway
- [x] Perbaikan mapping field (step_order, action, description, estimated_cost, quantity) agar konsisten dengan skema DB
- [x] Perbaikan AJAX untuk tambah/edit/hapus langkah (dengan CSRF dan respons JSON)

### 1.4. Modul Data Pasien & Biaya Input
- [x] Desain form input data kasus pasien
- [x] Implementasi form manual input data kasus
- [x] Implementasi template unggah CSV (jika sempat)
- [x] Validasi data input kasus
- [x] Relasi data kasus dengan pathway

### 1.5. Modul Kalkulasi Kepatuhan & Selisih
- [x] Implementasi logika perhitungan kepatuhan pathway
- [x] Implementasi perhitungan selisih biaya vs INA-CBG
- [x] Penyimpanan hasil kalkulasi ke database
- [x] Validasi hasil kalkulasi

### 1.6. Modul Dashboard Basic
- [x] Desain UI dashboard
- [x] Implementasi tampilan rata-rata kepatuhan per pathway
- [x] Implementasi tampilan jumlah kasus over/under budget
- [x] Implementasi tampilan rata-rata selisih biaya per diagnosa
- [x] Implementasi filter dasar di dashboard

### 1.7. Modul Laporan
- [x] Desain template laporan bulanan KMKB
- [x] Implementasi generate laporan PDF
- [x] Implementasi export laporan ke Excel
- [x] Routes laporan: reports.dashboard, reports.export, reports.export.generate (beserta method di ReportController)

### 1.8. Testing & Deployment
- [x] Unit testing modul-modul
- [x] User Acceptance Testing (UAT)
- [x] Perbaikan bug berdasarkan feedback UAT
- [x] Deployment ke environment staging
- [x] Deployment ke environment production

### 1.9. Modul Cost Reference (CRUD)
- [x] Resource routes untuk cost-references (web.php)
- [x] CostReferenceController (index, create, store, show, edit, update, destroy)
- [x] Blade views: index, create, edit, show (Tailwind), pagination, validasi form
- [x] Integrasi dengan Pathway Builder: dropdown memilih service_description, mengisi estimated_cost
- [x] Link navigasi "Cost References" (hanya tampil untuk role admin; desktop & responsif)
- [x] Seeder data awal cost references (CostReferencesTableSeeder)

## Tahap 2: Peningkatan Fitur & Stabilitas - Estimasi waktu: ±2-3 bulan

### 2.1. Penyempurnaan Pathway Builder
- [x] Implementasi fitur duplikasi pathway (duplicate pathway + replicate steps, status draft)
- [x] Implementasi versioning pathway yang lebih baik (semver bump major/minor/patch via selector di halaman detail pathway; baseline new draft version + replicate steps; compare UI masih TBD)
- [x] Implementasi conditional step sederhana:
  - UI badge "Conditional" untuk langkah dengan `criteria`.
  - Engine evaluasi `app/Services/CriteriaEvaluator.php` mendukung operator: `==`, `!=`, `~=` (contains, case-insensitive), `>`, `<`, `>=`, `<=`.
  - Field yang didukung: `ina_cbg_code`, `primary_diagnosis`/`diagnosis`, `los` (length of stay), `cost_over_tariff`.
  - Format criteria: kondisi dipisah titik koma (AND). Contoh: `ina_cbg_code==A123; los>=3; diagnosis~=pneumonia`.
  - Integrasi perhitungan: `app/Services/ComplianceCalculator.php` hanya menghitung langkah yang applicable; compliance = completed/applicable*100, applicable = langkah yang kriterianya true. Jika applicable = 0, compliance = 100.
  - Integrasi lifecycle: dihitung di `PatientCaseController@store` dan `@update`; auto-recalc via event di `App\\Models\\CaseDetail::booted()` saat created/updated/deleted.
- [x] Perbaikan penyimpanan langkah "Add Step" (AJAX + fallback non-JS, perbaikan CSRF/script section, logging di controller)

### 2.2. Peningkatan UI/UX
- [x] Refactor seluruh tampilan Laravel Blade dari Bootstrap 5 ke Tailwind CSS 3.x
- [x] Implementasi dark mode menyeluruh di seluruh aplikasi menggunakan strategi class pada Tailwind CSS
- [x] Peningkatan struktur HTML semantik dengan penggunaan elemen section yang tepat
- [x] Peningkatan aksesibilitas dengan penambahan atribut ARIA dan perbaikan struktur heading
- [x] Implementasi kelas tombol yang dapat digunakan kembali untuk konsistensi tampilan
- [x] Penggantian ikon Font Awesome dengan SVG inline untuk performa yang lebih baik
- [x] Peningkatan tampilan form dengan styling konsisten dan responsif
- [x] Peningkatan tampilan tabel dengan desain modern dan dark mode support
- [x] Implementasi toggle dark mode di navigation bar
- [x] Peningkatan kontras warna untuk memenuhi standar WCAG 2.1 AA
- [x] Penambahan label yang sesuai untuk elemen form
- [x] Implementasi focus ring yang jelas untuk navigasi keyboard
- [ ] Redesign dashboard dengan grafik interaktif
- [ ] Implementasi navigasi yang lebih intuitif
- [ ] Implementasi fitur search dan filter yang lebih lengkap

### 2.3. Fitur Unggah Data
- [ ] Implementasi batch upload data kasus dari Excel
- [ ] Implementasi modul master tarif
- [ ] Implementasi upload master tarif dari data billing

### 2.4. Laporan Tambahan
- [ ] Implementasi laporan detail varians per kasus
- [ ] Implementasi laporan untuk akreditasi (KARS/JCI)

### 2.5. Hardening Security
- [ ] Security review dan peningkatan
- [ ] Perbaikan bug yang ditemukan
- [ ] Optimasi performa query

## Tahap 3: Integrasi Dasar dengan Sistem Lain - Estimasi waktu: ±3-6 bulan

### 3.1. Integrasi SIMRS/HIS
- [ ] Analisis struktur database SIMRS/HIS
- [ ] Implementasi ETL atau API untuk mengambil data pasien
- [ ] Implementasi penjadwalan harian untuk sinkronisasi data
- [ ] Testing integrasi dengan data real

### 3.2. Integrasi INA-CBG Grouper
- [ ] Implementasi pembuatan file XML klaim
- [ ] Implementasi pengambilan tarif INA-CBG terbaru
- [ ] Validasi akurasi perhitungan selisih

### 3.3. Single Sign-On (SSO)
- [ ] Integrasi dengan Active Directory rumah sakit
- [ ] Testing SSO dengan user real

### 3.4. Modul Notifikasi
- [ ] Implementasi notifikasi email internal
- [ ] Implementasi WhatsApp Gateway (jika tersedia)
- [ ] Konfigurasi rule notifikasi untuk kasus outlier

## Tahap 4: Pengayaan Fitur Lanjutan - Estimasi waktu: ±3 bulan

### 4.1. Analitik Lanjutan
- [ ] Implementasi prediksi overbudget sederhana
- [ ] Implementasi analisis korelasi kepatuhan dengan outcome

### 4.2. Benchmarking & Multi-unit
- [ ] Implementasi perbandingan antar departemen
- [ ] Implementasi multi-RS (jika diperlukan)

### 4.3. Mobile Access
- [ ] Implementasi versi mobile-friendly dashboard
- [ ] (Opsional) Implementasi aplikasi mobile

### 4.4. Feedback Loop ke Klinik
- [ ] Implementasi akses terbatas untuk dokter/kadep
- [ ] Implementasi kontrol hak akses yang ketat

## Tahap 5: Scale Up dan Maintenance Berkelanjutan

### 5.1. Maintenance
- [ ] Bug fixing rutin
- [ ] Upgrade teknologi (PHP, framework, database)
- [ ] Patch keamanan berkala

### 5.2. Adaptasi Regulasi
- [ ] Penyesuaian terhadap perubahan skema INA-CBG
- [ ] Penyesuaian terhadap regulasi baru Kemenkes

### 5.3. Training & Knowledge Transfer
- [ ] Training pengguna baru
- [ ] Dokumentasi teknis dan user guide
- [ ] Knowledge transfer ke tim IT rumah sakit

### 5.4. Multi-site Implementation
- [ ] Penyesuaian konfigurasi untuk multi-RS
- [ ] Implementasi multi-instance (jika diperlukan)

## Addendum: Implementasi Multi-tenant (Single DB, Row-based, tanpa subdomain)

Catatan: sistem baru dibangun, tidak diperlukan backfill data eksisting.

### A. Skema & Migrasi
- [x] Buat tabel `hospitals` (id, name, code, logo_path, theme_color, address, contact, is_active, timestamps)
- [x] Tambah kolom `hospital_id` (nullable sementara) + index + FK ke `hospitals.id` pada tabel: `users`, `clinical_pathways`, `pathway_steps`, `patient_cases`, `case_details`, `cost_references`, `audit_logs`
- [x] Set default pengisian `hospital_id` via aplikasi saat create data baru
- [x] Ubah `hospital_id` menjadi NOT NULL setelah alur pembuatan data terverifikasi

### B. Model & Global Scope
- [x] Buat trait `BelongsToHospital` yang menambahkan Global Scope filter `hospital_id`
- [x] Tambahkan `$fillable`/`$guarded` untuk `hospital_id` di model terkait
- [x] Terapkan trait ke model: `User` (opsional untuk scope query list), `ClinicalPathway`, `PathwayStep`, `PatientCase`, `CaseDetail`, `CostReference`, `AuditLog`
- [x] Pastikan route model binding mempertimbangkan scope tenant (404 jika beda tenant)

### C. Middleware & Helper
- [x] Buat middleware `SetHospital` untuk menetapkan tenant aktif dari `auth()->user()->hospital_id`
- [x] Registrasikan middleware di `app/Http/Kernel.php` (web + api)
- [x] Sediakan helper `hospital()` untuk akses cepat tenant aktif (id, nama, logo)

### D. Policy/Otorisasi
- [x] Revisi policy agar cek kepemilikan `hospital_id` sebelum authorize action
- [x] Tambahkan test untuk mencegah akses lintas tenant

### E. UI/Branding Per Tenant (sederhana)
- [x] Tambahkan field upload logo RS dan pilih warna tema di halaman pengaturan (admin)
- [x] Simpan file di `storage/app/public/tenants/{hospital_id}/branding/`
- [x] Tampilkan logo/nama RS pada layout dan laporan

### F. Import/Export/Report Scoping
- [x] Import Pathway Steps (Excel/CSV, PhpSpreadsheet): set `hospital_id` = tenant aktif pada create/upsert
- [x] Export/Reports: semua query data ter-scope `hospital_id` tenant aktif
- [x] Pastikan template Excel tetap didukung sesuai preferensi (.xlsx sebagai default, CSV fallback)

### G. Cache/Storage/Queue Prefixing
- [x] Terapkan prefix cache berdasarkan `hospital_id` (config cache key helper)
- [x] Struktur folder storage per tenant (`tenants/{hospital_id}/...`)

### H. Seeder & Admin Awal
- [x] Seeder `hospitals` minimal 1 entri default
- [x] Seeder `users` mengikat `hospital_id` default

### I. Testing
- [x] Feature tests: akses data tenant A tidak menampilkan data tenant B
- [x] Import/export tests: data hasil impor/ekspor berada pada tenant yang benar
- [x] Policy tests: create/update/delete hanya dalam tenant sendiri
- [x] Middleware tests: request tanpa user/tenant ter-handle dengan benar

### J. Superadmin (Global Admin Multi-tenant)
- [x] Tambahkan role `superadmin` pada `User` (+ `isSuperadmin()` dan penyesuaian `hasRole()` agar superadmin bypass role check)
- [x] Migrasi `users.hospital_id` nullable khusus superadmin
- [x] Trait `BelongsToHospital`: kecualikan superadmin dari global scope `hospital_id`
- [x] Middleware `SetHospital`: jangan set `session('hospital_id')` untuk superadmin
- [x] Route Model Binding (`ClinicalPathway`, `PatientCase`): izinkan akses lintas RS untuk superadmin
- [x] Seeder: buat akun default superadmin `superadmin@example.com` (password awal "password"; ganti di produksi)
- [x] Tambah test khusus superadmin (akses lintas tenant, kebijakan otorisasi, middleware)

### K. Modul Hospital Management (CRUD) untuk Superadmin
- [x] Tambah resource route `hospitals` dengan middleware `auth` + role superadmin (ditempatkan sebelum grup route ter-scope tenant)
- [x] Implementasi `HospitalController` (index, create, store, show, edit, update, destroy)
- [x] Validasi form termasuk upload logo (penyimpanan ke storage publik per-tenant)
- [x] Blade views `resources/views/hospitals/` (index, create, edit, show) berbasiskan Tailwind CSS
- [x] Proteksi akses: hanya superadmin yang dapat mengakses modul ini

### L. Peningkatan User Management untuk Superadmin (Cross-tenant)
- [x] Update `UserController` untuk bypass scoping saat superadmin (list semua user + relasi hospital)
- [x] Tambah dukungan field `hospital_id` pada create/update dengan validasi dan pembatasan hanya superadmin yang boleh mengubah
- [x] Update views users:
  - [x] `create.blade.php`: dropdown pilih hospital tampil hanya untuk superadmin
  - [x] `edit.blade.php`: dropdown pilih hospital tampil hanya untuk superadmin
  - [x] `index.blade.php`: kolom "Hospital" tampil untuk superadmin
  - [x] `show.blade.php`: informasi hospital tampil untuk superadmin
- [x] Pastikan authorization mencegah non-superadmin menetapkan/mengubah `hospital_id`
