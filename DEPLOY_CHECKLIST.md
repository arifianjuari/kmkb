# Checklist Deploy ke Laravel Cloud

Quick reference checklist untuk deploy aplikasi KMKB ke Laravel Cloud.

## ✅ Pre-Deploy Checklist

### Repository

- [ ] Repository Git sudah dibuat (GitHub/GitLab/Bitbucket)
- [ ] Semua kode sudah di-commit dan di-push
- [ ] File `.env` TIDAK ter-commit (sudah di `.gitignore`)
- [ ] File `.env.example` sudah ada dan lengkap

### Aplikasi

- [ ] `composer.json` dan `composer.lock` sudah ter-commit
- [ ] `package.json` dan `package-lock.json` sudah ter-commit
- [ ] Semua migration files sudah ter-commit
- [ ] Build scripts sudah benar di `package.json`

---

## ✅ Laravel Cloud Setup

### Akun & Project

- [ ] Akun Laravel Cloud sudah dibuat
- [ ] Project baru sudah dibuat
- [ ] Repository sudah di-connect ke Laravel Cloud
- [ ] Environment sudah dibuat (production/staging)

---

## ✅ Configuration

### Environment Variables

- [ ] `APP_NAME` sudah di-set
- [ ] `APP_ENV=production` sudah di-set
- [ ] `APP_DEBUG=false` sudah di-set
- [ ] `APP_URL` sudah di-set (URL dari Laravel Cloud)
- [ ] `APP_KEY` akan di-generate otomatis (biarkan kosong dulu)

### Database Variables

- [ ] Database sudah dibuat di Laravel Cloud
- [ ] `DB_HOST` sudah di-set
- [ ] `DB_PORT=3306` sudah di-set
- [ ] `DB_DATABASE` sudah di-set
- [ ] `DB_USERNAME` sudah di-set
- [ ] `DB_PASSWORD` sudah di-set

### SIMRS Database (jika diperlukan)

- [ ] `SIMRS_DB_HOST` sudah di-set
- [ ] `SIMRS_DB_PORT=3306` sudah di-set
- [ ] `SIMRS_DB_DATABASE` sudah di-set
- [ ] `SIMRS_DB_USERNAME` sudah di-set
- [ ] `SIMRS_DB_PASSWORD` sudah di-set

### Session & Cache

- [ ] `SESSION_DRIVER=database` sudah di-set
- [ ] `CACHE_DRIVER=database` sudah di-set
- [ ] `QUEUE_CONNECTION=database` sudah di-set

---

## ✅ Deploy

### Build & Deploy

- [ ] Build command sudah dikonfigurasi di Laravel Cloud
- [ ] Deploy pertama sudah di-trigger
- [ ] Deploy berhasil tanpa error

### Post-Deploy

- [ ] `APP_KEY` sudah di-generate (jika belum otomatis)
- [ ] Migrations sudah di-run: `php artisan migrate --force`
- [ ] Seeders sudah di-run (jika ada): `php artisan db:seed --force`
- [ ] Storage link sudah dibuat: `php artisan storage:link`
- [ ] Cache sudah di-clear dan di-rebuild

---

## ✅ Verification

### Aplikasi

- [ ] Aplikasi bisa diakses via URL Laravel Cloud
- [ ] Halaman login bisa dibuka
- [ ] Login berfungsi dengan user yang ada
- [ ] Dashboard bisa diakses setelah login

### Database

- [ ] Data bisa di-load dari database
- [ ] CRUD operations berfungsi
- [ ] SIMRS connection berfungsi (jika ada)

### Assets

- [ ] CSS loading dengan benar
- [ ] JavaScript loading dengan benar
- [ ] Images loading dengan benar
- [ ] File upload berfungsi (jika ada)

### Logs

- [ ] Tidak ada error di application logs
- [ ] Tidak ada error di deployment logs

---

## 📝 Quick Commands (SSH Terminal)

Setelah deploy, jalankan command berikut di SSH terminal Laravel Cloud:

```bash
# Generate APP_KEY (jika belum ada)
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Run seeders (jika ada)
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Clear and cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions (jika diperlukan)
chmod -R 775 storage bootstrap/cache
```

---

## 🔧 Troubleshooting Quick Fix

| Error                      | Quick Fix                            |
| -------------------------- | ------------------------------------ |
| APP_KEY not set            | `php artisan key:generate --force`   |
| Database connection failed | Check DB\_\* environment variables   |
| Assets 404                 | Run `npm run build` and clear cache  |
| Storage link not found     | `php artisan storage:link`           |
| Migration failed           | Check database permissions           |
| Build failed               | Check Node.js version and build logs |

---

**📖 Untuk panduan lengkap, lihat file `DEPLOY_LARAVEL_CLOUD.md`**
