# KMKB — referensi cepat

## Dokumentasi utama (`documentation/`)

| File | Isi |
|------|-----|
| `BRD_KMKB_v2.md` | Kebutuhan bisnis |
| `IMPLEMENTATION_PLAN.md` | Rencana implementasi |
| `PRD-WebApp-Costing-Tariff-ClinicalPathway.md` | PRD produk |
| `SPEC-DB-UserFlow-CostingTariffClinicalPathway.md` | Skema DB + alur costing/tarif/pathway |
| `User Flow Praktis Webapp KMKB.md` | Alur praktis per peran |
| `PANDUAN-PENGGUNAAN-WEBAPP.md` | Panduan pengguna |
| `ROLE-PERMISSIONS-CRUD.md` | Role & permission |
| `MENU-STRUCTURE-DESIGN.md` / `MENU-STRUCTURE-REVISED.md` | Struktur menu |
| `database/DAFTAR-TABEL-DAN-KOLOM.md` | Inventaris tabel |
| `database/SIMRS_INTEGRATION_GUIDE.md` | Setup integrasi SIMRS |
| `database/SIMRS_COST_REFERENCE_SYNC.md` | Sync referensi biaya |
| `deployment/DEPLOY-CHECKLIST.md` | Deploy |
| `troubleshooting/TROUBLESHOOTING-GUIDE.md` | Troubleshooting |

## Entry point kode

| Tujuan | Lokasi |
|--------|--------|
| Routing web | `routes/web.php` |
| API (Sanctum) | `routes/api.php` |
| Permission role | `config/permissions.php` |
| Provider SIMRS | `app/Providers/SimrsServiceProvider.php` |
| Model SIMRS | `app/Models/Simrs/` |
| Pathway | `app/Http/Controllers/Pathway/` |
| Kasus pasien | `app/Http/Controllers/PatientCaseController.php`, `PatientCase/` |
| Analytics | `app/Http/Controllers/Analytics/` |
| Costing process | `app/Http/Controllers/CostingProcess/` |

## Test yang berguna

- `tests/Feature/TenantScopingTest.php` — scoping `hospital_id`
- Jalankan subset: `php artisan test --filter=NamaTest`

## Skill terkait (di luar repo)

| Skill | Path | Kapan |
|-------|------|--------|
| SIMRS Khanza read-only | `~/.cursor/skills/rs-simrs-hospital-db/` | Query/sync ke DB RS |
| Geolokasi / geofence | `~/.cursor/skills/laravel-geolokasi-geofence/` | Jika fitur lokasi dipakai |
| Multi-platform UI | `~/.cursor/skills/tampilan-multi-platform/` | Layout PWA/mobile |
