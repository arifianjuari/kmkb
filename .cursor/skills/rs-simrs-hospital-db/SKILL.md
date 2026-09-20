---
name: rs-simrs-hospital-db
description: Integrasi read-only ke database SIMRS Khanza RS (MySQL simsvbaru, user web_hasta) — koneksi Laravel hospital_sims, VPN/jaringan internal, query rawat jalan (Ralan) dan rawat inap (Ranap), health check, cache ketersediaan, dan pola API search/import. Gunakan saat membangun webapp yang mengakses server RS, SIMRS, simsvbaru, hospital_sims, HOSPITAL_DB_*, atau menyalin integrasi dari Faskelola/praktekobgin.
---

# Akses Database SIMRS RS (Khanza / simsvbaru)

Skill ini mendeskripsikan pola integrasi **read-only** ke MySQL SIMRS RS Bhayangkara Hasta Brata Batu (dan RS sejenis dengan skema Khanza). Referensi implementasi lengkap: proyek **Faskelola** (`modules/Emr/app/Services/HospitalInpatientLookupService.php`, `HospitalOutpatientLookupService.php`).

## Prinsip

1. **Bukan database aplikasi** — SIMRS (`simsvbaru`) terpisah dari DB utama webapp. Hanya **SELECT**; jangan INSERT/UPDATE/DELETE ke SIMRS.
2. **Jaringan dulu** — MySQL port 3306 biasanya **tidak** terbuka dari internet. Butuh server di jaringan RS, **OpenVPN** (split tunnel `172.16.1.0/24`), atau **API HTTPS** di sisi RS.
3. **Kredensial hanya di `.env`** — jangan commit password; user tipikal read-only: `web_hasta`.
4. **Satu koneksi per deployment** — env `HOSPITAL_DB_*` global; pemetaan ke tenant/org di aplikasi Anda (bukan di MySQL SIMRS).

## Checklist integrasi (Laravel)

```
- [ ] Tambah connection `hospital_sims` di config/database.php
- [ ] Variabel .env HOSPITAL_DB_* + HOSPITAL_DB_ENABLED
- [ ] Config flag fitur (mis. hospital_db.enabled)
- [ ] Service: isConfigured(), isAvailable(), search*, import* dengan DB::connection()
- [ ] Cache ketersediaan (30s) + purge connection setelah probe
- [ ] Timeout koneksi pendek (3s) agar request tidak hang
- [ ] Route API terproteksi auth + permission; validasi input `q` min 2 char
- [ ] UI: toggle lokal vs RS; status endpoint sebelum search
- [ ] Test feature dengan mock connection atau skip jika env kosong
```

Detail env, SQL, dan snippet: [reference.md](reference.md), [laravel-integration.md](laravel-integration.md).

## Variabel environment (template)

```env
HOSPITAL_DB_ENABLED=true
HOSPITAL_DB_HOST=172.16.1.251
HOSPITAL_DB_PORT=3306
HOSPITAL_DB_DATABASE=simsvbaru
HOSPITAL_DB_USERNAME=web_hasta
HOSPITAL_DB_PASSWORD="<dari tim IT>"
HOSPITAL_DB_CONNECT_TIMEOUT=3
HOSPITAL_DB_AVAILABILITY_CACHE_SECONDS=30
```

Setelah ubah `.env`: `php artisan config:clear` (production: `config:cache`).

## Kapan koneksi “configured” vs “available”

| Status | Arti |
|--------|------|
| **configured** | `HOSPITAL_DB_ENABLED=true` dan host, database, username terisi |
| **available** | configured + TCP ke host:port OK + `SELECT 1` sukses (biasanya via cache) |

Tampilkan toggle RS di UI jika **configured**; disable search atau tampilkan offline jika **available=false**.

## Query inti (ringkas)

**Rawat jalan hari ini (Ralan)** — `status_lanjut = 'Ralan'`, `tgl_registrasi` hari ini, `stts != 'Batal'`:

- Tabel: `reg_periksa` → `pasien`, `poliklinik`, `dokter`
- Kunci impor: `no_rawat`

**Rawat inap aktif (Ranap)** — `kamar_inap.stts_pulang = '-'`:

- Tabel: `kamar_inap` → `reg_periksa` → `pasien`, `kamar`, `bangsal`, `dpjp_ranap` → `dokter`
- Kunci impor: `no_rawat`

Lihat JOIN lengkap di [reference.md](reference.md).

## Alur API yang disarankan

1. `GET .../hospital-outpatient/status` → `{ available: bool }`
2. `GET .../hospital-outpatient/search?q=` → array hasil (limit 20)
3. `POST .../hospital-outpatient/import` body `{ no_rawat }` → sinkron ke DB aplikasi + hints form

Pola sama untuk ranap dengan service/controller terpisah atau gabung `searchForAdmission` (ranap + rajal).

## Mapping pasien SIMRS → aplikasi

Urutan match yang aman (sesuai Faskelola):

1. Cari by NIK (`pasien.no_ktp`) jika valid
2. Else by MRN (`no_rkm_medis`) di konteks tenant/org Anda
3. Create atau update atribut demografi; daftarkan ke tenant jika belum

Field mapping umum: `nm_pasien` → nama, `tgl_lahir`, `jk` (L/P), `alamat`, `no_tlp`, `gol_darah`, dll.

## Keamanan

- Jangan expose endpoint search ke publik tanpa auth.
- Rate limit pencarian; `q` max length ~100.
- Gunakan akun MySQL **read-only**.
- Jika webapp di cloud tanpa VPN: jangan hardcode IP publik RS untuk MySQL — gunakan VPN di VPS atau gateway API internal RS.

## Stack non-Laravel

Prinsip sama: PDO/MySQL client kedua, env terpisah, hanya SELECT, health check, timeout. Legacy praktekobgin memakai `$conn_db1` di `config.php` — migrasi ke env + service layer.

## Verifikasi cepat (Laravel)

```bash
php artisan tinker --execute="
\$s = app(\Modules\Emr\App\Services\HospitalInpatientLookupService::class);
echo 'configured=' . (\$s->isConfigured() ? 'yes' : 'no') . PHP_EOL;
echo 'available=' . (\$s->isAvailable(true) ? 'yes' : 'no') . PHP_EOL;
"
```

Di proyek baru, ganti FQCN service Anda.

## Salin ke project lain

Copy seluruh folder skill:

```text
rs-simrs-hospital-db/
├── SKILL.md
├── reference.md
└── laravel-integration.md
```

Ke `.cursor/skills/` project target atau `~/.cursor/skills/` untuk semua project.

Opsional: salin juga pola dari Faskelola `config/database.php` (blok `hospital_sims`) dan service EMR sebagai starting point, lalu sesuaikan namespace dan routing.
