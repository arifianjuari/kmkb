# PRD – Dashboard KMKB (Multi-Tab “Satu Layar = Satu Cerita”)

_Teknologi: Laravel (Blade) + Alpine.js + Library Grafik (disarankan: Chart.js / ApexCharts / ECharts)_

---

## 1. Ringkasan Singkat

Produk: **Dashboard KMKB** (Kendali Mutu Kendali Biaya) berbasis clinical pathway, dengan konsep:

> **Satu layar = satu cerita**  
> 1 tab dashboard menjawab 1 pertanyaan manajerial besar.

Dashboard ini dipakai pimpinan RS untuk:

- Memantau **mutu klinis & kepatuhan pathway**
- Mengendalikan **biaya, unit cost, dan selisih terhadap tarif / INA-CBG**
- Melihat **risiko finansial** dan prioritas perbaikan
- Memastikan **proses costing & data** up to date

**Stack front-end:**

- View: **Laravel Blade**
- Interaktivitas: **Alpine.js**
- Grafik: **Chart.js / ApexCharts / ECharts** (bebas pilih satu, PRD akan menyebut “library grafik” generik)

---

## 2. Tujuan Produk

1. Menyediakan **dashboard eksekutif** yang ringkas namun kaya insight, spesifik untuk KMKB.
2. Memecah informasi menjadi **5 tab utama**, sehingga setiap tab fokus ke 1 “cerita” / pertanyaan.
3. Mendukung siklus **PDCA (Plan–Do–Check–Act)**:
   - Plan : Clinical pathway, costing, tarif internal
   - Do : Implementasi pelayanan
   - Check : Dashboard kepatuhan, biaya, varians, risiko
   - Act : Identifikasi area perbaikan prioritas

---

## 3. Ruang Lingkup

**Dalam scope v1:**

- 1 route utama Laravel: contoh `/dashboard` (atau `/kmkb/dashboard`), berisi:
  - Tab 1: **Overview**
  - Tab 2: **Biaya & Tarif**
  - Tab 3: **Pathway & Mutu**
  - Tab 4: **Case Variance & JKN Risk**
  - Tab 5: **Data & Proses Costing**
- Global filter:
  - Periode (month/year atau date range sederhana)
  - Payer type (JKN / Non-JKN / All)
  - (opsional) Kelas perawatan (VIP, I, II, III)
- Widget interaktif: tiles, charts, tables dengan sorting dasar dan klik untuk drill-down.
- Role-based visibility tab (berdasar middleware / Gate Laravel).

**Di luar scope v1 (boleh sebagai future):**

- Meng-trigger import GL / run allocation langsung dari dashboard (v1 hanya **view status**).
- Advanced analytics (forecasting, machine learning).
- Custom dashboard per user.

---

## 4. Target User & Kebutuhan

### 4.1. Direktur RS / Direktur Utama

- Pertanyaan utama: **“RS saya merah atau hijau dari sisi mutu & biaya?”**
- Ingin tampilan cepat: tiles KPI + lampu warna.

### 4.2. Direktur Keuangan / Manajer Keuangan

- Fokus: **biaya, unit cost, tarif internal, selisih INA-CBG**.
- Butuh daftar layanan/pathway dengan **defisit terbesar**.

### 4.3. Direktur Medis / Komite Mutu & KP / Komite Medis

- Fokus: **clinical pathway, compliance, LOS, mutu**.
- Butuh lihat pathway dengan **kepatuhan rendah & LOS berlebih**.

### 4.4. Tim Costing / Tim Data

- Fokus: **status proses costing & kualitas data**.
- Pertanyaan utama: data yang dipakai di dashboard **sudah update** dan **valid** atau belum.

---

## 5. Struktur Navigasi Dashboard

### 5.1. Navigasi Utama

- Menu utama (sidebar atau top nav): **“Dashboard KMKB”**
- Blade view utama misal: `resources/views/dashboard/kmkb.blade.php`
- Di dalamnya: **komponen tabs** (dibuat dengan HTML + Alpine.js) dengan label:
  1. Overview
  2. Biaya & Tarif
  3. Pathway & Mutu
  4. Case Variance & JKN
  5. Data & Proses

**Tab default:** Overview.

**State tab** dikelola oleh Alpine.js (mis: `x-data="{ activeTab: 'overview', ... }"`).

### 5.2. Global Filter Bar

Diletakkan **di atas tabs**, dan berada di dalam satu Alpine root component sehingga bisa mempengaruhi semua tab.

Filter global:

- **Periode**
  - Bentuk minimal: dropdown bulan & tahun (`Jan 2025`, dst).
  - Data disimpan di Alpine state: `period`.
- **Payer Type**
  - Options: `All`, `JKN`, `Non-JKN`.
  - State: `payerType`.
- **Kelas Perawatan** (opsional)
  - Options: `All`, `VIP`, `I`, `II`, `III`.
  - State: `kelasRawat`.
- Tombol:
  - `Apply` → men-trigger panggilan AJAX (fetch) untuk memuat ulang data semua tab aktif.
  - `Reset` → mengembalikan ke default.

Implementasi:

- Gunakan `fetch()` atau `axios` untuk panggil endpoint JSON Laravel (lihat bagian 7).
- Setiap tab bisa punya endpoint sendiri – filter dikirim sebagai query string.

---

## 6. Detail Per Tab

> **Catatan teknis:**
>
> - Setiap tab bisa di-render sebagai section `<div x-show="activeTab === 'overview'">...</div>`.
> - Data tiap tab dipegang di state Alpine masing-masing (bisa nested object di dalam root).

---

### 6.1. Tab 1 – Overview

**Cerita:**

> “Secara umum, mutu & biaya kita lagi sehat atau sakit?”

#### 6.1.1. Komponen

1. **KPI Summary Tiles (4–6 tile)**

Contoh tile:

- Total Biaya (Actual Cost) periode ini
- Rata-rata selisih biaya vs INA-CBG (kasus JKN)
- Pathway compliance overall (%)
- Cost variance overall vs pathway (%)
- (opsional) Margin rata-rata tarif internal vs unit cost (Non-JKN)

Data:

- Diterima dari endpoint JSON, mis. `/api/dashboard/overview?period=...`.
- Disimpan di Alpine: `overview.kpis`.

Interaksi:

- Hover: tooltip (bisa pakai title sederhana).
- Klik tile tertentu: `@click="activeTab = 'pathway_mutu'"` atau `'biaya_tarif'` sesuai relevansi.

2. **Chart: Total Cost vs INA-CBG (JKN)**

- Library: Chart.js / ApexCharts / ECharts (pilih salah satu).
- Tipe: line atau bar chart.
- Data:
  - X-axis: beberapa bulan terakhir atau periode yang dipilih (aggregasi).
  - Series:
    - `actual_cost`
    - `ina_cbg_claim`
- Implementasi:
  - Canvas `<canvas id="overviewCostChart" x-ref="overviewCostChart"></canvas>`
  - Inisialisasi grafik di `x-init` atau setelah data loaded (gunakan `Alpine.effect` di v3).
  - Saat filter berubah → rerender / update chart.

3. **Chart: Pathway Compliance vs LOS (Highlight)**

- Menampilkan top 5 pathway (berdasar volume tertinggi).
- Data:
  - For each pathway:
    - `compliance_percent`
    - `los_standard`
    - `los_actual`
- Bisa dibuat:
  - Grafik bar untuk compliance,
  - Atau combined chart (jika library mendukung).
- Klik bar satu pathway: set filter pathway global untuk tab Pathway & Mutu (disimpan ke state global `selectedPathway` dan switch tab).

4. **Mini Table: 5 Pathway / Layanan Kritis**

Tabela ringkas dengan kolom:

- Pathway / Layanan
- Compliance %
- Average cost per case
- Selisih rata-rata vs INA-CBG (jika JKN)
- Status lampu (HTML badge warna: hijau/kuning/merah)

Interaksi:

- Sorting sederhana di sisi frontend (Alpine) atau backend.
- Klik baris: ubah tab ke `pathway_mutu` dan set filter pathway.

#### 6.1.2. Layout (Desktop & Mobile)

- Desktop:
  - Baris 1: KPI tiles (grid 2–3 kolom).
  - Baris 2: 2 kolom (Cost vs INA-CBG chart, Compliance vs LOS chart).
  - Baris 3: Table full width.
- Mobile:
  - KPI tiles stack vertikal atau grid 2 kolom.
  - Chart dan table stack vertikal.

---

### 6.2. Tab 2 – Biaya & Tarif

**Cerita:**

> “Di mana biaya terbesar dan bagaimana hubungannya dengan tarif internal & INA-CBG?”

#### 6.2.1. Komponen

1. **Chart: Top Cost Center – Post Allocation**

- Tipe: horizontal bar chart.
- Data:
  - Y-axis: cost center.
  - X-axis: total cost (Rp).
- Endpoint: `/api/dashboard/costing/top-cost-centers?period=...`.
- Interaksi:
  - Klik bar: (future) boleh diarahkan ke halaman detail cost center.

2. **Chart: Unit Cost Trend – Layanan Kunci**

- Tipe: line chart multi-series.
- Data:
  - X-axis: periode (bulan).
  - Series: beberapa layanan prioritas.
- Dropdown Blade + Alpine untuk memilih layanan:
  - State: `selectedServices` (array atau single).
- Library grafik di-update via JS saat `selectedServices` berubah.

3. **Table: Tarif Internal vs Unit Cost (Non-JKN)**

Kolom:

- Kode layanan
- Nama layanan
- Unit cost (Rp)
- Tarif internal (Rp)
- Margin (Rp)
- Margin %
- Status (badge Defisit / Surplus / BEP)

Endpoint: `/api/dashboard/costing/tarif-vs-unit-cost?period=...&payer=non_jkn`

Interaksi:

- Sorting (front/back-end).
- Filter status (dropdown “semua/defisit/surplus”).

4. **Table: Unit Cost vs INA-CBG (JKN)**

Kolom:

- Pathway / paket
- Average unit cost per case
- Average INA-CBG per case
- Selisih (Rp & %)
- Volume kasus

Endpoint: `/api/dashboard/costing/unit-vs-cbg?period=...&payer=jkn`.

---

### 6.3. Tab 3 – Pathway & Mutu

**Cerita:**

> “Seberapa patuh terhadap clinical pathway dan apa dampaknya ke LOS & biaya?”

#### 6.3.1. Filter Spesifik

- Dropdown Pathway: `All` / nama pathway.
- (Opsional) Filter DPJP / Departemen.

Semua ini state-nya di Alpine: `pathwayFilter`.

#### 6.3.2. Komponen

1. **Chart: Pathway Compliance per Pathway**

- Tipe: bar chart.
- Data:
  - X-axis: pathway.
  - Y-axis: compliance %.
  - Warna bar bisa disesuaikan status (hijau/kuning/merah) via config.
- Endpoint: `/api/dashboard/pathway/compliance?period=...`

2. **Chart: LOS Actual vs LOS Standar per Pathway**

- Tipe: grouped bar.
- Data:
  - X: pathway.
  - Series:
    - `los_standard`
    - `los_actual`
- Endpoint: `/api/dashboard/pathway/los?period=...`

3. **Table: Ringkasan Pathway**

Kolom:

- Pathway
- Jumlah kasus
- Compliance %
- LOS standar
- LOS actual
- Average cost per case
- Status LOS: “Over” / “On Target”

Endpoint: `/api/dashboard/pathway/summary?period=...`

4. **Table: Top Non-compliant Steps**

Kolom:

- Pathway
- Nama step
- % ketidakpatuhan
- (Opsional) flag dampak (LOS / cost)

Endpoint: `/api/dashboard/pathway/noncompliant-steps?period=...&pathway=...`

---

### 6.4. Tab 4 – Case Variance & JKN Risk

**Cerita:**

> “Kasus mana saja yang bikin keuangan jebol vs INA-CBG dan seberapa besar risikonya?”

#### 6.4.1. Filter Spesifik

- Tipe variance: `actual_vs_pathway` | `actual_vs_inacbg`
- Pathway/Diagnosis (dropdown)
- Kelas Rawat (jika belum dijadikan global)

State: `varianceFilter` di Alpine.

#### 6.4.2. Komponen

1. **Chart: Distribusi Variance Cost per Case**

- Tipe: histogram / bar binned.
- X-axis: bucket variance (mis `<-20%`, `-20–0`, `0–20`, `20–50`, `>50`).
- Y-axis: jumlah kasus.
- Endpoint: `/api/dashboard/variance/distribution?period=...&type=...`

2. **KPI Mini Tiles**

Contoh:

- Jumlah kasus dengan variance > +20%
- Jumlah kasus dengan variance < -10%
- Total defisit INA-CBG (Rp) pada periode
- Rata-rata defisit per kasus JKN

Endpoint: `/api/dashboard/variance/kpi?period=...`

3. **Table: Top 10 Kasus Defisit Terbesar (Actual vs INA-CBG)**

Kolom:

- Case ID (bisa anonim)
- Pathway/Diagnosis
- Kelas
- Actual cost
- INA-CBG
- Selisih (Rp & %)
- LOS
- DPJP (opsional)

Endpoint: `/api/dashboard/variance/top-cases?period=...&limit=10`

4. **Table: Pathway/Diagnosis dengan Variance Tinggi (Agregat)**

Kolom:

- Pathway/Diagnosis
- Jumlah kasus
- Average variance (Rp & %)
- % kasus dengan variance > threshold

Endpoint: `/api/dashboard/variance/by-pathway?period=...`

---

### 6.5. Tab 5 – Data & Proses Costing

**Cerita:**

> “Data ini terakhir di-update kapan dan proses costingnya sudah beres belum?”

#### 6.5.1. Hak Akses

- Hanya role tertentu (admin/costing/data-team) boleh melihat detail penuh.
- Gunakan middleware / Gate Laravel:
  - `@can('viewCostingStatus')` untuk blok tab ini.

#### 6.5.2. Komponen

1. **Cards Status Data**

Minimal 4 card:

- GL & Expenses
  - Tanggal import terakhir
  - Periode data terakhir (mis `2025-01`)
  - Status: `OK / Warning / Error`
- Allocation
  - Tanggal run terakhir
  - Periode yang selesai dialokasikan
  - Nama/versi driver
- Unit Cost
  - Versi unit cost aktif (mis `UC_2025_JAN`)
  - Tanggal perhitungan
- Tarif Internal
  - Tanggal update terakhir
  - Jumlah layanan dengan tarif aktif

Endpoint: `/api/dashboard/data-status/summary`

2. **Table: Data Quality / Pre-Allocation Check**

Kolom:

- Nama check (mis `Cost center tanpa driver`, `Layanan tanpa volume`, dsb.)
- Jumlah temuan
- Status (OK/Ada temuan)

Endpoint: `/api/dashboard/data-status/checks`

3. **Timeline Proses (opsional)**

- List simple (ul/li) atau timeline horizontal:
  - Timestamp + aktivitas + status.
- Endpoint: `/api/dashboard/data-status/logs?limit=20`

---
