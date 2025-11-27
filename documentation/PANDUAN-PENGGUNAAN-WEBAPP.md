# Panduan Penggunaan WebApp Costing, Tariff, dan Clinical Pathway Management System

## Daftar Isi
1. [Pendahuluan](#pendahuluan)
2. [Ikhtisar Peran & Menu](#ikhtisar-peran--menu)
3. [Workflow Cepat End-to-End](#workflow-cepat-end-to-end)
4. [Persiapan Awal & Navigasi](#persiapan-awal--navigasi)
5. [Setup Master Data](#setup-master-data)
6. [Input Data Operasional](#input-data-operasional)
7. [Cost Allocation](#cost-allocation)
8. [Unit Costing](#unit-costing)
9. [Tariff Management](#tariff-management)
10. [Clinical Pathway Management](#clinical-pathway-management)
11. [Patient Case Management](#patient-case-management)
12. [Analytics & Reporting](#analytics--reporting)
13. [SIMRS Integration & Service Volume Current](#simrs-integration--service-volume-current)
14. [System Administration & Utilities](#system-administration--utilities)
15. [Tips & Best Practices](#tips--best-practices)
16. [Troubleshooting](#troubleshooting)
17. [Lampiran & Template](#lampiran--template)

---

## Pendahuluan
WebApp Costing, Tariff, dan Clinical Pathway Management System adalah platform Laravel + Tailwind berbasis multi-tenant yang menyatukan modul costing, penetapan tariff, clinical pathway, dan monitoring kasus pasien. Dokumen ini menjadi rujukan operasional bagi seluruh peran (superadmin sampai auditor) untuk menjalankan proses mutu-biaya secara konsisten.

### Tujuan Sistem
- Standarisasi pengelolaan cost center, expense category, driver, dan tariff class.
- Otomatisasi perhitungan allocation → unit cost → simulasi tariff → final tariff.
- Digitalisasi clinical pathway lengkap dengan builder, versi, approval, dan estimasi biaya.
- Monitoring kasus aktual terhadap pathway, termasuk compliance dan cost variance.
- Integrasi data SIMRS serta penyediaan laporan KPI siap ekspor.

---

## Ikhtisar Peran & Menu
| Peran | Modul Utama | Catatan |
| --- | --- | --- |
| Superadmin | Hospitals, Dashboard Superadmin, Audit Logs | Memilih hospital aktif & troubleshooting lintas tenant. |
| Admin RS / Administrator IT | Master Data, Users, SIMRS, Settings | Setup awal, kelola user, koneksi SIMRS, storage. |
| Financial Manager | GL & Expenses, Allocation, Unit Cost, Final Tariffs, Reports | Menyetujui data costing & hasil tariff. |
| Costing Analyst | Master Data Costing, GL Intake, Allocation Engine, Unit Cost, Tariff Simulation | Menjalankan proses costing harian/bulanan. |
| Pathway Designer / Tim Mutu | Clinical Pathways, Cost References, Reports | Menyusun pathway, versi, dan dokumentasi. |
| Medical Committee | Pathway Approval, Reports | Memberi catatan dan keputusan pathway. |
| Case Manager / Unit Klaim | Patient Cases, Tariff Explorer, Reports | Menginput kasus dan analisis variance. |
| Auditor & Manajemen | Dashboard, Analytics, Audit Logs | Membaca KPI dan jejak audit read-only. |

Ikon menu utama berada di sidebar kiri. Gunakan tombol `Ctrl/Cmd + K` untuk membuka Command Palette dan melompat ke halaman tertentu.

---

## Workflow Cepat End-to-End
1. **Setup awal (sekali)**: master data + allocation map + referensi knowledge.
2. **Input bulanan**: GL Expenses → Driver Statistics → Service Volumes.
3. **Processing**: Run allocation → Hitung unit cost → Simulasi & tetapkan final tariff.
4. **Clinical**: Bangun pathway, salin biaya estimasi, jalankan approval.
5. **Operational**: Catat patient case, copy langkah pathway, lengkapi actual services.
6. **Analisis**: Gunakan laporan compliance, variance, pathway performance, dan dashboards.
7. **Integrasi & utilitas**: Sinkronisasi SIMRS, kelola user/role, pantau audit logs.

Gunakan tabel Workflow Lengkap di [Lampiran](#lampiran--template) untuk checklist harian/bulanan.

---

## Persiapan Awal & Navigasi
1. **Login** menggunakan kredensial masing-masing peran.
2. **Superadmin** memilih hospital melalui halaman `Hospitals → Select` sebelum mengakses modul lain.
3. **Dashboard** menampilkan tile KPI (cost trend, tariff margin, pathway compliance, cost variance, aktivitas terbaru). Klik tile untuk shortcut.
4. Gunakan **Profile menu** kanan atas untuk ganti password, ubah bahasa tampilan, atau logout.

---

## Setup Master Data
Master data wajib disiapkan sebelum menjalankan proses costing. Lakukan dalam urutan berikut:

### 1. Cost Centers
- Menu: `Master Data → Cost Centers`.
- Input kode, nama, tipe (`support` atau `revenue`), hierarki parent, serta status aktif.
- Gunakan tombol **Export** untuk backup atau audit.

### 2. Expense Categories
- Menu: `Master Data → Expense Categories`.
- Wajib mengisi account code, nama, cost type (fixed/variable/semi), allocation category (Gaji, BHP, Depresiasi, Lain).
- Pastikan mapping COA ke kategori alokasi telah disepakati tim akuntansi.

### 3. Allocation Drivers
- Menu: `Master Data → Allocation Drivers`.
- Definisikan nama driver, satuan, dan deskripsi.
- Contoh: `Luas Lantai (m2)`, `Jumlah Karyawan (orang)`, `Jam Layanan (jam)`.

### 4. Tariff Classes
- Menu: `Master Data → Tariff Classes`.
- Digunakan oleh unit cost dan modul tariff; isi kode, nama, deskripsi, dan status aktif.

### 5. Cost References (Chargemaster)
- Menu: `Master Data → Cost References`.
- Input service code, deskripsi, unit, sumber, serta link ke cost center dan expense category.
- Fitur penting: template import, bulk delete, pencarian cepat, dan SIMRS sync.

### 6. Knowledge References
- Menu: `Referensi`.
- Simpan SOP, guideline, dan pengumuman penting (markdown). Gunakan pin untuk menampilkan catatan prioritas.

### 7. JKN CBG Codes (Opsional tetapi disarankan)
- Menu: `Master Data → JKN CBG Codes`.
- Admin membuat kode INA-CBG lengkap dengan base tariff; semua role dapat mencari melalui endpoint publik.

**Checklist Master Data** tersedia di lampiran untuk memastikan setiap hospital siap menjalankan proses costing.

---

## Input Data Operasional
Dilakukan setiap periode (bulanan atau sesuai kebutuhan laporan).

### 1. GL Expenses
- Menu: `GL & Expenses → GL Expenses`.
- Input manual atau impor Excel (cost center code, expense category code, amount).
- Validasi kesesuaian dengan laporan keuangan sebelum melanjutkan.

### 2. Driver Statistics
- Menu: `GL & Expenses → Driver Statistics`.
- Masukkan nilai driver per cost center dan periode. Gunakan import untuk bulk update.

### 3. Service Volumes
- Menu: `GL & Expenses → Service Volumes` atau `Unit Costing → Service Volumes`.
- Catat volume layanan per cost reference dan (opsional) tariff class. Dapat diimpor massal.

### 4. Data Quality Checklist
- Semua cost center dan expense category memiliki GL.
- Semua driver yang dipakai allocation memiliki nilai > 0.
- Layanan yang akan dihitung unit cost-nya memiliki volume.
- Catat hasil pengecekan dalam Knowledge References agar dapat diaudit.

---

## Cost Allocation
### 1. Setup Allocation Maps
- Menu: `Allocation → Allocation Maps`.
- Tentukan source cost center (support), allocation driver, dan urutan step.
- Step sequence menentukan prioritas step-down; cost center yang sudah dialokasikan tidak lagi menerima alokasi berikutnya.

### 2. Run Allocation
- Menu: `Allocation → Run Allocation`.
- Pilih periode, review konfigurasi prereq (GL & driver). Klik **Run** untuk mengeksekusi `AllocationService`.
- Sistem menjalankan dalam transaksi; jika ada error, seluruh proses dibatalkan.

### 3. Review Allocation Results
- Menu: `Allocation → Allocation Results`.
- Filter berdasarkan periode dan versi, lihat source-target, step, dan nilai alokasi.
- Gunakan tombol export untuk Excel/PDF.

### 4. Tips
- Arsipkan snapshot hasil alokasi ke Knowledge References untuk audit.
- Gunakan catatan warning yang ditampilkan modul (misal selisih total biaya) sebagai indikator data belum lengkap.

---

## Unit Costing
### 1. Validasi Service Volume
- Pastikan data volume lengkap sebelum menjalankan perhitungan.

### 2. Calculate Unit Cost
- Menu: `Unit Costing → Calculate Unit Cost`.
- Pilih periode, beri label versi (misal `UC_2025_JAN`).
- Sistem menggabungkan GL (direct) + allocation result (overhead) + volume → direct material, direct labor, indirect overhead.

### 3. Review Unit Cost Results
- Menu: `Unit Costing → Unit Cost Results`.
- Filter berdasarkan versi, periode, layanan. Klik detail untuk breakdown cost center.
- Fitur compare version membantu analisis trend.

### 4. Export & Audit
- Export ke Excel/PDF, catat asumsi di Knowledge References.
- Unit cost version menjadi dasar modul tariff dan clinical pathway estimasi biaya.

---

## Tariff Management
### 1. Tariff Simulation
- Menu: `Tariff → Tariff Simulation`.
- Pilih unit cost version, atur margin global atau per layanan, preview hasil, bandingkan skenario, lalu export.

### 2. Final Tariffs
- Menu: `Tariff → Final Tariffs`.
- Isi service, tariff class, unit cost version, margin, komponen jasa sarana/pelayanan, SK number, effective date, dan status.
- Workflow: Draft → Review → Approved. Hanya status approved yang muncul di Tariff Explorer.

### 3. Tariff Explorer
- Menu: `Tariff → Tariff Explorer`.
- Semua role dapat mencari tarif berdasarkan kode, kelas, atau tanggal efektif. Tersedia histori perubahan dan komparasi INA-CBG.

### 4. Export & Distribusi
- Gunakan export PDF/Excel untuk publikasi internal. Cantumkan SK di Knowledge References agar mudah ditemukan.

---

## Clinical Pathway Management
### 1. Create Pathway
- Menu: `Clinical Pathways → Pathway List → Add New`.
- Isi nama, deskripsi, diagnosis/INA-CBG code, expected LOS, versi, status.

### 2. Pathway Builder
- Builder mendukung drag-and-drop, impor template Excel, dan pengaturan mandatory/optional.
- Setiap langkah dapat dikaitkan dengan cost reference sehingga estimasi biaya otomatis terisi.

### 3. Recalculate Summary & Export
- Klik **Recalculate Summary** untuk memperbarui estimasi total biaya/tariff.
- Ekspor ke PDF atau DOCX untuk presentasi atau kebutuhan akreditasi.

### 4. Approval Workflow
- Medical Committee melakukan review di halaman pathway. Gunakan komentar untuk catatan klinis.
- Status berubah dari Draft → Review → Approved → Archived.

---

## Patient Case Management
### 1. Register Case
- Menu: `Patient Cases → Case List → Add New`.
- Isi MRN, pathway terkait, tanggal masuk, diagnosis, INA-CBG, skema pembayaran, unit cost version.
- Fitur upload Excel tersedia untuk bulk input.

### 2. Case Details
- Gunakan tombol **Copy Steps from Pathway** untuk membuat planned steps otomatis.
- Tambahkan layanan tambahan jika ada varian. Sistem menampilkan unit cost dan tarif yang berlaku.

### 3. Recalculate & Analysis
- Klik **Recalculate** untuk menghitung actual total cost, tariff, compliance %, dan cost variance.
- Tab Analysis menampilkan planned vs actual, skipped, additional steps, serta komparasi INA-CBG.

### 4. Export & Catatan
- Export analysis (PDF/Excel) untuk rapat mutu. Gunakan kolom anotasi untuk dokumentasi variansi klinis.

---

## Analytics & Reporting
Menu: `Reports`.

| Laporan | Fungsi | Highlight |
| --- | --- | --- |
| Dashboard Report | Ringkasan KPI tambahan | Widget interaktif + filter periode. |
| Pathway Compliance | Monitoring kepatuhan per pathway/departemen | Top non-compliant steps. |
| Cost Variance Analysis | Selisih actual vs estimasi vs INA-CBG | Ranking kasus variance tertinggi. |
| Pathway Performance | LOS, cost efficiency, compliance vs cost | Cocok untuk rapat mutu. |
| Cost Center Performance | Pre/post allocation, expense breakdown | Digunakan FM/Admin. |
| Allocation Results Summary | Resume step-down per periode | Flow diagram + compare versi. |
| Unit Cost Summary | Trend unit cost & breakdown | Dapat diekspor per departemen. |
| Tariff Comparison | Internal vs INA-CBG | Margin analysis per kelas. |

**Export Jobs**: halaman `Reports → Export` memungkinkan generate PDF/Excel secara asynchronous dan menyimpan arsip download.

---

## SIMRS Integration & Service Volume Current
### 1. Konfigurasi SIMRS
- Admin mengatur koneksi di `.env` / panel konfigurasi sesuai panduan `SIMRS_DATABASE_SETUP.md`.

### 2. Viewer Data SIMRS
- Menu: `SIMRS → (Master Barang / Tindakan Rawat Jalan / Rawat Inap / Laboratorium / Radiologi / Operasi / Kamar)`.
- Setiap halaman memiliki filter, status last sync, dan tombol export.

### 3. Sync Management
- Menu: `SIMRS → Sync`.
- Tersedia tombol manual (misal `Sync Drugs`) dengan log status sukses/gagal. Catat error di Knowledge References bila perlu tindakan lanjutan.

### 4. Service Volume Current
- Menu: `Service Volume Current → (kategori)`.
- Monitoring volume aktual per layanan (misal kamar, operasi). Dilengkapi tombol export dan placeholder integrasi realtime.

---

## System Administration & Utilities
| Modul | Deskripsi |
| --- | --- |
| Hospitals | Superadmin menambah/mengedit rumah sakit dan memilih hospital aktif. |
| Users | Admin mengelola akun, role, reset password, dan status aktif. |
| Roles & Permissions | Pengaturan role berbasis Spatie Permission (opsional di UI). |
| Audit Logs | Menyimpan semua aktivitas penting; dapat difilter dan diekspor. |
| Migrate Storage | Wizard untuk memindahkan file evidence/pathway ke storage baru atau S3. |
| API Tokens (Roadmap Tahap 4) | Persiapan integrasi pihak ketiga dengan scope terbatas. |
| Diagnostics Scripts | Lihat Folder `documentation/*` (misal `TROUBLESHOOTING_ERROR_500.md`). |

Gunakan menu **Profile → Switch Hospital** (bagi superadmin) untuk berganti tenant tanpa logout.

---

## Tips & Best Practices
1. **Master Data**: jaga konsistensi kode; gunakan export sebagai backup rutin.
2. **Data Operasional**: tetapkan SLA input (misal setiap tanggal 5 bulan berikutnya selesai).
3. **Allocation & Unit Cost**: document-kan asumsi dan hasil verifikasi di Knowledge References.
4. **Tariff**: simpan SK resmi sebagai lampiran di pathway atau knowledge base.
5. **Clinical Pathway**: gunakan import template saat membuat pathway baru dari versi naratif.
6. **Patient Case**: disiplinkan pencatatan alasan varian agar mudah saat audit.
7. **Reporting**: jadwalkan export bulanan dan arsipkan di folder aman.
8. **Security**: rotasi password admin, review audit log minimal mingguan.
9. **Integrasi SIMRS**: lakukan sync di jam tidak sibuk; gunakan log untuk memverifikasi perubahan.
10. **Kolaborasi**: manfaatkan Knowledge References sebagai "living document" lintas tim.

---

## Troubleshooting
| Masalah | Kemungkinan Penyebab | Solusi |
| --- | --- | --- |
| Data import gagal | Format tidak sesuai template, kode belum terdaftar | Unduh ulang template, pastikan kode tersedia di master data, cek log error. |
| Allocation menghasilkan selisih | Driver kosong, GL belum lengkap | Review driver statistics & GL, perbaiki data lalu jalankan ulang. |
| Unit cost ekstrem | Volume nol, mapping cost reference salah | Validasi service volumes, cek mapping cost center di cost reference. |
| Pathway compliance rendah | Langkah tidak realistis atau data kasus belum lengkap | Tinjau pathway, komunikasikan ke tim lapangan, update pathway bila perlu. |
| Variance kasus tinggi | Layanan tambahan tidak tercatat, tarif INA-CBG berbeda | Lengkapi case detail, ulangi recalculation, dokumentasikan justifikasi. |
| Sync SIMRS gagal | Koneksi DB, kredensial salah | Test koneksi via `test_simrs_connection.php`, update konfigurasi, ulangi sync. |
| Export job macet | Worker belum dijalankan | Jalankan queue worker atau gunakan opsi download manual. |
| File evidence hilang | Storage belum dimigrasi | Buka `System Admin → Migrate Storage`, ikuti langkah pada `MIGRATE_TO_OBJECT_STORAGE.md`. |

---

## Lampiran & Template
1. **Checklist Setup Hospital Baru**
   - [ ] Cost Centers selesai
   - [ ] Expense Categories selesai
   - [ ] Allocation Drivers selesai
   - [ ] Tariff Classes selesai
   - [ ] Cost References impor
   - [ ] Knowledge References minimal 1 SOP
   - [ ] JKN CBG (opsional) siap
   - [ ] Allocation Maps dibuat

2. **Workflow Bulanan (Ringkas)**
   1. Input GL → Driver → Volume
   2. Jalankan Allocation → Review hasil
   3. Hitung Unit Cost → Ekspor
   4. Simulasikan Tariff → Finalisasi jika diperlukan
   5. Update Pathway/Case bila ada perubahan klinis
   6. Generate Laporan (Compliance, Variance, Tariff)

3. **Template Excel**
   - Dapat diunduh langsung dari masing-masing halaman import (GL, Driver, Service Volume, Pathway Steps, Cases).
   - Simpan versi template di knowledge base untuk referensi offline.

4. **Glosarium**
   - **Unit Cost Version**: Snapshot hasil kalkulasi per periode.
   - **Allocation Driver**: Basis pembagi biaya step-down.
   - **Compliance %**: Persentase langkah pathway yang terlaksana sesuai rencana.
   - **Variance**: Selisih biaya aktual vs estimasi vs INA-CBG.

5. **Dokumen Pendukung**
   - Lihat folder `documentation/` (BRD, PRD, checklist deployment, panduan SIMRS, dsb) untuk detail teknis tambahan.

---

_Dokumen ini diperbarui selaras dengan fitur per November 2025. Gunakan tombol "Feedback" di aplikasi atau tambah entri baru di Knowledge References jika menemukan gap panduan._
