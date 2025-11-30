# Struktur Chart of Account (COA) Rumah Sakit

## Struktur Kode

- `xx xxx xx xxx` : Chart of Account (lengkap)
  - `xx` : Kode lokasi
  - `xxx` : Main account
  - `xx` : Kode departemen (bagian)
  - `xxx` : Sub / detail

## Rentang Kelompok Akun

- `100–199` : Aktiva
- `200–299` : Pasiva
- `300–399` : Modal / Laba Ditahan
- `400–499` : Penjualan / Pendapatan Layanan
- `500–599` : Harga Pokok Penjualan / Layanan
- `600–699` : Pendapatan Lain-Lain
- `700–799` : Biaya Lain-Lain

## Kode Tambahan

- `01–09` : Sub akun neraca
- `10–19` : Departemen Layanan Medis
- `20–29` : Departemen Penunjang Medis
- `30–39` : Departemen Penunjang Non Medis
- `40–59` : Departemen Administrasi
- `001–999` : Detail / keterangan

---

# 1. AKTIVA

## 1.1 AKTIVA LANCAR

### Kas / Bank

- `11` : Kas / Bank
- `111` : Kas / Bank
- `111 01` : Kas
- `111 01 001`: Kas Besar
- `111 02` : Bank
- `111 02 001`: Bank Mandiri
- `111 02 002`: BNI
- `111 02 003`: BCA

### Surat Berharga

- `112` : Surat Berharga
- `112 01` : Deposito
- `112 01 001` : Deposito Bank Mandiri
- `112 01 002` : Deposito BNI
- `112 01 003` : Deposito BCA
- `112 02` : Saham
- `112 02 001` : Saham PT.
- `112 02 002` : Saham PT.
- `112 02 003` : Saham PT.
- `112 03` : Obligasi
- `112 03 001` : Obligasi PT.
- `112 03 002` : Obligasi PT.
- `112 03 003` : Obligasi PT.

### Piutang Usaha

- `113` : Piutang Usaha
- `113 01` : Piutang Perorangan
- `113 01 001` : Piutang Perorangan
- `113 01 002` : Piutang Karyawan
- `113 02` : Piutang Perusahaan Asuransi
- `113 02 001` : Piutang BPJS Kesehatan
- `113 02 002` : Piutang PT.
- `113 02 003` : Piutang PT.
- `113 03` : Piutang Perusahaan
- `113 03 001` : Piutang PT.
- `113 03 002` : Piutang PT.
- `113 03 003` : Piutang PT.
- `113 04` : Piutang Ragu-ragu
- `113 04 001` : Piutang Ragu-ragu

### Persediaan

- `114` : Persediaan

**Persediaan Obat**

- `114 01` : Persediaan Obat
- `114 01 001` : Persediaan Obat Apotik Rawat Jalan Utama
- `114 01 002` : Persediaan Obat Apotik Rawat Jalan Umum & BPJS
- `114 01 003` : Persediaan Obat Farmasi Rawat Inap Utama
- `114 01 004` : Persediaan Obat Farmasi Rawat Inap Umum & BPJS

**Persediaan Alat Kesehatan**

- `114 02` : Persediaan Alat Kesehatan
- `114 02 001` : Persediaan Alkes Apotik Rawat Jalan Utama
- `114 02 002` : Persediaan Alkes Apotik Rawat Jalan Umum & BPJS
- `114 02 003` : Persediaan Alkes Farmasi Rawat Inap Utama
- `114 02 004` : Persediaan Alkes Farmasi Rawat Inap Umum & BPJS
- `114 02 005` : Persediaan Gas Medis
- `114 02 006` : Persediaan Reagen Laboratorium
- `114 02 007` : Persediaan Reagen Radiologi

**Persediaan Logistik Umum**

- `114 03` : Persediaan Logistik Umum
- `114 03 001` : Persediaan Alat Tulis Kantor (ATK)
- `114 03 002` : Persediaan Barang Rumah Tangga
- `114 03 003` : Persediaan Linen
- `114 03 004` : Persediaan Elektrikal
- `114 03 005` : Persediaan Computer Supplies

**Persediaan Gizi**

- `114 04` : Persediaan Gizi
- `114 04 001` : Persediaan Bahan Basah
- `114 04 002` : Persediaan Bahan Kering
- `114 04 003` : Persediaan Tabung Gas Elpiji

### Biaya Dibayar Dimuka

- `115` : Biaya Dibayar Dimuka

**Pajak Penghasilan**

- `115 01` : Pajak Penghasilan
- `115 01 001` : PPh Pasal 21
- `115 01 002` : PPh Pasal 23
- `115 01 003` : PPh Pasal 25

**PPN**

- `115 02` : Pajak Pertambahan Nilai (PPN)
- `115 02 001` : PPN Masukan

**Asuransi Dibayar Dimuka**

- `115 03` : Asuransi Dibayar Dimuka
- `115 03 001` : Asuransi Gedung
- `115 03 002` : Asuransi Peralatan Medis
- `115 03 003` : Asuransi Kendaraan

**Uang Muka Pembelian Barang**

- `115 04` : Uang Muka Pembelian Barang
- `115 04 001` : Uang Muka Pembelian Barang

## 1.2 AKTIVA TETAP

### Aktiva Tetap

- `12` : Aktiva Tetap
- `121` : Aktiva Tetap

**Aktiva Tetap Berwujud**

- `121 01` : Aktiva Tetap Berwujud
- `121 01 001` : Aktiva Tetap Berwujud Golongan I
- `121 01 002` : Aktiva Tetap Berwujud Golongan II
- `121 01 003` : Aktiva Tetap Berwujud Golongan III
- `121 01 004` : Aktiva Tetap Berwujud Golongan Bangunan
- `121 01 005` : Land & Right

**Aktiva Tetap Tak Berwujud**

- `121 02` : Aktiva Tetap Tak Berwujud
- `121 02 001` : Hak Pengelolaan Usaha
- `121 02 002` : Goodwill

**Aktiva Tetap Lainnya**

- `121 03` : Aktiva Tetap Lainnya
- `121 03 001` : Biaya Pra Operasi

### Cadangan Penyusutan

- `122` : Cadangan Penyusutan
- `122 01` : Cadangan Penyusutan Aktiva Tetap
- `122 01 001` : Cad. Penyusutan Aktiva Tetap Gol. I
- `122 01 002` : Cad. Penyusutan Aktiva Tetap Gol. II
- `122 01 003` : Cad. Penyusutan Aktiva Tetap Gol. III
- `122 01 004` : Cad. Penyusutan Aktiva Tetap Gol. Bangunan

### Suspend Account

- `19` : Suspend Account
- `199` : Suspend Account
- `199 01` : Suspend Account
- `199 01 001` : Suspend Account

---

# 2. PASIVA

## 2.1 KEWAJIBAN LANCAR

- `21` : Kewajiban Lancar
- `211` : Kewajiban Lancar

**Utang kepada Supplier**

- `211 01` : Utang kepada Supplier
- `211 01 001` : Utang kepada PT.
- `211 01 002` : Utang kepada PT.
- `211 01 003` : Utang kepada PT.

**Utang Jasa Layanan Medis**

- `211 02` : Utang Jasa Layanan Medis
- `211 02 001` : Utang Jasa Medis kepada …
- `211 02 002` : Utang Jasa Medis kepada …
- `211 02 003` : Utang Jasa Medis kepada …

**Utang Pajak**

- `211 03` : Utang Pajak
- `211 03 001` : PPh Pasal 21 Jasa Medis
- `211 03 002` : PPh Pasal 21 Karyawan
- `211 03 003` : PPh Pasal 25
- `211 03 004` : Pajak Pertambahan Nilai (PPN)
- `211 03 005` : Pajak Bumi & Bangunan (PBB)
- `211 03 006` : Pajak Pembangunan I
- `211 03 007` : Retribusi Air, Listrik, Sampah, dll

**Iuran BPJS**

- `211 04` : Iuran BPJS
- `211 04 001` : Iuran JPK BPJS Kesehatan
- `211 04 002` : Iuran JKK BPJS Ketenagakerjaan
- `211 04 003` : Iuran JKM BPJS Ketenagakerjaan
- `211 04 004` : Iuran JHT BPJS Ketenagakerjaan
- `211 04 005` : Iuran JDP BPJS Ketenagakerjaan

**Utang Lancar Lainnya**

- `211 05` : Utang Lancar Lainnya
- `211 05 001` : Deposit Pasien
- `211 05 002` : Titipan Koperasi Karyawan
- `211 05 003` : Tagihan PLN
- `211 05 004` : Tagihan PDAM
- `211 05 005` : Tagihan TELKOM
- `211 05 006` : Titipan Gaji Karyawan
- `211 05 007` : Biaya yang Masih Harus Dibayar

## 2.2 KEWAJIBAN JANGKA PANJANG

- `212` : Kewajiban Jangka Panjang

**Angsuran Pinjaman Bank**

- `212 01` : Angsuran Pinjaman Bank
- `212 01 001` : Angsuran Pinjaman Bank
- `212 01 002` : Angsuran Pinjaman Bank

**Angsuran Pinjaman Leasing (LKBB)**

- `212 02` : Angsuran Pinjaman LKBB (Leasing)
- `212 02 001` : Angsuran Pinjaman Leasing PT.
- `212 02 002` : Angsuran Pinjaman Leasing PT.

**Kerjasama Operasi (KSO)**

- `212 03` : Kerjasama Operasi
- `212 03 001` : Angsuran Pinjaman KSO PT.
- `212 03 002` : Angsuran Pinjaman KSO PT.

## 2.3 Intercompany / Affiliated Company

- `213` : Intercompany / Affiliated Company Account
- `213 01` : Intercompany Account
- `213 01 001` : Intercompany Account
- `213 02` : Affiliated Company Account
- `213 02 001` : Affiliated Company Account

---

# 3. MODAL

- `3` : Modal
- `31` : Modal Saham
- `311` : Modal Saham

**Saham atas Nama**

- `311 01` : Saham Atas Nama
- `311 01 001` : Saham Atas Nama
- `311 01 002` : Saham Atas Nama PT.

**Saham Biasa**

- `311 02` : Saham Biasa
- `311 02 001` : Saham Biasa Atas Nama
- `311 02 002` : Saham Biasa Atas Nama

**Donasi**

- `311 03` : Donasi
- `311 03 001` : Bantuan Pemerintah
- `311 03 002` : Bantuan Swasta

**Laba Ditahan**

- `311 04` : Laba Ditahan
- `311 04 001` : Laba Ditahan Tahun Lalu
- `311 04 002` : Laba Ditahan Tahun Berjalan

---

# 4. PENDAPATAN

## 4.1 Pendapatan Layanan Medis

- `4` : Pendapatan Layanan & Penunjang Medis
- `41` : Pendapatan Layanan Medis
- `411` : Pendapatan Layanan Medis

**Rawat Jalan**

- `411 11` : Pendapatan Rawat Jalan
- `411 11 001` : Poli Spesialis Utama
- `411 11 002` : Poli Spesialis Umum & BPJS

**Rawat Darurat**

- `411 12` : Pendapatan Rawat Darurat
- `411 12 001` : Instalasi Rawat Darurat
- `411 12 002` : Instalasi Rawat Darurat Trauma Center

**Rawat Inap**

- `411 13` : Pendapatan Rawat Inap
- `411 13 001` : Pendapatan Rawat Inap VVIP
- `411 13 002` : Pendapatan Rawat Inap VIP
- `411 13 003` : Pendapatan Rawat Inap Utama
- `411 13 004` : Pendapatan Rawat Inap Kelas I
- `411 13 005` : Pendapatan Rawat Inap Kelas II
- `411 13 006` : Pendapatan Rawat Inap Kelas III
- `411 13 007` : Pendapatan Rawat Inap Kelas I BPJS
- `411 13 008` : Pendapatan Rawat Inap Kelas II BPJS
- `411 13 009` : Pendapatan Rawat Inap Kelas III BPJS
- `411 13 010` : Pendapatan Rawat Inap ICU Kelas Utama
- `411 13 011` : Pendapatan Rawat Inap ICU Kelas Umum
- `411 13 012` : Pendapatan Rawat Inap ICU Kelas BPJS

## 4.2 Pendapatan Penunjang Medis

- `412` : Pendapatan Penunjang Medis

**Penjualan Obat & Alkes**

- `412 21` : Penjualan Obat dan Alat Kesehatan
- `412 21 001` : Obat Apotik Poli Spesialis Utama
- `412 21 002` : Obat Apotik Poli Spesialis Umum & BPJS
- `412 21 003` : Obat Depo Farmasi Rawat Darurat
- `412 21 004` : Obat Depo Farmasi Rawat Inap Utama
- `412 21 005` : Obat Depo Farmasi Rawat Inap Umum & BPJS
- `412 21 011` : Alkes Apotik Poli Spesialis Utama
- `412 21 012` : Alkes Apotik Poli Spesialis Umum & BPJS
- `412 21 013` : Alkes Depo Farmasi Rawat Darurat
- `412 21 014` : Alkes Depo Farmasi Rawat Inap Utama
- `412 21 015` : Alkes Depo Farmasi Rawat Inap Umum & BPJS
- `412 21 021` : BHP Apotik Poli Spesialis Utama
- `412 21 022` : BHP Apotik Poli Spesialis Umum & BPJS
- `412 21 023` : BHP Depo Farmasi Rawat Darurat
- `412 21 024` : BHP Depo Farmasi Rawat Inap Utama
- `412 21 025` : BHP Depo Farmasi Rawat Inap Umum & BPJS

**Laboratorium**

- `412 22` : Pendapatan Laboratorium
- `412 22 001` : Pemeriksaan Laboratorium Ringan
- `412 22 002` : Pemeriksaan Laboratorium Sedang
- `412 22 003` : Pemeriksaan Laboratorium Lengkap

**Radiologi**

- `412 23` : Pendapatan Radiologi
- `412 23 001` : Photo Rontgent Film Kecil
- `412 23 002` : Photo Rontgent Film Sedang
- `412 23 003` : Photo Rontgent Film Besar
- `412 23 004` : Photo Rontgent Gigi (Panoramic)
- `412 23 005` : MRCT (CT Scan)

**Ruang OK**

- `412 24` : Pendapatan Ruang OK
- `412 24 001` : Pemakaian Kamar OK 1
- `412 24 002` : Pemakaian Kamar OK 2
- `412 24 003` : Pemakaian Kamar OK 3
- `412 24 004` : Pemakaian Kamar OK 4
- `412 24 005` : Pemakaian Kamar OK 5
- `412 24 006` : Pemakaian Kamar OK 6
- `412 24 007` : Jasa Operator OK Ringan
- `412 24 008` : Jasa Operator OK Sedang
- `412 24 009` : Jasa Operator OK Berat
- `412 24 010` : Jasa Anestesia (30% Operator)
- `412 24 011` : Jasa Penata Anestesia (10% Operator)
- `412 24 012` : Jasa Penata Bedah (10% Operator)

**Gizi**

- `412 25` : Pendapatan Gizi
- `412 25 001` : Layanan Gizi Pasien VVIP
- `412 25 002` : Layanan Gizi Pasien VIP
- `412 25 003` : Layanan Gizi Pasien Utama
- `412 25 004` : Layanan Gizi Pasien Kelas I
- `412 25 005` : Layanan Gizi Pasien Kelas II
- `412 25 006` : Layanan Gizi Pasien Kelas III
- `412 25 007` : Layanan Gizi Pasien Kelas I BPJS
- `412 25 008` : Layanan Gizi Pasien Kelas II BPJS
- `412 25 009` : Layanan Gizi Pasien Kelas III BPJS
- `412 25 010` : Layanan Gizi Pasien ICU Utama
- `412 25 011` : Layanan Gizi Pasien ICU Umum
- `412 25 012` : Layanan Gizi Pasien ICU BPJS

**Medical Check Up**

- `412 26` : Medical Check Up
- `412 26 001` : Medical Check Up Dasar
- `412 26 002` : Medical Check Up Sedang
- `412 26 003` : Medical Check Up Lengkap
- `412 26 004` : Medical Check Up On Location

## 4.3 Pendapatan Penunjang Non Medis

- `413` : Pendapatan Penunjang Non Medis
- `413 xx 001` : Sewa Ambulan Pasien
- `413 xx 002` : Jasa Pengolahan Limbah Padat Infeksius
- `413 xx 003` : Pendapatan Sewa Gedung / ATM Center / BTS
- `413 xx 004` : Pendapatan Lain-Lain

---

# 5. HPP & BIAYA

## 5.1 HPP Layanan Medis

- `5` : HPP Layanan & Penunjang Medis
- `51` : HPP Layanan Medis
- `511` : HPP Layanan Medis

**HPP Rawat Jalan**

- `511 11` : HPP Rawat Jalan
- `511 11 001` : Jasa Dokter Poli Spesialis Utama
- `511 11 002` : Jasa Dokter Poli Spesialis Umum & BPJS
- `511 11 003` : Jasa Perawat Poli Spesialis

**HPP Rawat Darurat**

- `511 12` : HPP Rawat Darurat
- `511 12 003` : Jasa Dokter Instalasi Rawat Darurat dan Trauma Center
- `511 12 004` : Jasa Perawat

**Jasa Visitasi Rawat Inap**

- `511 12` : Jasa Visitasi Dokter Rawat Inap
- `511 12 001` : Visitasi Rawat Inap VVIP
- `511 12 002` : Visitasi Rawat Inap VIP
- `511 12 003` : Visitasi Rawat Inap Utama
- `511 12 004` : Visitasi Rawat Inap Kelas I
- `511 12 005` : Visitasi Rawat Inap Kelas II
- `511 12 006` : Visitasi Rawat Inap Kelas III
- `511 12 007` : Visitasi Rawat Inap Kelas I BPJS
- `511 12 008` : Visitasi Rawat Inap Kelas II BPJS
- `511 12 009` : Visitasi Rawat Inap Kelas III BPJS
- `511 12 010` : Visitasi Rawat Inap ICU Utama
- `511 12 011` : Visitasi Rawat Inap ICU Umum
- `511 12 012` : Visitasi Rawat Inap ICU BPJS

**Jasa Perawat Rawat Inap**

- `511 12 021` : Jasa Perawat Ruang Rawat Inap VVIP
- `511 12 022` : Jasa Perawat Ruang Rawat Inap VIP
- `511 12 023` : Jasa Perawat Ruang Rawat Inap Utama
- `511 12 024` : Jasa Perawat Ruang Rawat Inap Kelas I
- `511 12 025` : Jasa Perawat Ruang Rawat Inap Kelas II
- `511 12 026` : Jasa Perawat Ruang Rawat Inap Kelas III
- `511 12 027` : Jasa Perawat Ruang Rawat Inap Kelas I BPJS
- `511 12 028` : Jasa Perawat Ruang Rawat Inap Kelas II BPJS
- `511 12 029` : Jasa Perawat Ruang Rawat Inap Kelas III BPJS
- `511 12 030` : Jasa Perawat Ruang Rawat Inap ICU Utama
- `511 12 031` : Jasa Perawat Ruang Rawat Inap ICU Umum
- `511 12 032` : Jasa Perawat Ruang Rawat Inap ICU BPJS

## 5.2 HPP Penunjang Medis

- `512` : HPP Penunjang Medis

**HPP Obat, Alkes dan BHP**

- `512 21` : HPP Obat, Alkes dan Bahan Habis Pakai
- `512 21 001` : HPP Obat Poli Spesialis Utama
- `512 21 002` : HPP Obat Poli Spesialis Umum & BPJS
- `512 21 003` : HPP Obat Depo Farmasi Rawat Darurat
- `512 21 004` : HPP Obat Depo Farmasi Rawat Inap Utama
- `512 21 005` : HPP Obat Depo Farmasi Rawat Inap Umum & BPJS
- `512 21 011` : HPP Alkes Poli Spesialis Utama
- `512 21 012` : HPP Alkes Poli Spesialis Umum & BPJS
- `512 21 013` : HPP Alkes Depo Farmasi Rawat Darurat
- `512 21 014` : HPP Alkes Depo Farmasi Rawat Inap Utama
- `512 21 015` : HPP Alkes Depo Farmasi Rawat Inap Umum & BPJS
- `512 21 021` : HPP BHP Poli Spesialis Utama
- `512 21 022` : HPP BHP Poli Spesialis Umum & BPJS
- `512 21 023` : HPP BHP Depo Farmasi Rawat Darurat
- `512 21 024` : HPP BHP Depo Farmasi Rawat Inap Utama
- `512 21 025` : HPP BHP Depo Farmasi Rawat Inap Umum & BPJS
- `512 21 026` : Jasa Apoteker dan Asisten Apoteker

**Laboratorium**

- `512 22` : HPP Laboratorium
- `512 22 001` : HPP Penggunaan Reagen Laboratorium
- `512 22 002` : Jasa Analis Laboratorium

**Radiologi**

- `512 23` : HPP Radiologi
- `512 23 001` : HPP Reagen Film Radiologi
- `512 23 002` : Tunjangan Bahaya Radiasi (TBR)

**Ruang OK**

- `512 24` : HPP Ruang OK
- `512 24 007` : Jasa Operator OK Ringan
- `512 24 008` : Jasa Operator OK Sedang
- `512 24 009` : Jasa Operator OK Berat
- `512 24 010` : Jasa Anestesia (30% Operator)
- `512 24 011` : Jasa Penata Anestesia (10% Operator)
- `512 24 012` : Jasa Penata Bedah (10% Operator)

**Gizi**

- `512 25` : HPP Gizi

**Medical Check Up**

- `512 26` : HPP Medical Check Up
- `512 26 001` : Jasa Pemeriksaan Dokter
- `512 26 002` : Jasa Perawat
- `512 26 003` : Biaya Administrasi

## 5.3 Biaya Administrasi Umum – Gaji & Kesejahteraan

- `52` : Biaya Administrasi Umum

**Gaji dan Upah**

- `521` : Gaji dan Upah
- `521 xx 001` : Gaji Karyawan
- `521 xx 002` : Lembur
- `521 xx 003` : Tunjangan Tetap
- `521 xx 004` : Tunjangan Transport
- `521 xx 005` : THR

**Kesejahteraan Karyawan**

- `522` : Kesejahteraan Karyawan
- `522 xx 001` : Iuran BPJS Kesehatan
- `522 xx 002` : Iuran JKK, JKM BPJS Ketenagakerjaan
- `522 xx 003` : Rekreasi dan Olah Raga
- `522 xx 004` : Biaya Training / Pelatihan
- `522 xx 005` : Bantuan Pengobatan
- `522 xx 006` : Sumbangan kepada Karyawan, Reward Kerja
- `522 xx 007` : Beasiswa dan Tugas Belajar
- `522 xx 008` : Biaya Kesejahteraan Lain-Lain

## 5.4 Biaya Lainnya (Perjalanan, Perlengkapan, Umum)

**Perjalanan Dinas**

- `523` : Biaya Perjalanan Dinas Bagian
- `523 xx 001` : Biaya Transportasi Perjalanan Dinas Dalam Negeri
- `523 xx 002` : Biaya Akomodasi Perjalanan Dinas Dalam Negeri
- `523 xx 003` : UPD Dalam Negeri
- `523 xx 004` : Biaya Transportasi Perjalanan Dinas Luar Negeri
- `523 xx 005` : Biaya Akomodasi Perjalanan Dinas Luar Negeri
- `523 xx 006` : UPD Luar Negeri

**Perlengkapan Kerja**

- `524` : Biaya Perlengkapan Kerja Bagian
- `524 xx 001` : Alat Tulis Kantor
- `524 xx 002` : Barang Cetakan
- `524 xx 003` : Computer Supplies
- `524 xx 004` : Seragam Kerja
- `524 xx 005` : Biaya Meeting & Convention
- `524 xx 006` : Perlengkapan Kerja Lain-Lain

## 5.5 Biaya Umum

**Perawatan & Perbaikan**

- `53` : Biaya Umum
- `531` : Biaya Perawatan dan Perbaikan Bagian
- `531 xx 001` : Biaya Perawatan Rutin Aktiva Tetap Gol. I & II
- `531 xx 002` : Biaya Perbaikan Aktiva Tetap Golongan I & II
- `531 xx 003` : Biaya Perawatan Rutin Bangunan
- `531 xx 004` : Biaya Perbaikan Bangunan

**BBM & Elpiji**

- `532` : Bahan Bakar Minyak dan Elpiji Bagian
- `532 xx 001` : BBM Kendaraan
- `532 xx 002` : BBM Genset
- `532 xx 003` : Gas Elpiji

**Biaya-biaya Umum**

- `533` : Biaya-Biaya Umum Bagian
- `533 xx 001` : Biaya Pemakaian Listrik PLN
- `533 xx 002` : Biaya Pemakaian Air PDAM
- `533 xx 003` : Biaya Rekening Telepon dan Internet
- `533 xx 004` : Biaya Kebersihan Lingkungan
- `533 xx 005` : Iuran Keanggotaan Organisasi
- `533 xx 006` : Biaya Kerugian Piutang
- `533 xx 007` : Beban Biaya Sewa Alat
- `533 xx 008` : Biaya Umum Lainnya

**Promosi & Penjualan**

- `534` : Biaya Promosi dan Penjualan Bagian
- `534 xx 001` : Biaya Promosi
- `534 xx 002` : Biaya Pengiriman Barang & Dokumen
- `534 xx 003` : Biaya Penggunaan Meterai
- `534 xx 004` : Biaya Pemasaran
- `534 xx 005` : Biaya Promosi Lainnya

**Pajak & Retribusi**

- `535` : Pajak dan Retribusi Bagian
- `535 xx 001` : PPh Pasal 25 Rampung
- `535 xx 002` : PPN Masukan
- `535 xx 003` : Pajak Bumi dan Bangunan
- `535 xx 004` : Pajak Pembangunan I
- `535 xx 005` : Biaya Perijinan
- `535 xx 006` : Retribusi Penerangan Jalan
- `535 xx 007` : Retribusi Sampah
- `535 xx 008` : Pajak dan Retribusi Lainnya

**Asuransi & Dana Pensiun**

- `536` : Asuransi dan Dana Pensiun Bagian
- `536 xx 001` : Asuransi Kendaraan
- `536 xx 002` : Asuransi Kebakaran dan Gempa Bumi
- `536 xx 003` : Asuransi Kecelakaan Diri (Personal Accident)
- `536 xx 004` : Asuransi Kesehatan
- `536 xx 005` : JHT 3,7% BPJS Ketenagakerjaan
- `536 xx 006` : JPK 4,0% BPJS Kesehatan

**Jasa Profesional**

- `537` : Jasa Profesional Bagian
- `537 xx 001` : Jasa Legal
- `537 xx 002` : Jasa Pendampingan / Jasa Manajemen
- `537 xx 003` : Jasa Audit
- `537 xx 004` : Jasa Appraisal
- `537 xx 005` : Jasa Outsourcing

**Donasi & Sumbangan**

- `538` : Donasi dan Sumbangan Bagian
- `538 xx 001` : Donasi kepada Pihak III
- `538 xx 002` : Sumbangan kepada Pihak III
- `538 xx 003` : Representatif
- `538 xx 004` : CSR

## 5.6 Depresiasi & Amortisasi

- `54` : Depresiasi
- `541` : Depresiasi Bagian
- `541 xx 001` : Depresiasi Aktiva Tetap Golongan I
- `541 xx 002` : Depresiasi Aktiva Tetap Golongan II
- `541 xx 003` : Depresiasi Aktiva Tetap Golongan III
- `541 xx 004` : Depresiasi Aktiva Tetap Golongan Bangunan

- `542` : Amortisasi Biaya Bagian
- `542 xx 001` : Biaya Pengelolaan Usaha
- `542 xx 002` : Amortisasi Biaya Kerjasama Operasi

## 5.7 Pembebanan dari Departemen Penunjang

- `599` : Pembebanan dari Dept Penunjang Bagian
- `599 xx 001` : Pembebanan dari Sub Dept

---

# 6. PENDAPATAN & BIAYA DI LUAR USAHA

- `6` : Pendapatan dan Biaya di Luar Usaha Bagian
- `61` : Pendapatan di Luar Usaha
- `611` : Pendapatan di Luar Usaha

**Pendapatan di Luar Usaha**

- `611 xx 001` : Pendapatan Jasa Giro
- `611 xx 002` : Pendapatan Bunga Deposito
- `611 xx 003` : Keuntungan Penjualan Surat Berharga
- `611 xx 004` : Keuntungan Penjualan Aktiva Tetap
- `611 xx 005` : Pendapatan Lain-Lain

**Biaya di Luar Usaha**

- `612` : Biaya di Luar Usaha
- `612 xx 001` : Beban Bunga Jasa Giro dan Deposito
- `612 xx 002` : Beban Bunga Pinjaman
- `612 xx 003` : Kerugian Penjualan Surat Berharga
- `612 xx 004` : Kerugian Penjualan Aktiva Tetap
- `612 xx 005` : Beban Biaya Lain-Lain
