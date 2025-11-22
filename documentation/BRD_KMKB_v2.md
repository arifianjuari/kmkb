# **Dokumen Kebutuhan Bisnis (BRD)**
### **Proyek: Pengembangan Aplikasi Web Kendali Mutu Kendali Biaya (KMKB) Berbasis Clinical Pathway**

---

## 1. Tujuan Proyek dan Ruang Lingkup

### **1.1. Tujuan Proyek**
Proyek ini bertujuan untuk membangun sebuah aplikasi web **Kendali Mutu Kendali Biaya (KMKB)** yang berbasis *clinical pathway* (jalur klinis). Tujuannya adalah untuk meningkatkan kualitas pelayanan kesehatan sekaligus mengendalikan biaya perawatan di rumah sakit.

*Clinical pathway* akan digunakan sebagai instrumen standar untuk:
- Memastikan pelayanan medis sesuai dengan panduan berbasis bukti.
- Mengurangi variasi yang tidak perlu dalam tata laksana pasien.
- Mendorong perawatan kesehatan yang lebih efisien dan bernilai tinggi.

Dengan meningkatkan kepatuhan terhadap *clinical pathway*, diharapkan mutu layanan akan meningkat dan selisih negatif antara biaya riil rumah sakit dengan tarif paket **INA-CBG** (tarif klaim BPJS Kesehatan) dapat berkurang.

### **1.2. Ruang Lingkup**

#### **Dalam Cakupan (In-Scope)**
- **Pilot Project**: Implementasi awal terbatas pada satu rumah sakit.
- **Pengguna Utama**: Aplikasi difokuskan untuk **Unit Klaim** dan **Tim Mutu**.
- **Fungsionalitas Inti**:
    - Digitalisasi *clinical pathway* dari format naratif ke format terstruktur.
    - Pencatatan data pelayanan aktual pasien untuk perbandingan dengan *pathway*.
    - Perhitungan otomatis kepatuhan terhadap *pathway*.
    - Analisis selisih biaya antara biaya riil dan tarif INA-CBG.
    - Penyediaan laporan dan *dashboard* Indikator Kinerja Kunci (KPI).
- **Arsitektur**: Sistem akan dibangun sebagai aplikasi mandiri (*standalone*) namun dirancang fleksibel dengan API dan struktur data yang siap untuk integrasi di masa depan dengan Sistem Informasi Rumah Sakit (HIS/SIMRS) dan sistem BPJS Kesehatan.

#### **Di Luar Cakupan (Out-of-Scope) Tahap Awal**
- Integrasi langsung dan *real-time* dengan HIS/SIMRS atau sistem BPJS Kesehatan.
- Fitur klinis mendalam seperti *Computerized Physician Order Entry* (CPOE) atau rekam medis elektronik penuh.
- Integrasi *real-time* dengan perangkat medis.
- Dukungan untuk multi-rumah sakit (akan dirancang arsitekturnya, namun tidak diimplementasikan penuh di awal).

---

## 2. Sasaran Pengguna dan Alur Kerja

### **2.1. Peran Pengguna (User Roles)**

#### **Tim Mutu Rumah Sakit**
Bertanggung jawab menyusun, memelihara, dan memonitor *clinical pathway*.
- **Menyusun/Memperbarui Pathway**: Menggunakan fitur **Pathway Builder** untuk mengubah *pathway* naratif menjadi format digital terstruktur.
- **Monitoring Kepatuhan**: Memantau KPI kepatuhan dan variansi melalui *dashboard* untuk analisis dan audit klinis.
- **Pelaporan**: Menyiapkan laporan rutin terkait mutu dan biaya untuk keperluan akreditasi (misalnya JCI) dan laporan manajemen.

#### **Unit Klaim (Billing/Case Mix Team)**
Bertanggung jawab mengelola tagihan pasien, klaim BPJS, dan memantau aspek biaya.
- **Input Data Kasus**: Memasukkan data pelayanan pasien secara manual atau melalui unggahan file (misal, Excel).
- **Analisis Biaya**: Menggunakan sistem untuk menghitung selisih antara biaya riil dengan tarif INA-CBG.
- **Analisis Kepatuhan**: Menganalisis kepatuhan kasus terhadap *pathway* dan mencatat justifikasi jika ada varian.
- **Pelaporan Biaya**: Memonitor tren biaya dan melaporkan area yang sering melampaui tarif kepada manajemen.

#### **Manajemen Rumah Sakit**
Pengguna pasif yang mengonsumsi *insight* dari sistem untuk pengambilan keputusan strategis.
- **Mengawasi KPI**: Memantau metrik kunci seperti *cost per case*, tingkat kepatuhan, dan efisiensi biaya melalui *dashboard* dan laporan.
- **Pengambilan Keputusan**: Menggunakan data untuk menentukan fokus perbaikan mutu, pengendalian biaya, dan alokasi anggaran.

#### **Administrator IT**
Bertanggung jawab atas pengelolaan teknis dan pemeliharaan sistem.
- **Manajemen Pengguna**: Mengelola akun pengguna dan hak akses.
- **Konfigurasi Awal**: Melakukan pengaturan awal sistem, termasuk *master data*.
- **Pemeliharaan dan Keamanan**: Memantau log aktivitas (*audit trail*) dan memastikan integritas serta keamanan data.

### **2.2. Alur Kerja Utama (Main Workflow)**
1.  **Perancangan Pathway**: Tim Mutu mendefinisikan dan memasukkan *clinical pathway* ke dalam sistem menggunakan **Pathway Builder**.
2.  **Input Data Kasus Pasien**: Setelah pasien selesai dirawat, Unit Klaim menginput detail pelayanan dan biaya yang diterima pasien ke dalam sistem.
3.  **Kalkulasi Otomatis oleh Sistem**: Sistem secara otomatis:
    - **Mengukur Kepatuhan**: Membandingkan layanan yang diberikan dengan standar di *pathway* dan menghasilkan persentase kepatuhan.
    - **Menghitung Selisih Biaya**: Menjumlahkan biaya riil dan membandingkannya dengan tarif INA-CBG, lalu menampilkan selisihnya.
4.  **Review dan Tindak Lanjut**: Tim Mutu dan Unit Klaim mereview kasus dengan kepatuhan rendah atau selisih biaya signifikan, serta mencatat justifikasi varian.
5.  **Analisis via Dashboard KPI**: Manajemen dan tim terkait memantau KPI secara kumulatif untuk mengidentifikasi area yang memerlukan perbaikan.
6.  **Peningkatan Berkesinambungan**: Berdasarkan *insight* yang didapat, Tim Mutu memperbarui *pathway* atau melakukan pelatihan, sementara manajemen mengambil langkah strategis untuk efisiensi biaya.

---

## 3. Fitur Fungsional

### **3.1. Fitur Utama**

#### **Pathway Builder (Penyusun Clinical Pathway)**
Fitur untuk membuat, mengedit, dan menyimpan *clinical pathway* dalam format digital terstruktur, lengkap dengan tahapan, kriteria mutu, dan estimasi biaya.

#### **Manajemen Referensi Biaya (CRUD)**
Modul untuk mengelola *master data* referensi biaya (`CostReference`) yang akan digunakan sebagai acuan dalam penyusunan estimasi biaya pada *pathway*.

#### **Unggah Data Biaya dan Tarif**
- **Unggah Biaya Sampel Kasus**: Mengimpor data historis kasus pasien untuk analisis awal.
- **Master Tarif & Unit Cost**: Memelihara database internal berisi tarif komponen layanan sebagai referensi perhitungan biaya.

#### **Dashboard & Laporan KPI**
*Dashboard* interaktif dengan antarmuka modern (Tailwind CSS 3.x, *dark mode*) yang menampilkan KPI utama seperti:
- Persentase kepatuhan *pathway*.
- Selisih biaya rata-rata per diagnosa (vs. INA-CBG).
- Jumlah kasus *over/under budget*.
- Rata-rata *Length of Stay* (LOS) vs. target.

#### **Kalkulasi Kepatuhan Pathway**
Fitur *backend* yang secara otomatis membandingkan layanan yang diterima pasien dengan standar *pathway* dan menghasilkan:
- *Checklist* kepatuhan per langkah.
- Persentase kepatuhan total.
- Daftar varian (layanan di luar *pathway* atau yang terlewat).

#### **Kalkulasi Selisih Biaya**
Sistem secara otomatis menghitung selisih biaya aktual terhadap:
- **Estimasi Pathway**: Untuk mengukur apakah kasus *over/under budget* dari rencana.
- **Tarif INA-CBG**: Untuk mengukur profitabilitas kasus bagi rumah sakit.

#### **Mekanisme Rekonsiliasi Pathway dan Billing**
Fitur untuk memetakan item pada data *billing* rumah sakit ke langkah-langkah *clinical pathway* untuk validasi kepatuhan yang lebih akurat.

#### **Jejak Audit (Audit Trail)**
Mencatat semua aktivitas penting dalam sistem (siapa, kapan, apa) untuk tujuan keamanan, akuntabilitas, dan audit.

### **3.2. Fitur Pendukung**

#### **Manajemen Pengguna & Hak Akses**
Modul untuk mengatur pengguna, peran (*role*), dan hak akses berdasarkan prinsip *least privilege*, termasuk peran **Superadmin** untuk pengelolaan multi-tenant.

#### **Master Data Klinik**
Pengelolaan *master data* pendukung seperti diagnosa (ICD-10), prosedur (ICD-9 CM), dan unit layanan.

#### **Notifikasi & Pengingat (Opsional di MVP)**
Sistem dapat mengirim notifikasi internal jika ada *pathway* yang perlu diperbarui atau jika KPI berada di bawah ambang batas tertentu.

---

## 4. Struktur Data dan Basis Data

### **4.1. Model Entitas Relasional (ERD)**
Struktur data dirancang menggunakan RDBMS dengan entitas utama sebagai berikut:
- **`ClinicalPathway`**: Merepresentasikan satu *clinical pathway* untuk diagnosis tertentu.
- **`PathwayStep`**: Langkah atau tahapan spesifik dalam sebuah `ClinicalPathway`.
- **`PatientCase`**: Satu episode perawatan pasien yang dievaluasi.
- **`CaseDetail`**: Rincian layanan yang diberikan pada `PatientCase` untuk mengukur kepatuhan.
- **`CostReference`**: Master data tarif atau biaya standar layanan.
- **`User`**: Data pengguna sistem beserta perannya.
- **`AuditLog`**: Log untuk mencatat semua aktivitas penting.
- **Entitas Tambahan**: Termasuk `canonical_services` dan `service_map` untuk mendukung mekanisme rekonsiliasi.

### **4.2. Catatan Implementasi Terkini (Agustus 2025)**
- **Pathway Builder**: Mendukung *drag-and-drop* untuk mengubah urutan langkah (`display_order`) dan impor massal langkah-langkah *pathway* melalui template Excel/CSV.
- **Manajemen Referensi Biaya**: Modul CRUD penuh telah tersedia dengan *pagination* dan validasi.
- **Seeder Pengguna**: Disediakan *seeder* untuk membuat akun *default* per peran guna memudahkan pengujian.

---

## 5. Standar Acuan

Sistem ini dikembangkan dengan mengacu pada beberapa standar mutu, biaya, dan teknis, antara lain:
- **ISO 7101:2023**: Standar sistem manajemen mutu untuk organisasi pelayanan kesehatan.
- **Standar Akreditasi JCI**: Standar internasional untuk mutu dan keselamatan pasien.
- **INA-CBG**: Sistem tarif paket JKN sebagai *benchmark* utama pengendalian biaya.
- **Regulasi Kemenkes RI**: Pedoman nasional terkait penerapan *clinical pathway*.
- **Prinsip Keamanan Informasi**: Mengacu pada praktik terbaik seperti OWASP dan standar ISO 27001.

---

## 6. Komponen Teknis (Kebutuhan Non-Fungsional)

- **Platform & Stack Teknologi**:
    - **Backend**: PHP dengan framework Laravel (LTS).
    - **Frontend**: Tailwind CSS 3.x dan Alpine.js.
    - **Database**: MySQL atau MariaDB.
    - **Server**: Linux dengan stack LAMP/LEMP.
- **Arsitektur Aplikasi**:
    - **Modular**: Dibangun dengan modul yang terpisah (*low coupling, high cohesion*) untuk kemudahan pengembangan.
    - **Multi-tenant**: Dirancang sejak awal untuk mendukung banyak rumah sakit dengan pemisahan data berbasis `hospital_id` (tanpa subdomain).
- **Keamanan Aplikasi**:
    - Otentikasi dan otorisasi berbasis peran.
    - Validasi input dan proteksi CSRF.
    - *Password hashing* (bcrypt/argon2).
    - Pencatatan *audit trail* yang komprehensif.
    - Koneksi wajib menggunakan HTTPS (SSL).
- **Extensibility & Skalabilitas**:
    - Desain **API-Ready** untuk integrasi di masa depan.
    - Kode yang terdokumentasi dengan baik mengikuti standar PSR.
    - Optimasi *query* untuk menangani volume data yang besar.

---

## 7. Roadmap Pengembangan

Pengembangan akan dilakukan secara bertahap dengan pendekatan *agile*.

- **Tahap 1: MVP (Minimum Viable Product) (3-4 bulan)**
  Fokus pada fungsionalitas inti: *Pathway Builder* sederhana, input data manual, kalkulasi dasar, dan *dashboard* statis untuk validasi konsep oleh pengguna.

- **Tahap 2: Peningkatan Fitur & Stabilitas (2-3 bulan)**
  Menyempurnakan UI/UX (refactor ke Tailwind CSS, *dark mode*), menambah fitur unggah data massal, laporan yang lebih canggih, dan penguatan keamanan.

- **Tahap 3: Integrasi Dasar (3-6 bulan)**
  Mulai menghubungkan aplikasi dengan sistem lain seperti HIS/SIMRS untuk menarik data secara otomatis dan mengurangi input manual.

- **Tahap 4: Pengayaan Fitur Lanjutan (3 bulan)**
  Menambahkan modul analitik prediktif, *benchmarking* antar unit, dan akses terbatas untuk para klinisi.

- **Tahap 5: Skalabilitas dan Pemeliharaan Berkelanjutan**
  Fokus pada perbaikan *bug*, pembaruan teknologi, dan persiapan untuk implementasi di rumah sakit lain (*roll-out*).

---

## 8. Kesimpulan

Dokumen ini menguraikan kebutuhan bisnis untuk aplikasi KMKB yang bertujuan menjadi alat digital strategis bagi rumah sakit. Dengan aplikasi ini, rumah sakit dapat menstandardisasi mutu layanan klinis dan mengendalikan biaya secara efektif. Implementasi yang sukses diharapkan dapat meningkatkan efisiensi operasional, kualitas perawatan pasien, dan mendorong budaya perbaikan berkelanjutan.