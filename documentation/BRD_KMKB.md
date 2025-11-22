# Dokumen Kebutuhan Bisnis 
(Business Requirements Document – BRD)

Proyek: Pengembangan Aplikasi Web Kendali Mutu Kendali Biaya (KMKB) Berbasis Clinical Pathway di Rumah Sakit

## Tujuan Proyek dan Ruang Lingkup

## Tujuan Proyek

## Proyek ini bertujuan untuk membangun sebuah aplikasi web Kendali Mutu Kendali Biaya (KMKB) yang berbasis clinical pathway (jalur klinis) guna meningkatkan kualitas pelayanan kesehatan sekaligus mengendalikan biaya perawatan di rumah sakit. Clinical pathway digunakan sebagai instrumen standar agar pelayanan medis sesuai dengan panduan berbasis bukti, mengurangi variasi yang tidak perlu dalam tata laksana pasien, serta mendorong perawatan kesehatan yang lebih efisien dan bernilai tinggi . Dengan meningkatkan kepatuhan terhadap clinical pathway, diharapkan mutu layanan meningkat dan selisih negatif antara biaya riil rumah sakit dan tarif paket INA-CBG (tarif klaim BPJS Kesehatan) dapat berkurang . Implementasi sistem ini sejalan dengan kebutuhan rumah sakit untuk beroperasi efisien tanpa mengorbankan mutu, apalagi mengingat banyak rumah sakit menghadapi masalah selisih negatif antara tarif INA-CBG dan biaya aktual pelayanan pasien .

## Ruang Lingkup

## Ruang lingkup proyek tahap awal dibatasi pada implementasi di satu rumah sakit sebagai pilot project. Aplikasi KMKB akan difokuskan untuk digunakan oleh dua kelompok pengguna utama yaitu Unit Klaim (yang menangani klaim pembiayaan, terutama terhadap BPJS Kesehatan) dan Tim Mutu (yang bertanggung jawab atas kendali mutu pelayanan klinis di rumah sakit). Lingkup fungsional mencakup digitalisasi data clinical pathway (yang sebelumnya masih berbentuk naratif di dokumen) menjadi struktur digital yang dapat diolah, pencatatan data pelayanan aktual pasien untuk dibandingkan dengan pathway, perhitungan kepatuhan terhadap pathway, analisis selisih biaya, serta penyediaan laporan indikator kinerja kunci (KPI) terkait mutu dan biaya. Proyek tidak mencakup integrasi langsung dengan sistem informasi rumah sakit (HIS/SIMRS) atau sistem BPJS Kesehatan pada tahap pertama – sistem akan berdiri sendiri (standalone). Namun, desain sistem akan dibuat fleksibel dan siap untuk integrasi di masa depan. Artinya, antarmuka (API) dan struktur data harus disiapkan sedemikian rupa agar di kemudian hari dapat diintegrasikan dengan HIS/SIMRS rumah sakit dan sistem BPJS (misalnya untuk mendapatkan data pasien atau mengirim data klaim) tanpa perlu melakukan perubahan mendasar.

## Hal-hal di luar cakupan tahap awal antara lain: fitur klinis mendalam (seperti order entry dokter atau rekam medis elektronik penuh), integrasi real-time dengan perangkat medis, serta dukungan multi-rumah sakit. Fokus utama MVP adalah memastikan fungsi KMKB berbasis pathway berjalan dengan baik di satu institusi terlebih dahulu.

## Sasaran Pengguna dan Alur Kerja Utama

Aplikasi KMKB ini akan digunakan oleh pengguna internal rumah sakit, dengan peran dan alur kerja utama sebagai berikut:

Tim Mutu Rumah Sakit

Tim Mutu (misalnya Komite Mutu atau Instalasi Manajemen Mutu) bertanggung jawab menyusun dan memelihara clinical pathway. Alur kerja utama mereka di sistem:

## Menyusun/Update Clinical Pathway: Tim Mutu mengubah clinical pathway naratif menjadi format digital terstruktur menggunakan fitur Pathway Builder. Setiap pathway disusun per diagnosa atau kondisi medis tertentu, mencakup tahap-tahap intervensi (misal pemeriksaan diagnostik, tindakan terapi, obat, lama perawatan yang dianjurkan, dll) beserta standar mutu dan target biaya pada tiap tahap.

## Sosialisasi dan Review: Setelah pathway dibuat, tim mutu bisa mencetak atau membagikan pathway ke tim klinis terkait (di luar sistem) untuk diterapkan. Mereka juga akan secara periodik mereview pathway di sistem dan memperbarui jika ada perubahan standar klinis atau kebijakan biaya.

## Monitoring Kepatuhan & Mutu: Tim Mutu menggunakan dashboard di aplikasi untuk memantau KPI seperti tingkat kepatuhan terhadap pathway, variansi tindakan, dan outcome klinis terkait mutu. Ketika terdapat kasus pasien yang penyelenggaraannya tidak sesuai pathway (misal ada penyimpangan/varian di luar alur standar), Tim Mutu akan mendapatkan informasi tersebut dan melakukan analisis lebih lanjut (misalnya melakukan clinical audit terhadap kasus tersebut di luar sistem). Hasil analisis ini dapat digunakan untuk perbaikan proses atau pembaruan pathway.

## Pelaporan: Tim Mutu menyiapkan laporan rutin (misal laporan triwulanan mutu & biaya) dengan data dari sistem, misalnya persentase kepatuhan pathway per diagnosa, rata-rata length of stay vs standar, dan tren biaya. Data ini digunakan untuk keperluan akreditasi (misal JCI) atau pelaporan ke manajemen rumah sakit.

Unit Klaim (Billing/Case Mix Team)

Unit Klaim bertugas mengurus tagihan pasien dan klaim ke BPJS Kesehatan, serta memantau aspek biaya pelayanan:

Input Data Kasus Pasien: Setelah pasien menyelesaikan perawatan (terutama pasien JKN/BPJS), petugas klaim mengumpulkan data administrasi dan klinis dari rekam medis atau billing. Data ini meliputi diagnosa, kode INA-CBG, rincian layanan/prosedur yang diberikan, biaya per item, dll. Pada tahap awal, data ini di-input manual atau di-upload dari file (misal format Excel) ke aplikasi KMKB. Setiap entri dikaitkan dengan clinical pathway yang relevan (berdasarkan diagnosa/CBG).

Perhitungan Klaim vs Biaya: Sistem akan membantu menghitung total biaya riil kasus tersebut dan membandingkannya dengan tarif INA-CBG yang berlaku untuk diagnosa itu. Petugas klaim dapat melihat apakah terjadi selisih biaya. Negative gap (biaya riil > tarif INA-CBG) menandakan potensi kerugian bagi RS, sementara positive gap (biaya riil < tarif) menandakan efisiensi atau keuntungan . Informasi ini penting bagi unit klaim untuk evaluasi finansial.

Analisis Kepatuhan Pathway: Unit Klaim bersama Tim Mutu dapat melihat di sistem apakah layanan yang diberikan ke pasien tadi sudah mengikuti pathway yang ditetapkan. Misalnya, sistem menandai jika ada tindakan di luar pathway (varian) atau jika ada langkah pathway yang dilewatkan. Petugas klaim dapat memberikan catatan (misal alasan klinis penyimpangan) jika diperlukan, sehingga data varian ini terekam.

Monitoring & Pelaporan Biaya: Unit Klaim memanfaatkan dashboard atau laporan untuk memonitor tren selisih INA-CBG vs biaya riil per diagnosa. Mereka dapat mengidentifikasi area di mana biaya sering melampaui tarif (misal pada kasus tertentu biaya obat tinggi). Informasi ini akan dilaporkan ke manajemen keuangan RS dan menjadi dasar negosiasi tarif atau upaya efisiensi internal.

Manajemen Rumah Sakit

Pihak manajemen (direksi, komite kendali mutu & biaya) tidak menggunakan sistem secara langsung untuk input, tetapi akan mengkonsumsi laporan dan dashboard. Mereka mengawasi KPI seperti rata-rata cost per case, tingkat kepatuhan clinical pathway, dan efisiensi biaya per layanan. Alur kerja manajemen lebih pada menerima insight dari sistem untuk pengambilan keputusan strategis (misal menentukan fokus peningkatan mutu, pengendalian pemborosan, atau alokasi anggaran).

Administrator IT

## Peran admin IT diperlukan untuk pengelolaan pengguna (user account & role akses) dan pemeliharaan sistem. Admin memastikan hanya pengguna berwenang (tim mutu, klaim, manajemen) yang dapat mengakses data sesuai kebutuhan mereka. Admin juga melakukan konfigurasi awal (seperti memasukkan master data rumah sakit, daftar diagnosa/INA-CBG, dan kode standar lain bila perlu). Selain itu admin memantau log aktivitas (audit trail) guna menjaga keamanan dan integritas data.



Alur Kerja Utama (Main Workflow) dalam sistem

Perancangan Pathway

## Tim Mutu mendefinisikan clinical pathway di sistem untuk setiap kondisi medis prioritas. Misal, pathway untuk “Demam Tifoid” dibuat berisi langkah-langkah: pemeriksaan lab, pemberian antibiotik A selama X hari, lama rawat inap Y hari, pemeriksaan evaluasi, dsb, lengkap dengan standar mutu (misal pemeriksaan apa yang harus dilakukan di hari 1, dll) dan estimasi komponen biaya. Pathway yang disetujui diinput ke aplikasi melalui modul Pathway Builder.

Input Data Kasus Pasien

## Ketika ada pasien rawat inap selesai dirawat untuk diagnosis yang punya pathway, petugas Unit Klaim menginput data kasus tersebut. Mereka memilih pathway yang relevan, memasukkan detail pelayanan yang diterima pasien (tindakan, pemeriksaan, obat, dsb) beserta biayanya. Jika sistem sudah memiliki fitur unggah, data ini dapat diunggah dari sistem billing rumah sakit (misal data klaim dalam format CSV).

Kalkulasi Otomatis oleh Sistem

Setelah data kasus masuk, sistem secara otomatis:

Mengukur Kepatuhan: membandingkan layanan yang diberikan dengan langkah-langkah di pathway. Sistem menandai setiap langkah pathway apakah “dilakukan sesuai rencana”, “tidak dilakukan”, atau “ada tindakan tambahan di luar pathway”. Lalu dihitung persentase kepatuhan pathway untuk kasus tersebut (misal 90% patuh jika sebagian besar langkah terpenuhi).

Menghitung Selisih Biaya: menjumlahkan total biaya riil kasus dan membandingkan dengan estimasi biaya pathway (jika ada) serta tarif INA-CBG untuk diagnosa itu. Sistem menampilkan selisih (+/-) dalam bentuk angka dan persen. Sebagai contoh, jika biaya riil Rp5 juta sementara tarif INA-CBG Rp4,5 juta, maka ada selisih +Rp500rb (11% over budget).

Review dan Tindak Lanjut

Tim Mutu dan Unit Klaim dapat melihat hasil kalkulasi tersebut di modul review. Untuk setiap kasus dengan kepatuhan rendah atau selisih biaya signifikan, mereka dapat memberi flag/komentar. Misal: “Pasien memiliki komorbid sehingga perlu tindakan X di luar pathway” – ini dicatat sebagai justifikasi varian. Data varian ini disimpan untuk analisis agregat.

Dashboard KPI

Secara real-time atau periodik, manajemen dan tim terkait melihat dashboard yang menyajikan KPI kumulatif: % kepatuhan rata-rata per pathway, tren biaya rata-rata vs INA-CBG per diagnosa, jumlah kasus yang over-budget vs under-budget, rata-rata LOS (length of stay) vs target, dsb. Dashboard ini membantu mengidentifikasi area bermasalah. Contoh, jika pathway Demam Tifoid kepatuhannya hanya 60%, itu sinyal perlu investigasi; atau jika biaya rata-rata kasus X selalu 20% di atas tarif INA-CBG, perlu upaya efisiensi.

Peningkatan Berkesinambungan

Berdasarkan insight dari dashboard dan laporan, Tim Mutu memperbarui pathway (jika perlu merevisi alur agar lebih efektif) atau melakukan training ke klinisi agar patuh pathway. Unit Klaim/Manajemen mungkin melakukan renegosiasi tarif atau audit internal. Siklus ini berulang sehingga terjadi continuous improvement dalam kendali mutu dan biaya.



Catatan: Pada tahap awal tanpa integrasi, ada langkah manual ekstra seperti input data pasien secara manual. Namun di masa depan (pasca integrasi HIS), alur akan lebih otomatis: data pasien dan tindakan bisa langsung ditarik dari rekam medis elektronik, sehingga langkah input manual diminimalkan.





## Fitur-Fitur Fungsional Utama

## Aplikasi KMKB berbasis clinical pathway ini akan memiliki sejumlah fitur utama sebagai berikut:

Pathway Builder (Penyusun Clinical Pathway)

## Fitur untuk membuat, mengedit, dan menyimpan clinical pathway dalam format terstruktur. Pengguna (Tim Mutu) dapat menambahkan tahap-tahap/step pada pathway lengkap dengan detail seperti deskripsi tindakan, pilihan prosedur/obat, kriteria mutu (misal pemeriksaan apa harus dilakukan hari ke berapa), serta parameter lain (misal kuantitas/quantity per tindakan dan estimasi biaya per tahap). Pathway Builder mendukung pembuatan branching sederhana jika ada skenario pilihan (misal opsi terapi A atau B). Setiap pathway memiliki versi/tanggal berlaku untuk mengelola perubahan. Output: repositori pathway digital yang menjadi acuan standar rumah sakit.

Manajemen Cost Reference (CRUD)

## Fitur untuk mengelola master referensi biaya (CostReference) secara penuh: daftar, tambah, edit, hapus, dan lihat detail. Field utama yang digunakan adalah service_code, service_description, dan standard_cost. Data ini menjadi referensi biaya standar yang terhubung dengan langkah pathway.

## Integrasi dengan Pathway Builder: dropdown Cost Reference pada Pathway Builder terhubung ke entitas CostReference. Saat item dipilih, standard_cost akan mengisi estimated_cost pada langkah pathway terkait untuk memudahkan estimasi biaya.

## Akses menu: tersedia di navigasi aplikasi untuk pengguna dengan role admin melalui menu “Cost References”.

Unggah Biaya Sampel & Master Tarif

## Fitur yang memungkinkan pengguna mengunggah data biaya. Terdiri dari dua sub-fitur:

Unggah Biaya Sampel Kasus:

Import data historis kasus pasien (misal file Excel berisi rincian biaya pasien per diagnosa) untuk analisis awal – membantu sistem mempelajari kisaran biaya aktual sebagai pembanding.

Master Tarif & Unit Cost:

## Database internal berisi tarif biaya komponen (misal biaya per hari rawat inap, biaya per tindakan/prosedur standar, harga obat rata-rata). Data ini bisa diimpor dari sistem billing RS atau diinput manual. Kegunaan fitur ini adalah sebagai referensi biaya standar saat menghitung estimasi biaya pathway. Dengan adanya data tarif, pathway builder dapat menampilkan estimasi total biaya pathway, dan modul analitik dapat membandingkan biaya aktual vs standar.

## Catatan: Pada MVP, fitur unggah mungkin sederhana (menggunakan template CSV), namun esensial untuk percepatan input data.

Dashboard & Laporan KPI

## Fitur dashboard interaktif menampilkan indikator kinerja kunci terkait mutu dan biaya dengan antarmuka pengguna yang modern dan responsif. Dashboard telah ditingkatkan dengan implementasi Tailwind CSS 3.x dan mendukung dark mode untuk kenyamanan pengguna dalam berbagai kondisi pencahayaan. Contoh KPI yang ditampilkan:

Kepatuhan Pathway (%): Misal persentase rata-rata kepatuhan untuk tiap pathway (per bulan). Dapat difilter per departemen atau dokter.

Selisih Biaya Rata-rata per Diagnosa: rata-rata selisih antara biaya aktual dan tarif INA-CBG untuk tiap diagnosa utama. Ditampilkan dalam nilai rupiah dan persentase.

Jumlah Kasus Over/Under Budget: jumlah kasus yang biayanya melebihi atau di bawah tarif INA-CBG, dikelompokkan per bulan atau periode.

Rata-rata Length of Stay (LOS) vs Target: perbandingan rata-rata durasi rawat aktual vs target pathway per diagnosa.

## Dashboard dirancang interaktif: pengguna bisa memilih periode (mingguan/bulanan/triwulan), filter berdasarkan diagnosa/unit/departemen, serta mengekspor data ke PDF/Excel. Visualisasi menggunakan chart (bar, line, pie) untuk mempermudah analisis tren. Misalnya, chart line untuk tren kepatuhan bulanan, atau pie chart untuk komposisi biaya. Antarmuka dashboard dioptimalkan untuk aksesibilitas dengan struktur heading yang benar dan atribut ARIA yang sesuai.

Audit Trail

## Fitur pencatatan jejak audit untuk semua aktivitas penting dalam sistem. Setiap perubahan pada data utama (misal pembuatan atau perubahan pathway, perubahan master tarif, penghapusan entri kasus) akan tersimpan dalam log dengan informasi siapa yang melakukan, kapan, dan apa yang diubah. Demikian pula setiap login user dan akses data sensitif dicatat. Audit trail ini penting untuk keamanan, pemantauan kepatuhan internal, dan persiapan jika ada audit eksternal. Misalnya, auditor (internal RS atau eksternal seperti JCI) dapat menanyakan bukti bahwa review pathway dilakukan periodik – log dapat menunjukkan timestamp update pathway dan user yang bertanggung jawab. Audit trail membantu organisasi memenuhi prinsip transparansi dan akuntabilitas dalam manajemen mutu .

Kalkulasi Kepatuhan Pathway

## Fitur backend yang menghitung secara otomatis tingkat kepatuhan setiap kasus pasien terhadap pathway-nya. Sistem akan membandingkan item layanan yang tercatat untuk pasien dengan daftar yang ada di pathway: langkah demi langkah. Hasilnya ditampilkan sebagai:

Checklist Kepatuhan: Indikator per langkah (ikon centang/silang) apakah langkah tersebut dilaksanakan.

Persentase Kepatuhan: Perbandingan jumlah langkah yang dipenuhi versus total langkah yang seharusnya, dalam persentase.

Daftar Varians: List komponen layanan yang tidak sesuai pathway – misal prosedur tambahan yang tidak ada di pathway atau langkah pathway yang dilewati.

## Logika kalkulasi ini bisa diatur agar fleksibel, misal beberapa langkah opsional tidak dihitung sebagai pelanggaran. Nilai kepatuhan ini kemudian disimpan/terkait dengan data kasus, sehingga bisa diolah di laporan agregat. Fitur ini membantu mengidentifikasi dimana deviasi pelayanan terjadi dan menjadi dasar audit klinis. Compliance semacam ini sejalan dengan pendekatan akreditasi internasional (seperti JCI) yang mendorong standarisasi proses klinis untuk menjamin perawatan aman dan bermutu tinggi .

Kalkulasi Selisih Biaya

Bersamaan dengan kalkulasi kepatuhan, sistem juga melakukan kalkulasi selisih biaya untuk tiap kasus:

## Vs Estimasi Pathway: Bila di pathway telah ditentukan estimasi biaya (berdasarkan unit cost standar), sistem menghitung total estimasi tersebut dan membandingkan dengan biaya aktual dari data billing. Selisih = Biaya aktual – Estimasi. Ini menunjukkan apakah kasus tersebut over budget atau under budget dibanding asumsi semula.

Vs Tarif INA-CBG: Sistem juga menampilkan selisih terhadap tarif INA-CBG (untuk pasien JKN) dengan formula: Selisih = Biaya aktual – Tarif INA-CBG. Nilai negatif berarti biaya rumah sakit lebih tinggi dari yang diganti BPJS (indikasi kerugian rumah sakit), nilai positif berarti biaya di bawah tarif (RS untung dari kasus ini) . Benchmark INA-CBG digunakan karena memang sistem JKN Indonesia memakai skema tarif paket INA-CBG sebagai basis pembayaran .

## Komponen Biaya: Fitur ini juga dapat merinci kontribusi masing-masing komponen biaya (misal obat, bahan habis, jasa dokter, dll) terhadap selisih. Dengan begitu, pengguna dapat melihat faktor penyebab overbudget. Contoh: Ternyata komponen obat anti-biotik melebihi estimasi karena menggunakan obat non-formulary.

Hasil kalkulasi selisih biaya disimpan per kasus dan ditampilkan di dashboard agregat. Ini mendukung target KMKB yakni mengidentifikasi peluang efisiensi biaya tanpa mengurangi kualitas.



Mekanisme Rekonsiliasi Pathway dan Billing

## Sistem menyediakan fitur untuk membandingkan setiap langkah clinical pathway dengan data billing rumah sakit.

## Mapping Otomatis & Manual: setiap item billing dipetakan ke layanan standar (canonical service). Pengguna bisa melengkapi mapping bila ada item baru.

Checklist Kepatuhan: pathway step ditandai done/missed/over/varian sesuai realisasi billing.

Simulator Perbandingan: tampilan sisi kiri pathway, sisi kanan billing, dengan highlight warna untuk status kepatuhan.

Laporan Deviasi & Biaya: sistem menghasilkan persentase kepatuhan, daftar varian, serta selisih biaya pathway vs billing.

## Selain fitur utama di atas, ada juga fitur pendukung lainnya yang akan disertakan:

## Manajemen Pengguna & Hak Akses: Modul untuk mengatur pengguna sistem, peran/role (Admin, Tim Mutu, Tim Klaim, Manajemen [hanya view]) serta pengaturan akses sesuai prinsip least privilege.

### Superadmin (Global)
- Peran khusus di level sistem untuk mengelola multi-tenant (multi-RS) dan pengguna lintas RS.
- Kewenangan utama:
  - CRUD master tenant (Hospital): nama, kode, logo, tema/branding, status aktif.
  - Kelola pengguna lintas RS: membuat/mengubah/nonaktifkan user pada RS mana pun.
  - Akses lintas tenant untuk tujuan audit/operasional (tidak terbatasi `hospital_id`).
- Catatan keamanan dan tata kelola:
  - Akun sangat terbatas (1–2 akun), dilindungi MFA dan password policy kuat.
  - Semua aktivitas superadmin wajib terekam di audit log.
  - Tidak digunakan untuk operasional harian; hanya provisioning, investigasi, dan maintenance.

### Catatan Teknis Superadmin
- Model `User` memiliki role `superadmin`. Akun superadmin tidak terikat RS (kolom `users.hospital_id` bisa `NULL` khusus superadmin).
- Global scope tenant (`BelongsToHospital`) tidak diterapkan kepada superadmin sehingga query tidak otomatis difilter `hospital_id`.
- Route model binding (contoh `ClinicalPathway`, `PatientCase`) mengizinkan superadmin mengakses data lintas RS.
- Middleware penetapan tenant (`SetHospital`) tidak menyetel `session('hospital_id')` untuk superadmin.
- Cache/storage per tenant tetap terstruktur per `hospital_id`; superadmin tidak memiliki folder tenant sendiri. Aksi tulis data oleh superadmin harus memilih RS target secara eksplisit.

### Akun Default (untuk pengujian/dev)
- Dibuat melalui seeder: `superadmin@example.com` dengan kata sandi awal `password` (WAJIB diganti di produksi).

### Modul Manajemen Rumah Sakit (Tenant) untuk Superadmin
- Fitur CRUD penuh untuk entitas `Hospital` (nama, kode, logo, warna tema, alamat, kontak, status aktif) melalui `HospitalController` dan Blade views `index/create/edit/show` berbasiskan Tailwind.
- Route resource hanya dapat diakses superadmin dan diposisikan sebelum grup route yang terikat tenant agar tidak terkena global scope.
- Upload logo tersimpan di storage publik sesuai struktur per-tenant.

### Pengelolaan User Lintas-Tenant oleh Superadmin
- Superadmin dapat melihat seluruh pengguna lintas `hospital_id` pada halaman index users (kolom “Hospital” tampil khusus superadmin).
- Form create/edit user menampilkan dropdown pemilihan `hospital` hanya untuk superadmin sehingga user dapat diprovisikan ke RS manapun.
- Validasi memastikan hanya superadmin yang boleh mengatur/ubah `hospital_id`; non-superadmin dibatasi pada tenant aktif.

### Routing & Middleware
- Routes modul `hospitals` dilindungi `auth` + role superadmin. Routes users tetap seperti semula, namun controller mengakomodasi akses superadmin lintas-tenant.
- Middleware penetapan tenant tetap aktif untuk user biasa; superadmin dilewati dari penyetelan `session('hospital_id')` sehingga tidak terscope otomatis.

### Keamanan & Audit
- Semua aksi superadmin (CRUD hospital, pengelolaan user lintas-tenant) tercatat di `AuditLog` untuk keperluan jejak audit.
- Pembatasan akses ketat pada role superadmin, disarankan MFA dan hardening password policy.

## Master Data Klinik: Seperti master data diagnosa (ICD-10), master prosedur (ICD-9 CM atau kode internasional lain), dan master unit/ruangan. Ini diperlukan untuk standarisasi input data kasus dan integrasi ke depannya.

## Notifikasi & Reminder: (Opsional di MVP) Sistem bisa mengirim notifikasi internal jika, misal, ada pathway yang belum diupdate >1 tahun, atau alert ke user jika kepatuhan pathway di bawah threshold tertentu. Fitur ini membantu proaktif dalam kendali mutu.





## Struktur Data Utama dan Sketsa Entitas Relasional

## Struktur data aplikasi dirancang berbasis relasional (RDBMS) dengan entitas utama sebagai berikut:

ClinicalPathway

merepresentasikan satu clinical pathway untuk suatu kondisi/diagnosa. Atribut kunci: ID Pathway, Nama/Deskripsi (misal “CP Demam Tifoid Dewasa”), Kode diagnosa terkait (ICD-10 atau kode internal), Versi/Tanggal berlaku, DibuatOleh (user penyusun), Status (aktif/arsip). Relasi: Satu ClinicalPathway memiliki banyak PathwayStep. Juga, satu ClinicalPathway dapat dikaitkan dengan banyak PatientCase (kasus pasien yang seharusnya mengikuti pathway tersebut).

PathwayStep

merekam langkah/tahapan dalam sebuah pathway. Atribut kunci: ID Step, ID Pathway (FK ke ClinicalPathway), Urutan (sequence number tahap), Jenis Tindakan (misal pemeriksaan lab, terapi, konsultasi, dll), Deskripsi langkah (contoh: “Pemeriksaan Laboratorium Widal pada hari ke-1”), Kriteria Mutu (jika ada, misal “hasil lab harus diperoleh <24 jam”), Estimasi Biaya (boleh null jika tak relevan per langkah), Opsional/Required (flag jika langkah opsional). Relasi: PathwayStep dimiliki oleh satu ClinicalPathway. Urutan menentukan alur. Bisa juga ditambahkan relasi self-referencing untuk menandai step lanjutan atau bercabang (misal NextStepID jika linear flow, tapi untuk kesederhanaan MVP linear saja).

PatientCase

mewakili satu kasus perawatan pasien (episode perawatan) yang akan dievaluasi. Atribut kunci: ID Case, No Rekam Medis atau ID Pasien (untuk referensi, dapat disimpan anonimisasi jika ada isu privasi), Tanggal Masuk & Keluar, Diagnosa Utama (ICD-10), Kode INA-CBG (jika pasien JKN), ID Pathway (FK ke ClinicalPathway yang seharusnya digunakan sesuai diagnosa), Total Biaya Riil, Tarif INA-CBG (jika ada), Persentase Kepatuhan (dihitung sistem), Selisih vs INA-CBG, Tanggal Input, InputOleh (user). Relasi: PatientCase berelasi dengan ClinicalPathway (banyak kasus bisa pakai pathway yang sama). Juga memiliki banyak CaseDetail/CaseStep yang memetakan rincian layanan.

CaseStep/CaseDetail

rincian tindakan/layanan yang diberikan pada pasien dalam satu PatientCase. Ini digunakan untuk mengukur kepatuhan per langkah. Atribut kunci: ID CaseDetail, ID Case (FK ke PatientCase), ID Step (opsional FK ke PathwayStep jika tindakan sesuai salah satu langkah pathway; boleh null jika tindakan di luar pathway), Deskripsi Tindakan (misal “Pemeriksaan Widal”), Kode Tindakan/Obat (bila ada, misal kode billing), Biaya Tindakan. Relasi: Banyak CaseDetail terkait ke satu PatientCase. Jika CaseDetail terkait ke PathwayStep, artinya tindakan itu memenuhi step tsb; jika ada PathwayStep yang tak punya CaseDetail terkait (mangkir), artinya langkah pathway terlewat; jika ada CaseDetail tanpa pasangan PathwayStep (FK null), itu dianggap varian (tindakan di luar pathway). Relasi ini memungkinkan analisis kepatuhan otomatis.

CostReference

## referensi biaya (master tarif). Atribut: ID CostRef, Kode Layanan/Item, Deskripsi Item, Biaya Standar, Satuan, Sumber (misal “harga rata-rata 2024”). Data ini digunakan PathwayStep estimasi biaya dan juga untuk menyusun laporan varians biaya. Relasi tidak langsung; PathwayStep bisa menyimpan ID CostRef untuk mengambil estimasi biaya.

User

data pengguna sistem. Atribut: UserID, Nama, Unit/Departemen, Role (admin/mutu/klaim/manajemen), Username, Password (terenkripsi), etc. Relasi: merekam role untuk menentukan hak akses.

AuditLog

log aktivitas. Atribut: LogID, Timestamp, UserID, JenisAktivitas (CREATE/UPDATE/DELETE/LOGIN/etc), Entitas (table/objek yang diubah), Detail (misal “update pathway X versi Y”, atau “user login success”). Relasi: terhubung ke User untuk identitas pelaku.



Diagram ERD sederhana untuk entitas di atas kira-kira: ClinicalPathway –< PathwayStep; ClinicalPathway –< PatientCase; PatientCase –< CaseDetail –> (opsional) PathwayStep. Entitas User, CostReference, AuditLog berdiri sendiri terhubung sesuai kegunaan (User ke AuditLog, CostReference ke PathwayStep via foreign key optional).



## Struktur data di atas memastikan bahwa satu pathway dapat diaplikasikan ke banyak kasus pasien dan evaluasi detail per langkah bisa dilakukan. Dengan struktur relasional ini, query untuk mendapatkan tingkat kepatuhan, rata-rata biaya, dsb, dapat dijalankan dengan efektif. Misalnya, menghitung persentase kepatuhan pathway: sistem cukup mengambil jumlah PathwayStep per pathway dan berapa yang terpenuhi di CaseDetail per kasus.

Catatan Implementasi Terkini (Agustus 2025)

## Pathway Builder: perbaikan pemetaan field agar konsisten dengan skema database. Field yang digunakan: step_order (urutan), action (aksi/tindakan), description (deskripsi), estimated_cost (estimasi biaya), quantity (kuantitas). Dropdown Cost Reference mengisi estimated_cost secara otomatis saat dipilih. Total cost ditampilkan sebagai atribut terhitung (estimated_cost x quantity) dan tidak disimpan di database.

## Cost Reference Management: penambahan modul CRUD penuh (routes resource, controller, dan Blade views index/create/edit/show) untuk mengelola referensi biaya. Tersedia pagination dan validasi form.

## Navigasi: menu “Cost References” ditambahkan pada navigasi (desktop dan responsif) khusus untuk role admin.

## User Seeder: disediakan seeder UsersTableSeeder untuk membuat akun default per role (admin, mutu, klaim, manajemen) guna memudahkan pengujian. Password default: "password" (harap ganti di produksi).

## Pathway Steps Drag-and-Drop Ordering (Agustus 2025)

- **Fitur**: Pathway Builder mendukung drag-and-drop untuk mengubah urutan tampilan langkah (row) dengan handle ikon "☰".
- **Perilaku Day (step_order)**: Nilai "Day" tidak berubah otomatis saat drag. Pengguna tetap dapat mengedit Day secara manual pada kolomnya masing-masing.
- **Persistensi Urutan**: Urutan visual disimpan pada kolom baru `display_order` di tabel `pathway_steps`. Tampilan builder mengurutkan langkah berdasarkan `display_order`.
- **Perubahan Skema**: Ditambahkan kolom `display_order` (integer, nullable, indexed) dan dilakukan backfill awal agar nilainya = `step_order` untuk data eksisting.
- **API Reorder**: Endpoint `POST pathways/{pathway}/steps/reorder` menerima payload `{"order": [{"id": <step_id>, "position": <urutan_baru>}, ...]}` dan menyimpan ke `display_order` (transaksional). Tidak mengubah `step_order`.
- **Frontend**: Menggunakan SortableJS (1.15.2). CSRF dan header JSON sudah ditangani. Drag handle hanya mengubah urutan visual; kolom Day tidak di-update otomatis.
- **Model**: `PathwayStep` menambahkan atribut `display_order` pada `$fillable` dan `$casts`.

## Bulk Import Pathway Steps via Excel (Agustus 2025)

- **Fitur**: Pathway Builder mendukung unduh template Excel (.xlsx) dan unggah file Excel/CSV untuk membuat langkah pathway secara massal.
- **Template**: Kolom yang disediakan: `day` (wajib), `activity` (wajib), `description` (wajib), `criteria` (opsional), `standard_cost` (wajib), `quantity` (opsional, default 1), `cost_reference_id` (opsional). Template menyertakan contoh baris untuk panduan.
- **Validasi Server-side** (`app/Http/Controllers/PathwayStepController.php`):
  - `day` integer ≥ 1
  - `activity`, `description` wajib terisi
  - `standard_cost` numerik (mendukung format desimal umum). Jika menggunakan format lokal, parser dapat disesuaikan.
  - `quantity` integer ≥ 1 (default 1)
  - `cost_reference_id` (jika diisi) harus valid dan ada pada master CostReference
  - Baris tidak valid dilewati; sistem menampilkan ringkasan baris yang gagal. Baris valid tetap diimpor (partial success).
- **Inisialisasi Urutan**: Untuk entri hasil impor baru, `step_order` dan `display_order` diinisialisasi sama dengan nilai `day` untuk memudahkan penyusunan awal. Pengguna dapat mengatur ulang urutan tampilan melalui fitur drag-and-drop (menulis ke `display_order`).
- **Dependensi**: `phpoffice/phpspreadsheet` (untuk buat/baca Excel) dan ekstensi Zip PHP harus aktif. Jika library belum tersedia, tombol unduh otomatis mengunduh template CSV dan unggah Excel akan ditolak dengan pesan ramah; unggah CSV tetap didukung.
- **Endpoint**:
  - `GET /pathways/{pathway}/steps/template` — unduh template (Excel, fallback CSV)
  - `POST /pathways/{pathway}/steps/import` — unggah Excel/CSV untuk impor langkah
- **UI** (`resources/views/pathways/builder.blade.php`): Tombol “Download Excel Template” dan input berkas menerima `.xlsx, .xls, .csv`. Teks instruksi diperbarui agar pengguna mengisi template dan mengunggah kembali.

Pemetaan Canonical Service dan Aturan Rekonsiliasi

Untuk menjembatani pathway (istilah klinis) dan billing (kode tarif), sistem menggunakan lapisan canonical service dan aturan mapping.

## Struktur Data Tambahan

## canonical_services: daftar layanan standar (id, nama, kategori, satuan).

service_map: aturan pemetaan kode_billing ↔ canonical_service_id (match type, nilai, prioritas).

step_matches: hasil evaluasi per kasus, mencatat pathway step, item billing terkait, status (done/missed/varian), qty dan biaya realisasi.
Alur Rekonsiliasi

Data billing → normalisasi → mapping ke canonical service.

Pathway step dicocokkan dengan hasil mapping berdasarkan hari/episode.

Sistem menilai kepatuhan dan mencatat deviasi.

Hasil disimpan untuk rekap per kasus & laporan agregat.



## Dengan tambahan ini, BRD Anda akan menjelaskan “apa yang dilihat user” (fitur fungsional) sekaligus “bagaimana sistem bekerja di belakang layar” (struktur data & teknis).



## Standar Mutu/Biaya/Klinis yang Dituju



## Pengembangan sistem ini memperhatikan rujukan beberapa standar nasional maupun internasional terkait mutu pelayanan, efisiensi biaya, dan praktik klinis, antara lain:

ISO 7101:2023 – Healthcare Organization Management – Quality

## ISO 7101 adalah standar sistem manajemen pertama untuk mutu di organisasi pelayanan kesehatan. Standar ini menetapkan persyaratan untuk pendekatan sistematis dalam penyelenggaraan layanan kesehatan yang berkelanjutan dan berkualitas tinggi . Dengan mengacu ISO 7101, sistem KMKB dirancang untuk mendukung prinsip PDCA (Plan-Do-Check-Act) dalam manajemen mutu: Pathway sebagai rencana mutu, implementasi pelayanan sebagai do, monitoring kepatuhan & biaya sebagai check, dan update/improvement pathway sebagai act. Penerapan audit trail, KPI dashboard, dan continuous improvement sejalan dengan semangat ISO 7101 dalam menciptakan layanan kesehatan yang efektif, efisien, aman, berpusat pada pasien, serta berkelanjutan.

## Standar Akreditasi JCI (Joint Commission International)

## JCI merupakan standar akreditasi rumah sakit bertaraf internasional yang berfokus pada peningkatan mutu dan keselamatan pasien. JCI mengembangkan standar berbasis bukti yang esensial untuk memastikan penyelenggaraan perawatan yang aman dan berkualitas tinggi, serta membantu organisasi mengukur dan meningkatkan kinerja . Beberapa elemen JCI yang relevan dengan proyek ini: Medication Management and Use, Quality Improvement and Patient Safety (QPS), Assessment of Patients, dll, yang semuanya terkait dengan adanya alur klinis terstandar dan evaluasi berkala. Aplikasi KMKB akan membantu rumah sakit memenuhi standar QPS JCI, misalnya melalui clinical pathway compliance (rekomendasi JCI bahwa proses perawatan harus terstandarisasi untuk meminimalkan risiko) dan performance improvement (menggunakan data varians biaya & mutu untuk proyek peningkatan). Selain itu, audit trail mendukung kepatuhan terhadap standar manajemen informasi JCI yang mengharuskan rekaman dokumentasi setiap perubahan relevan.

INA-CBG (Indonesian Case Base Groups) – Tarif Klaim JKN

INA-CBG adalah sistem tarif paket yang diterapkan oleh BPJS Kesehatan (Jaminan Kesehatan Nasional) sebagai metode pembayaran prospektif untuk pelayanan kesehatan di Indonesia . Setiap diagnosa/kasus dikelompokkan ke dalam sebuah group dengan tarif baku tertentu. Tantangan yang ada adalah sering terjadi perbedaan antara biaya riil rumah sakit dengan tarif INA-CBG, yang apabila biaya riil lebih tinggi, rumah sakit menanggung selisih (kerugian) . Oleh sebab itu, kontrol biaya melalui KMKB menjadi krusial. Aplikasi ini mengacu pada INA-CBG sebagai benchmark biaya: setiap pathway akan mempertimbangkan batas tarif INA-CBG agar layanan klinis disusun seefisien mungkin tanpa melebihi tarif. Laporan selisih biaya vs INA-CBG yang dihasilkan sistem membantu manajemen memastikan kinerja keuangan tetap sehat. Selain itu, sistem akan disiapkan untuk kemudahan integrasi dengan aplikasi grouper INA-CBG di masa depan, misal ekspor data kasus dalam format yang kompatibel untuk perhitungan klaim.

## Standar/Regulasi Kementerian Kesehatan RI terkait Mutu & Biaya:

## Di tingkat nasional, Kemenkes telah mengeluarkan regulasi yang mendorong kendali mutu dan biaya. Contohnya Permenkes No. 30/2019 tentang Pedoman Penyusunan dan Penerapan Clinical Pathway (hipotetis, sebagai ilustrasi) atau standar akreditasi KARS (Komisi Akreditasi RS) yang juga mewajibkan alur klinis untuk diagnosa utama. Sistem KMKB akan mematuhi panduan tersebut, misalnya memastikan setiap diagnosa prioritas memiliki clinical pathway tertulis dan terdokumentasi digital, serta ada evaluasi outcome-nya. Standar lainnya seperti Clinical Practice Guidelines perhimpunan klinis juga bisa dijadikan rujukan konten pathway.

Prinsip Health Technology dan Keamanan Informasi:

## Selain standar mutu di atas, pengembangan teknis sistem mengikuti best practice TI seperti OWASP untuk keamanan aplikasi web (karena aplikasi akan menyimpan data sensitif pasien, meski pseudonim, dan data keuangan RS). Kepatuhan terhadap ISO 27001 (standar manajemen keamanan informasi) dijadikan referensi dalam hal kebijakan akses dan enkripsi data penting. Juga, standar HL7/FHIR dipantau sebagai acuan integrasi data klinis jika di masa depan menghubungkan sistem ini dengan EMR/HIS.



## Dengan mengacu ke berbagai standar di atas, diharapkan aplikasi ini tidak hanya memenuhi kebutuhan internal, tapi juga memperkuat posisi rumah sakit dalam akreditasi dan compliance. Sebagai contoh nyata, peningkatan kepatuhan terhadap clinical pathway akan berkontribusi pada berkurangnya gap negatif INA-CBG serta peningkatan keselamatan pasien, dua hal yang diukur baik oleh regulator nasional maupun akreditasi internasional.



## Komponen Teknis

## Pengembangan aplikasi web KMKB ini akan dilakukan dengan mempertimbangkan teknologi yang sesuai dengan ekosistem IT rumah sakit (yang umumnya berbasis PHP) serta kemudahan maintenance dan scalability. Berikut komponen teknis yang direkomendasikan:

Platform & Stack Teknologi

## Aplikasi akan dibangun berbasis PHP stack. Disarankan menggunakan framework PHP modern seperti Laravel (versi terbaru yang LTS) untuk mempercepat development dengan arsitektur MVC yang rapi. Laravel menyediakan fitur bawaan untuk otentikasi, migrasi database, dan keamanan yang akan bermanfaat. Alternatif lain bisa berupa CodeIgniter 4 atau Symfony, namun Laravel dipilih karena ekosistemnya kuat dan banyak library pendukung. Aplikasi akan berjalan di atas web server Apache atau Nginx (bagian dari stack LAMP/LEMP – Linux, Apache/Nginx, MySQL/MariaDB, PHP). Sistem operasi server bisa Linux (Ubuntu LTS atau CentOS) agar stabil dan hemat biaya. Database MySQL/MariaDB direkomendasikan untuk kemudahan integrasi dengan PHP; PostgreSQL juga bisa dipertimbangkan untuk peningkatan performa dan compliance ACID. Semua komponen stack tersebut adalah open-source, sesuai dengan kemungkinan keterbatasan anggaran dan untuk kemudahan kustomisasi.

Untuk frontend, aplikasi menggunakan Tailwind CSS 3.x sebagai framework CSS utility-first yang memungkinkan pengembangan UI yang konsisten dan responsif. Tailwind CSS dikombinasikan dengan Alpine.js untuk interaktivitas frontend yang ringan. Pendekatan ini menggantikan Bootstrap 5 dan jQuery yang digunakan sebelumnya, memberikan fleksibilitas yang lebih besar dalam desain UI/UX serta performa yang lebih baik.

Arsitektur Aplikasi & Rancangan Modul

Aplikasi akan dibangun dengan arsitektur modular agar mudah dikembangkan lebih lanjut dan diintegrasikan. Rancangan modul meliputi:

## Modul Manajemen Pathway: berisi fitur Pathway Builder, penyimpanan pathway dan langkah-langkahnya, versi, dll. Modul ini diakses Tim Mutu.

Modul Data Kasus & Biaya: menangani input/unggah data kasus pasien dan biaya. Termasuk form entri kasus, upload CSV, validasi data sesuai master (diagnosa, kode layanan), serta kalkulasi otomatis kepatuhan & selisih biaya ketika data disimpan.

Modul Analitik & Laporan: mencakup logika perhitungan KPI, agregasi data, dan penyajian dashboard. Modul ini juga menghasilkan laporan (PDF/Excel) sesuai template.

Modul User Management & Security: untuk otentikasi (login, logout, reset password), otorisasi (middleware akses berdasarkan role), manajemen akun pengguna, serta audit trail logging.

Modul Audit Trail: meskipun terintegrasi di modul lain (misal logging di setiap modul), dapat dianggap modul tersendiri untuk penanganan penyimpanan log dan antarmuka monitoring log bagi admin.

Modul Integration/API (future): modul yang disiapkan (meski mungkin belum aktif di MVP) untuk integrasi data. Misal endpoint API untuk menerima data dari HIS, atau modul untuk ekspor data klaim ke format XML INA-CBG grouper. Arsitektur sistem sebaiknya RESTful, dimana modul-modul saling berkomunikasi via service layer API internal. Ini akan memudahkan saat nanti membuka API ke sistem eksternal.



Tiap modul di atas akan dikemas sedemikian rupa sehingga bisa dikembangkan atau diperbaiki tanpa mempengaruhi modul lain (low coupling, high cohesion). Contohnya, modul Pathway bisa di-deploy update sendiri jika ada perubahan logika pathway, asalkan contract dengan modul lain (misal modul kasus) tetap sama.

Desain Basis Data

Database relasional (MySQL/MariaDB) dengan desain sesuai entitas pada bagian sebelumnya. Dibuat dengan normalisasi secukupnya: data master terpisah, data transaksi kasus terpisah. Indexing dioptimalkan untuk query utama (misal index di kolom ID Pathway pada tabel kasus untuk analisis per pathway). Juga disiapkan beberapa materialized view atau table agregat untuk keperluan dashboard agar load cepat (misal tabel rekapan bulanan kepatuhan per pathway). Migrasi skema akan dikelola via tool (Laravel migration) agar perubahan skema tercatat dan reproducible.

## Multi-tenant (Single DB, Row-based, Tanpa Subdomain)

Untuk memungkinkan satu instance aplikasi melayani banyak rumah sakit, arsitektur disiapkan sejak awal sebagai multi-tenant berbasis satu database (row-based tenancy) tanpa menggunakan subdomain. Pemisahan tenant dilakukan melalui kolom `hospital_id` pada tabel-tabel domain, dengan cakupan berikut:

- Entitas tenant: tabel `hospitals` menyimpan identitas RS (id, name, code, logo_path, theme_color, address, contact, is_active).
- Kolom `hospital_id`: ditambahkan pada tabel utama seperti `users`, `clinical_pathways`, `pathway_steps`, `patient_cases`, `case_details`, `cost_references`, dan `audit_logs` agar setiap baris selalu terkait ke satu RS.
- Indeks & FK: `hospital_id` diindeks dan menjadi foreign key ke `hospitals.id` untuk menjaga integritas dan kinerja query.
- Global scope model: seluruh model domain (mis. `ClinicalPathway`, `PathwayStep`, `PatientCase`, `CaseDetail`, `CostReference`, `AuditLog`) menerapkan Global Scope berbasis `hospital_id` sehingga semua query otomatis terfilter sesuai tenant aktif. Disediakan mekanisme sementara (tanpa subdomain) untuk menentukan tenant dari user yang sedang login.
- Resolusi tenant: karena tidak menggunakan subdomain, tenant aktif diambil dari `auth()->user()->hospital_id` pasca login melalui middleware `SetHospital` dan helper `hospital()` yang dapat diakses view/service.
- Konfigurasi per-tenant (sederhana): hanya branding dan identitas, seperti nama RS, logo, dan warna tema. Branding dipakai pada layout dan laporan.
- Storage & cache: prefiks folder/file dan key cache menggunakan `hospital_id` (mis. `storage/app/public/tenants/{hospital_id}/branding/logo.png`) untuk menghindari tabrakan nama file lintas tenant.
- Import/Export Excel: sesuai preferensi, sistem mendukung unduh template Excel dan unggah Excel/CSV (PhpSpreadsheet). Pada saat impor/ekspor, data selalu ditandai/diambil dengan `hospital_id` tenant aktif sehingga tidak terjadi kebocoran data lintas RS.
- Keamanan & isolasi: route model binding dan policy memastikan akses antar-tenant terblokir (404/forbidden) meskipun ada upaya mengakses ID milik tenant lain.

Catatan: Meskipun pilot operasional dapat dimulai dengan 1 RS, rancangan ini siap multi-tenant sejak hari pertama. Karena sistem baru dibangun dari nol, tidak diperlukan proses backfill `hospital_id` pada data eksisting.

Keamanan Aplikasi

Keamanan menjadi perhatian utama karena data yang diolah bersifat sensitif (data pasien, data kinerja). Langkah-langkah keamanan:

Authentication & Authorization: Menggunakan mekanisme login berbasis session yang aman, dengan password hashing (bcrypt/argon2). Multi-level akses diterapkan (misal admin vs user biasa memiliki menu berbeda).

Input Validation: Semua input pengguna diverifikasi (server-side validation) untuk mencegah SQL injection, XSS, dan kesalahan data. Framework Laravel sudah menyediakan ORM (Eloquent) yang secara default mencegah SQL injection.

CSRF Protection: Form-form penting dilengkapi token CSRF untuk mencegah cross-site request forgery.

## Audit Trail & Monitoring: Setiap akses data penting dan perubahan data dicatat (seperti dijelaskan di fitur Audit Trail). Log ini dapat disimpan di tabel database khusus atau file log terenskripsi.

Encryption: Data sangat sensitif (misal informasi pasien yang teridentifikasi, meski mungkin di BRD ini data pasien hanya level agregat atau pseudonim) dapat dienkripsi di database. Minimal, koneksi ke aplikasi menggunakan HTTPS (SSL) terutama jika diakses via jaringan publik.

Backup & Recovery: Disiapkan prosedur backup database rutin serta mekanisme recovery untuk mencegah kehilangan data.

Pengetesan Keamanan: Sebelum rilis, dilakukan uji penetrasi internal atau menggunakan tool otomatis (seperti OWASP ZAP) untuk menemukan celah umum.

## Compliance Regulasi: Memastikan aplikasi mematuhi regulasi lokal tentang data kesehatan (semisal PerMenkominfo tentang data pribadi, dll). Juga, menyelaraskan praktik dengan standar internasional (HIPAA atau ISO 27799 untuk keamanan data kesehatan) jika relevan, meskipun implementasi disederhanakan untuk lingkungan lokal tertutup.

Extensibility (Kemudahan Dikembangkan Lanjut)

## Sistem dirancang dengan mempertimbangkan perubahan dan penambahan fitur di masa depan:

API-Ready: Sejak awal, logika aplikasi bisa diakses via lapisan API. Misal, perhitungan kepatuhan bisa dibuat sebagai fungsi terpisah yang bisa diekspos. Rencana jangka menengah adalah menyediakan REST API atau web service agar modul ini bisa terhubung dengan HIS/EMR.

Modular & Plugin Architecture: Jika memungkinkan, beberapa komponen dibuat seperti plugin. Misal, modul integrasi BPJS (grouper) bisa ditambahkan belakangan tanpa mengubah core system.

## Scalability: Walaupun saat ini untuk 1 RS, sistem dipersiapkan agar bisa menangani skala data yang lebih besar. Misal, jumlah pathway bisa puluhan, data kasus mungkin ribuan per tahun. Query dan coding dioptimalkan (penting memastikan N+1 query problem dihindari). Jika di masa depan multi-RS, struktur database bisa diadaptasi (misal menambah kode RS di tabel, dsb., atau menggunakan basis data terpisah per RS).

## Documentation & Code Quality: Kode telah di-dokumentasi dengan baik (comments, README developer) agar dev lain mudah mengambil alih. Mengikuti standar PSR untuk PHP coding style. Codebase telah ditingkatkan dengan refactor dari Bootstrap 5 ke Tailwind CSS 3.x untuk meningkatkan konsistensi tampilan dan maintainability. Struktur kode dioptimalkan dengan peningkatan struktur HTML semantik dan peningkatan aksesibilitas. Dengan codebase yang rapi dan terdokumentasi dengan baik, penambahan fitur baru akan lebih mudah dan pengembangan selanjutnya dapat dilakukan dengan efisien.

Lingkungan Deployment

Untuk fase awal, aplikasi kemungkinan di-deploy di server lokal rumah sakit (on-premise) mengingat data sensitif. Bisa disiapkan di VM/Linux server dengan stack LAMP/LEMP. Jika ke depan diperbolehkan cloud, bisa migrasi ke cloud hosting (pastikan cloud compliance untuk data kesehatan). Environment dev, staging, production sebaiknya dipisah untuk pengujian. CI/CD pipeline sederhana bisa diterapkan untuk mempercepat update (opsional).



## Roadmap Pengembangan (MVP dan Tahapan Rilis)



## Pengembangan aplikasi KMKB ini direncanakan bertahap untuk mencapai Minimum Viable Product (MVP) terlebih dahulu, kemudian dilanjutkan dengan iterasi penyempurnaan dan integrasi. Berikut roadmap awal beserta lingkup tiap tahapan rilis:

Tahap 1: MVP (Minimum Viable Product) – Estimasi waktu: ±3-4 bulan.

## Lingkup: Membangun fitur inti yang diperlukan untuk menjalankan fungsi KMKB dasar secara mandiri. Pada tahap ini fokus pada functional correctness daripada kelengkapan semua fitur. Komponen yang akan disertakan:

Pathway Builder sederhana: dapat input langkah-langkah pathway (belum mendukung branching kompleks, namun cukup untuk linear pathway).

Data Pasien & Biaya Input: form manual untuk memasukkan data kasus dan biaya per kasus. Template unggah CSV untuk data kasus mungkin disertakan untuk efisiensi (jika sempat).

Kalkulasi Kepatuhan & Selisih: otomatis menghitung persentase kepatuhan pathway per kasus, dan selisih biaya vs INA-CBG.

Dashboard Basic: menampilkan minimal 3 KPI: rata-rata kepatuhan per pathway, jumlah kasus dan persentase yang over/under budget, rata-rata selisih biaya per diagnosa. Dashboard tahap MVP bisa statis sederhana atau berupa kumpulan tabel/grafik dasar.

## Laporan: satu jenis laporan standar (PDF) misal laporan ringkasan bulanan KMKB.

User Management & Keamanan dasar: login multi-role (admin, mutu, klaim), session management, form validation, dan audit trail logging dasar (login, create/update pathway, input kasus).

Tujuan MVP: Sistem dapat digunakan oleh Tim Mutu dan Unit Klaim untuk satu atau dua pilot clinical pathway terlebih dahulu. Misal, uji coba pada 1 diagnosa (seperti Demam Tifoid atau Sectio Caesarea) dengan memasukkan data 10 kasus riil, lalu melihat hasil dashboard. Output MVP akan divalidasi user (UAT) dan mendapat feedback.

## Tahap 2: Peningkatan Fitur & Stabilitas – Estimasi waktu: ±2-3 bulan setelah MVP.

## Lingkup: Menyempurnakan fitur berdasarkan umpan balik MVP dan menambah komponen yang belum sempat pada MVP:

Penyempurnaan Pathway Builder: mendukung duplikasi pathway, versi pathway (bila update bisa simpan versi lama untuk audit), mungkin mendukung conditional step sederhana (jika perlu).

Peningkatan UI/UX: Refactor seluruh tampilan dari Bootstrap 5 ke Tailwind CSS 3.x dengan implementasi dark mode menyeluruh, peningkatan struktur HTML semantik, peningkatan aksesibilitas dengan atribut ARIA dan struktur heading yang benar, serta implementasi kelas tombol yang dapat digunakan kembali untuk konsistensi tampilan. Selain itu, penambahan grafik interaktif di dashboard, navigasi user yang lebih intuitif, dan filter/pencarian yang lebih lengkap (misal search pathway, filter kasus per periode).

## Fitur Unggah Data lebih lengkap: mendukung unggahan batch data kasus (dari file Excel) agar Unit Klaim bisa import data banyak sekaligus. Juga modul master tarif yang bisa diunggah dari data billing.

## Laporan tambahan: misal laporan detail varians per kasus, atau laporan untuk keperluan akreditasi (format disesuaikan standar KARS/JCI).

Hardening Security: review keamanan pasca MVP, perbaikan bug, optimasi performa query (agar sistem tetap responsif dengan data bertambah).

## Tujuan Tahap 2: Sistem siap digunakan secara lebih luas di rumah sakit (bisa memasukkan banyak pathway dan seluruh kasus untuk pathway tersebut). Pada akhir tahap ini, sebaiknya sistem sudah cukup stabil dan user merasa fitur-fitur kunci telah terpenuhi.

Tahap 3: Integrasi Dasar dengan Sistem Lain – Estimasi waktu: ±3-6 bulan.

Lingkup: Mulai menghubungkan aplikasi KMKB dengan sistem eksisting untuk mengurangi input manual dan meningkatkan akurasi data:

Integrasi dengan SIMRS/HIS: membangun skrip ETL atau API untuk menarik data pasien, diagnosa, layanan, dan biaya langsung dari database SIMRS rumah sakit. Misalnya, ketika pasien pulang dan tagihan final, data otomatis masuk ke KMKB (mungkin via penjadwalan harian). Jika API SIMRS belum ada, bisa lewat query langsung ke database (dengan izin).

Integrasi dengan INA-CBG Grouper: jika memungkinkan, sistem bisa menyiapkan file XML klaim untuk diimpor ke grouper BPJS, atau sebaliknya menarik tarif INA-CBG terbaru dari sistem e-claim BPJS. Langkah ini memastikan tarif selalu update dan menghitung selisih lebih akurat.

Single Sign-On (SSO): jika RS punya Active Directory atau mekanisme SSO, diintegrasikan agar user yang sudah login di domain RS bisa akses aplikasi tanpa kredensial terpisah (meningkatkan adopsi).

## Modul notifikasi: integrasi email internal atau WhatsApp Gateway (bila ada) untuk mengirim notifikasi ke petugas bila ada kasus outlier (bisa difiturkan jika dianggap bermanfaat).

Tujuan Tahap 3: Mengurangi human error dan beban kerja manual. Aplikasi mulai menjadi bagian ekosistem IT RS. Data realtime makin terjamin, sehingga output KPI lebih dipercaya.

## Tahap 4: Pengayaan Fitur Lanjutan – Estimasi waktu: ±3 bulan.

## Lingkup: Menambahkan fitur lanjutan sesuai kebutuhan lanjutan KMKB dan insight baru:

Analitik Lanjutan: Misal penambahan modul predictive sederhana: memprediksi kemungkinan overbudget berdasarkan pola data (untuk pencegahan), atau analisis korelasi kepatuhan dengan outcome (butuh input data outcome seperti re-admisi).

## Benchmarking & Multi-unit: Jika RS besar dengan banyak unit, bisa tambahkan fitur perbandingan antar departemen atau antar RS (bila group hospital). Meskipun awalnya 1 RS, modul ini disiapkan jika manajemen ingin melihat perbandingan antar cabang di masa depan.

Mobile Access: Aplikasi telah dirancang dengan pendekatan mobile-first menggunakan Tailwind CSS 3.x, memastikan antarmuka pengguna yang responsif dan optimal di berbagai perangkat, termasuk desktop, tablet, dan mobile. Desain responsif memungkinkan manajemen untuk mengakses dashboard KPI dan fitur penting lainnya dengan baik saat on-the-go. Dengan implementasi desain responsif yang komprehensif, kebutuhan akan aplikasi mobile terpisah dapat diminimalkan sementara pengalaman pengguna tetap optimal di semua perangkat.

## Feedback Loop ke Klinik: Fitur untuk dokter atau kepala departemen melihat data mereka (misal dokter bedah bisa lihat kepatuhan pathway bedahnya). Ini semacam membuka akses terbatas ke lebih banyak user klinis sebagai end-user data. Harus disertai kontrol hak akses yang ketat.

## Tujuan Tahap 4: Memastikan sistem KMKB benar-benar menjadi alat manajemen strategis di RS, tidak hanya administrasi belakang layar. Fitur lanjutan ini bersifat opsional tergantung prioritas rumah sakit dan dampak yang diinginkan.

Tahap 5: Scale Up dan Maintenance Berkelanjutan –

## Setelah fitur lengkap, roadmap dilanjutkan dengan fase maintenance dan continuous improvement: memperbaiki bug yang muncul, melakukan upgrade teknologi (misal upgrade versi PHP, patch keamanan), menyesuaikan jika ada perubahan regulasi (misal skema INA-CBG baru), dan training berkala pengguna baru. Jika ada rencana implementasi di rumah sakit lain (roll-out multi-site), maka perlu penyesuaian konfigurasi agar satu instance bisa multi-RS atau menyiapkan multi-instance. Dokumentasi dan transfer knowledge ke tim IT RS menjadi bagian penting agar sistem dapat dikelola jangka panjang.



## Roadmap di atas fleksibel menyesuaikan kapasitas tim pengembang dan respon pengguna. Pendekatan yang dipakai adalah agile incremental – tiap tahap menghasilkan output yang dapat digunakan dan dievaluasi oleh user, lalu perbaikan/fitur baru ditambahkan di iterasi berikutnya. Dengan demikian, risiko kegagalan diminimalkan dan sistem KMKB dapat segera memberikan manfaat nyata (misal tahap MVP sudah bisa menemukan insight selisih biaya) lalu ditingkatkan akurasinya seiring waktu.



## Kesimpulan

## Dokumen BRD ini memaparkan kebutuhan bisnis untuk aplikasi KMKB berbasis clinical pathway. Intinya, rumah sakit ingin memiliki alat digital untuk memastikan pelayanan mengikuti standar mutu klinis dan biaya terkendali, yang pada gilirannya meningkatkan efisiensi operasional dan kualitas perawatan pasien. Proyek ini berakar pada prinsip bahwa clinical pathways terbukti membantu pengambilan keputusan klinis yang tepat dan cost-effective , serta mampu menekan perbedaan biaya riil dengan tarif pembayar . Dengan dukungan manajemen dan kolaborasi lintas unit (mutu, klaim, IT, klinis), implementasi aplikasi ini diharapkan sukses dan berkontribusi pada budaya quality improvement berkelanjutan di rumah sakit. Semua persyaratan dan rencana yang tertuang akan menjadi acuan tim pengembang dan pemangku kepentingan dalam merealisasikan sistem KMKB sesuai kebutuhan.

