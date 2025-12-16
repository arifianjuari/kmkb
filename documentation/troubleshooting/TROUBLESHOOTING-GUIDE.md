# Troubleshooting Guide - KMKB Application

Panduan lengkap untuk mengatasi masalah umum yang terjadi pada aplikasi KMKB.

## 📋 Daftar Isi

1. [Error 500 - Server Error](#error-500---server-error)
2. [Broken Image / Gambar Tidak Tampil](#broken-image--gambar-tidak-tampil)
3. [Cache Issues](#cache-issues)
4. [Database Connection Issues](#database-connection-issues)
5. [Assets Not Loading](#assets-not-loading)
6. [Deployment Issues](#deployment-issues)

---

## Error 500 - Server Error

### Penyebab Umum

1. **Package tidak terinstall** - Terutama setelah setup Object Storage
2. **Config error** - Environment variables tidak valid
3. **Permission error** - Folder tidak writable
4. **Database connection failed**

### Solusi

#### 1. Check Laravel Logs

```bash
tail -n 100 storage/logs/laravel.log
```

#### 2. Jika Error Terkait Object Storage

Pastikan package `league/flysystem-aws-s3-v3` terinstall:

```bash
composer show league/flysystem-aws-s3-v3
```

Jika tidak terinstall:
```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

#### 3. Check Environment Variables Format

```env
# ✅ BENAR (tanpa quotes)
APP_URL=https://kmkb.online
AWS_BUCKET=fls-xxxx

# ❌ SALAH (jangan pakai quotes)
APP_URL="https://kmkb.online"
```

#### 4. Clear dan Rebuild Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 5. Check Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

---

## Broken Image / Gambar Tidak Tampil

### Gejala

- ❌ Image logo tenant tidak tampil (broken image)
- ❌ Image artikel references tidak tampil
- ❌ Error 404 saat mengakses `/storage/...`

### Langkah Diagnosis

#### 1. Cek Path di Database

```bash
php artisan tinker
```

```php
$ref = \App\Models\Reference::whereNotNull('image_path')->first();
if ($ref) {
    echo "ID: {$ref->id}\n";
    echo "Title: {$ref->title}\n";
    echo "Image Path: {$ref->image_path}\n";
}
```

#### 2. Cek File di Storage

```php
$path = $ref->image_path;
$disk = uploads_disk();
echo "Disk: {$disk}\n";

$exists = Storage::disk($disk)->exists($path);
echo "Exists: " . ($exists ? "YES" : "NO") . "\n";

$url = storage_url($path);
echo "URL: {$url}\n";
```

#### 3. Cek Storage Link

```bash
ls -la public/storage
# Harus menampilkan: public/storage -> ../storage/app/public
```

### Solusi

#### Storage Link Tidak Ada

```bash
php artisan storage:link
```

#### File Tidak Ada di Storage

- Upload ulang gambar, atau
- Migrasi dari local ke Object Storage:
  ```bash
  php artisan storage:migrate-to-s3
  ```

#### Permission Error

```bash
chmod -R 775 storage/app/public
```

#### URL Tidak Benar (Object Storage)

Pastikan konfigurasi disk di `config/filesystems.php` sudah benar dan clear config cache:

```bash
php artisan config:clear
php artisan config:cache
```

---

## Cache Issues

### Gejala

- Perubahan tidak terlihat setelah update code
- Layout berantakan setelah deploy
- Data lama masih tampil

### Solusi

#### 1. Clear All Cache

```bash
php artisan optimize:clear
```

Atau manual:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### 2. Rebuild Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 3. Clear Browser Cache

**Hard Refresh:**
- **Chrome/Edge (Mac)**: `Cmd + Shift + R`
- **Chrome/Edge (Windows)**: `Ctrl + Shift + R`
- **Safari**: `Cmd + Option + R`

**Clear Cache Completely:**
- Chrome: `Cmd/Ctrl + Shift + Delete` → Pilih "Cached images and files" → Clear

**Test di Incognito Mode:**
- Chrome: `Cmd/Ctrl + Shift + N`

---

## Database Connection Issues

### Error: SQLSTATE[HY000] [2002] Connection refused

**Penyebab:** Database tidak bisa diakses.

**Solusi:**
1. Periksa `DB_HOST` dan `DB_PORT` di environment variables
2. Pastikan database server running
3. Periksa firewall mengizinkan koneksi

### Error: SQLSTATE[HY000] [1045] Access denied

**Penyebab:** Username atau password salah.

**Solusi:**
1. Periksa `DB_USERNAME` dan `DB_PASSWORD`
2. Test koneksi manual:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

### Error: SQLSTATE[HY000] [1049] Unknown database

**Penyebab:** Nama database salah.

**Solusi:**
1. Periksa `DB_DATABASE`
2. Pastikan database sudah dibuat

### SIMRS Connection Failed

**Penyebab:** Database SIMRS tidak bisa diakses.

**Solusi:**
1. Periksa `SIMRS_DB_*` environment variables
2. Pastikan network access dari Laravel Cloud ke SIMRS
3. Test koneksi:
   ```bash
   php artisan tinker
   DB::connection('simrs')->getPdo();
   ```

---

## Assets Not Loading

### Gejala

- CSS tidak loading (layout berantakan)
- JavaScript error
- Error 404 untuk file di `/build/...`

### Penyebab

1. Assets belum di-build
2. Vite manifest tidak ada
3. File build tidak ter-commit

### Solusi

#### 1. Build Assets

```bash
npm install
npm run build
```

#### 2. Verifikasi Build

Pastikan file berikut ada:
- `public/build/manifest.json`
- `public/build/assets/app-*.css`
- `public/build/assets/app-*.js`

#### 3. Check Vite Directive

Pastikan blade template include:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

#### 4. Clear Cache

```bash
php artisan config:clear
php artisan view:clear
```

---

## Deployment Issues

### Error: bootstrap/cache directory must be present and writable

**Penyebab:** Directory tidak ada saat composer install.

**Solusi:**

Tambahkan di awal build command:
```bash
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chmod -R 775 bootstrap/cache storage
```

### Error: APP_KEY is not set

**Solusi:**
```bash
php artisan key:generate --force
```

### Migration Failed

**Penyebab:** Database belum ready atau permission issue.

**Solusi:**
1. Pastikan database sudah dibuat
2. Pastikan credentials benar
3. Run dengan verbose:
   ```bash
   php artisan migrate --force -vvv
   ```

### Gambar Hilang Setelah Deploy

**Penyebab:** Fresh clone menghapus uploaded files.

**Solusi:**
1. **Best:** Gunakan Persistent Storage di Laravel Cloud
2. **Alternatif:** Setup Before/After Deploy Hooks untuk backup/restore
3. **Recommended:** Gunakan Object Storage (S3/R2)

Lihat [Panduan Image Storage](../storage/PANDUAN-IMAGE-STORAGE.md) untuk detail.

---

## Quick Diagnostic Commands

```bash
# Check Laravel version
php artisan --version

# Check environment
php artisan env

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Check storage link
ls -la public/storage

# Check permissions
ls -la storage/
ls -la bootstrap/cache/

# View recent logs
tail -n 50 storage/logs/laravel.log

# Clear all cache
php artisan optimize:clear
```

---

## Checklist Troubleshooting

- [ ] Check Laravel logs (`storage/logs/laravel.log`)
- [ ] Verify environment variables are set correctly
- [ ] Clear all Laravel cache
- [ ] Check file/folder permissions
- [ ] Verify storage link exists
- [ ] Test database connection
- [ ] Clear browser cache
- [ ] Check if assets are built

---

## Support

Jika masalah masih berlanjut:

1. Check deployment logs di Laravel Cloud Dashboard
2. Enable `APP_DEBUG=true` temporarily untuk detail error
3. Contact Laravel Cloud Support

---

**Semoga troubleshooting guide ini membantu!** 🔧
