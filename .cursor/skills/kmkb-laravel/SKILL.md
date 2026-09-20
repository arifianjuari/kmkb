---
name: kmkb-laravel
description: >-
  Panduan pengembangan aplikasi KMKB (Kendali Mutu Kendali Biaya) di repo kmkb —
  Laravel 10, multi-rumah sakit via hospital_id, unit costing, tarif, clinical pathway,
  kasus pasien, integrasi SIMRS read-only, permission per role. Gunakan saat mengubah
  fitur KMKB, costing, pathway, tarif, patient case, dashboard, atau dokumentasi di folder documentation/.
---

# KMKB Laravel — skill proyek

## Ringkasan domain

KMKB mendukung **unit costing**, **tarif**, **clinical pathway**, dan **analisis kasus pasien** (compliance & variance) per rumah sakit. Spesifikasi bisnis dan alur user ada di `documentation/` — utamakan dokumen itu sebelum mengasumsikan behavior.

| Area | Model / modul utama | Dokumen acuan |
|------|---------------------|---------------|
| Unit cost & alokasi | `UnitCostService`, allocation*, `GlExpense` | `documentation/CARA-MENGGUNAKAN-UNIT-COST-COMMAND.md`, `SPEC-DB-UserFlow-CostingTariffClinicalPathway.md` |
| Tarif | `TariffService`, `FinalTariff`, explorer/simulasi | PRD & SPEC di `documentation/` |
| Clinical pathway | `Pathway*`, `PathwayStep`, approval template | `documentation/User Flow Praktis Webapp KMKB.md` |
| Kasus pasien | `PatientCaseController`, costing/variance | SPEC § patient cases |
| SIMRS | `App\Models\Simrs\*`, `SimrsService` | `documentation/database/SIMRS_*.md` |
| Menu & role | `MenuHelper`, `config/permissions.php` | `documentation/ROLE-PERMISSIONS-CRUD.md`, `MENU-STRUCTURE-*.md` |

Detail peta file dan dokumen: [reference.md](reference.md).

## Stack & konvensi kode

- **PHP ^8.1**, **Laravel ^10**, auth **Breeze**, API **Sanctum**, export **DomPDF / PhpSpreadsheet / PhpWord**.
- Controller di `app/Http/Controllers/` (termasuk subfolder `Pathway/`, `PatientCase/`, `Analytics/`, `Api/`).
- Logika bisnis berat → `app/Services/` (`UnitCostService`, `TariffService`, `SimrsService`).
- Helper global: `app/Helpers/HospitalHelper.php` (`hospital()`, `hospital_cache_key()`, `hospital_storage_path()`, `uploads_disk()`).
- Policy dasar: `app/Policies/BasePolicy.php` — ikuti pola authorize + cek ownership rumah sakit.

## Multi-rumah sakit (wajib)

Satu database aplikasi; isolasi data lewat **`hospital_id`**, bukan tenancy package.

1. Context aktif: `session('hospital_id')` atau `auth()->user()->hospital_id`.
2. Query & validasi: selalu filter / unique rule dengan `hospital_id` aktif (`hospital('id')`).
3. Jangan mengembalikan atau mengubah record milik `hospital_id` lain — mirror pola di controller yang sudah ada (mis. `RevenueSourceController`).
4. Cache & file upload: gunakan `hospital_cache_key()` dan `hospital_storage_path()` / `uploads_disk()` agar tidak bocor antar RS.

Saat menulis test multi-tenant, lihat `tests/Feature/TenantScopingTest.php`.

## Integrasi SIMRS

- Koneksi terpisah (biasanya `hospital_sims` / env `HOSPITAL_DB_*`) — **hanya SELECT** ke DB SIMRS.
- Sync referensi biaya: `app/Console/Commands/SyncCostReferencesFromSimrs.php`, UI setup di `Setup/SimrsIntegrationController`.
- Untuk pola koneksi, VPN, dan query Khanza secara mendalam, gunakan skill pribadi **`rs-simrs-hospital-db`** (`~/.cursor/skills/rs-simrs-hospital-db/`) bersama dokumen `documentation/database/SIMRS_INTEGRATION_GUIDE.md`.

## Database & perintah artisan

**Larangan (production & local):** jangan jalankan `migrate:fresh`, `migrate:reset`, `db:wipe`, atau skrip inline yang setara — kecuali user secara eksplisit meminta reset **dan** `ALLOW_DESTRUCTIVE=1` di `.env`.

Aman: `php artisan migrate --force`, `db:seed --force`, `php artisan test --filter=...`.

Perhitungan unit cost batch: ikuti `documentation/CARA-MENGGUNAKAN-UNIT-COST-COMMAND.md` (`CalculateUnitCost`).

## UI & asset

- Blade + layout `AppLayout` / `GuestLayout`; menu mengikuti permission, bukan hardcode role di view.
- Build front-end: `documentation/development/ASSETS-BUILD-INSTRUCTIONS.md`.

## Alur kerja agent di repo ini

1. **Cari requirement** di `documentation/` (BRD, SPEC, user flow) sebelum mengubah schema atau alur approval.
2. **Perubahan schema** → migration baru di `database/migrations/`, update seeder/README seeder jika ada (`database/seeders/README_*.md`).
3. **Fitur baru** → route (`routes/web.php` / `api.php`), controller, form request jika perlu, policy, dan feature test jika pola sudah dipakai di modul sejenis.
4. **Bahasa UI**: string user-facing banyak memakai `__()` — pertahankan konsistensi i18n yang ada.
5. **Jangan commit** `.env`, kredensial SIMRS, atau isi `storage/logs/` tanpa diminta.

## Dokumentasi paket pihak ketiga

- Laravel core / Breeze: Laravel Boost `search-docs` bila tersedia.
- DomPDF, PhpSpreadsheet, dll.: Context7 (`resolve-library-id` → `query-docs`) sesuai rule workspace.
