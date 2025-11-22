# Setup Image Storage di Laravel Cloud

Panduan lengkap untuk menyimpan dan menampilkan image/logo di Laravel Cloud.

## 📋 Daftar Isi

1. [Masalah yang Sering Terjadi](#masalah-yang-sering-terjadi)
2. [Setup Storage Link](#setup-storage-link)
3. [Setup Permission](#setup-permission)
4. [Verifikasi Image](#verifikasi-image)
5. [Troubleshooting](#troubleshooting)

---

## Masalah yang Sering Terjadi

### Gejala:
- ❌ Image logo tidak tampil (broken image)
- ❌ Image yang di-upload tidak bisa diakses
- ❌ Error 404 saat mengakses image dari storage

### Penyebab:
1. Storage link belum dibuat (`public/storage` → `storage/app/public`)
2. Permission folder `storage` tidak writable
3. Folder `storage/app/public` tidak ada
4. Path asset tidak benar

---

## Setup Storage Link

### 1.1 Buat Storage Link (Setelah Deploy)

Setelah deploy pertama kali, buat storage link melalui SSH terminal Laravel Cloud:

1. **Akses SSH Terminal:**
   - Di Laravel Cloud dashboard, klik tab **"SSH"** atau **"Terminal"**
   - Atau gunakan command:
     ```bash
     laravel cloud ssh
     ```

2. **Jalankan Command:**
   ```bash
   php artisan storage:link
   ```

3. **Verifikasi:**
   ```bash
   ls -la public/storage
   ```
   
   Seharusnya menampilkan symlink ke `storage/app/public`

### 1.2 Otomatisasi Storage Link (Recommended)

Untuk memastikan storage link selalu dibuat saat deploy, tambahkan di **After Deploy Hook** di Laravel Cloud:

1. Di Laravel Cloud dashboard, pilih environment
2. Klik tab **"Settings"** → **"Deployment Hooks"**
3. Di bagian **"After Deploy"**, tambahkan:
   ```bash
   php artisan storage:link
   ```

Atau update build command untuk include storage link:

```bash
# Ensure required directories exist and are writable
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public
chmod -R 775 bootstrap/cache storage

# Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install NPM dependencies
npm ci

# Build frontend assets
npm run build

# Create storage link
php artisan storage:link

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Setup Permission

### 2.1 Set Permission untuk Storage

Pastikan folder `storage` dan `bootstrap/cache` memiliki permission yang benar:

1. **Via SSH Terminal:**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Atau via Build Command:**
   Tambahkan di awal build command:
   ```bash
   mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
   chmod -R 775 storage bootstrap/cache
   ```

### 2.2 Buat Folder yang Diperlukan

Pastikan folder berikut ada:

```bash
storage/app/public
storage/app/public/hospitals  # Untuk logo hospital
storage/framework/cache
storage/framework/sessions
storage/framework/views
storage/logs
```

Buat folder jika belum ada:
```bash
mkdir -p storage/app/public/hospitals
chmod -R 775 storage/app/public
```

---

## Verifikasi Image

### 3.1 Test Image Default (rsbb-logo.png)

Image default seharusnya bisa diakses via:
```
https://your-app.laravelcloud.com/images/rsbb-logo.png
```

**Cara Test:**
1. Buka browser
2. Akses URL di atas
3. Image seharusnya tampil

**Jika tidak tampil:**
- Pastikan file `public/images/rsbb-logo.png` ter-commit di repository
- Pastikan file ada di server setelah deploy

### 3.2 Test Storage Link

Test apakah storage link sudah benar:

1. **Via SSH Terminal:**
   ```bash
   ls -la public/storage
   ```
   
   Seharusnya menampilkan:
   ```
   lrwxrwxrwx 1 www-data www-data ... public/storage -> ../storage/app/public
   ```

2. **Test Upload Image:**
   - Login ke aplikasi
   - Upload logo hospital
   - Cek apakah image tersimpan di `storage/app/public/hospitals/`
   - Akses image via browser

### 3.3 Test Image dari Storage

Setelah upload logo hospital, test akses image:

1. **Cek Path di Database:**
   ```bash
   php artisan tinker
   ```
   
   ```php
   $hospital = \App\Models\Hospital::first();
   echo $hospital->logo_path;
   ```

2. **Test URL:**
   ```php
   echo Storage::disk('public')->url($hospital->logo_path);
   ```
   
   Seharusnya menghasilkan URL seperti:
   ```
   https://your-app.laravelcloud.com/storage/hospitals/logo-name.png
   ```

3. **Akses di Browser:**
   - Buka URL yang dihasilkan
   - Image seharusnya tampil

---

## Troubleshooting

### 4.1 Error: Storage Link Already Exists

**Penyebab:** Storage link sudah ada tapi mungkin broken.

**Solusi:**
```bash
# Hapus link yang ada
rm public/storage

# Buat ulang
php artisan storage:link
```

### 4.2 Error: Permission Denied

**Penyebab:** Permission folder tidak cukup.

**Solusi:**
```bash
# Set permission
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Pastikan folder writable
chmod -R 775 storage/app/public
```

### 4.3 Image 404 Not Found

**Penyebab:** 
- Storage link belum dibuat
- Path image salah
- File tidak ada di storage

**Solusi:**

1. **Cek Storage Link:**
   ```bash
   ls -la public/storage
   ```
   
   Jika tidak ada, buat:
   ```bash
   php artisan storage:link
   ```

2. **Cek File:**
   ```bash
   ls -la storage/app/public/hospitals/
   ```
   
   Pastikan file ada di folder tersebut.

3. **Cek Path di Database:**
   ```bash
   php artisan tinker
   ```
   
   ```php
   $hospital = \App\Models\Hospital::first();
   $path = $hospital->logo_path;
   echo Storage::disk('public')->exists($path) ? 'EXISTS' : 'NOT FOUND';
   echo "\n";
   echo Storage::disk('public')->path($path);
   ```

### 4.4 Image Default (rsbb-logo.png) Tidak Tampil

**Penyebab:** File tidak ter-commit atau path salah.

**Solusi:**

1. **Pastikan File Ter-commit:**
   ```bash
   git ls-files public/images/rsbb-logo.png
   ```
   
   Jika tidak ada, commit file:
   ```bash
   git add public/images/rsbb-logo.png
   git commit -m "Add default logo"
   git push
   ```

2. **Cek File di Server:**
   ```bash
   ls -la public/images/rsbb-logo.png
   ```
   
   Pastikan file ada setelah deploy.

3. **Test URL:**
   ```
   https://your-app.laravelcloud.com/images/rsbb-logo.png
   ```

### 4.5 Image Upload Gagal

**Penyebab:** 
- Permission folder tidak writable
- Folder tidak ada
- Upload size limit

**Solusi:**

1. **Cek Permission:**
   ```bash
   ls -la storage/app/public/
   chmod -R 775 storage/app/public
   ```

2. **Buat Folder:**
   ```bash
   mkdir -p storage/app/public/hospitals
   chmod -R 775 storage/app/public/hospitals
   ```

3. **Cek Upload Size Limit:**
   - Di Laravel Cloud, cek PHP settings
   - Pastikan `upload_max_filesize` dan `post_max_size` cukup besar

### 4.6 Image Tampil di Local tapi Tidak di Production

**Penyebab:** 
- Storage link belum dibuat di production
- Path berbeda antara local dan production

**Solusi:**

1. **Pastikan Storage Link:**
   ```bash
   php artisan storage:link
   ```

2. **Gunakan Helper Function:**
   - Untuk image default: `asset('images/rsbb-logo.png')`
   - Untuk image dari storage: `Storage::disk('public')->url($path)`

3. **Jangan Hardcode Path:**
   ❌ Jangan gunakan: `/storage/hospitals/logo.png`
   ✅ Gunakan: `Storage::disk('public')->url('hospitals/logo.png')`

---

## Best Practices

### 5.1 Struktur Folder

```
storage/
├── app/
│   └── public/
│       ├── hospitals/          # Logo hospital
│       └── .gitkeep           # Pastikan folder ter-track
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/
```

### 5.2 Upload Image

**Gunakan Storage Facade:**
```php
use Illuminate\Support\Facades\Storage;

// Upload
$path = $request->file('logo')->store('hospitals', 'public');

// Get URL
$url = Storage::disk('public')->url($path);

// Check exists
if (Storage::disk('public')->exists($path)) {
    // File exists
}
```

### 5.3 Display Image

**Di Blade Template:**
```blade
{{-- Image default --}}
<img src="{{ asset('images/rsbb-logo.png') }}" alt="Logo">

{{-- Image dari storage --}}
@if($hospital->logo_path)
    <img src="{{ Storage::disk('public')->url($hospital->logo_path) }}" alt="Logo">
@else
    <img src="{{ asset('images/rsbb-logo.png') }}" alt="Logo">
@endif
```

### 5.4 Deploy Checklist

- [ ] Storage link dibuat (`php artisan storage:link`)
- [ ] Permission folder storage sudah benar (775)
- [ ] Folder `storage/app/public/hospitals` ada
- [ ] File default logo (`public/images/rsbb-logo.png`) ter-commit
- [ ] After Deploy Hook include `php artisan storage:link`
- [ ] Build command include permission setup

---

## Quick Fix Commands

Jika image tidak tampil, jalankan command berikut di SSH terminal Laravel Cloud:

```bash
# 1. Buat folder yang diperlukan
mkdir -p storage/app/public/hospitals
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# 2. Set permission
chmod -R 775 storage bootstrap/cache

# 3. Buat storage link
php artisan storage:link

# 4. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 5. Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Verifikasi
ls -la public/storage
ls -la storage/app/public/hospitals
```

---

## Update Deployment Script

Update file `laravel-cloud-deploy.sh` untuk include setup image:

```bash
#!/bin/bash

echo "🚀 Starting deployment process..."

# Ensure required directories exist and are writable
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public/hospitals
chmod -R 775 bootstrap/cache storage

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear and optimize
echo "⚙️  Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed!"
```

---

**Selamat! Image storage sudah berhasil di-setup! 🎉**

Jika masih ada masalah, cek bagian [Troubleshooting](#troubleshooting) atau hubungi Laravel Cloud support.

