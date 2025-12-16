# Panduan Lengkap Image Storage di Laravel Cloud

Panduan komprehensif untuk setup, konfigurasi, dan troubleshooting image storage di Laravel Cloud.

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Setup Object Storage (S3/R2)](#setup-object-storage-s3r2)
3. [Setup Local Storage](#setup-local-storage)
4. [Persistent Storage](#persistent-storage)
5. [Migrasi ke Object Storage](#migrasi-ke-object-storage)
6. [Upload dan Display Image](#upload-dan-display-image)
7. [Troubleshooting](#troubleshooting)
8. [Quick Fix Commands](#quick-fix-commands)

---

## Overview

### Masalah yang Sering Terjadi

- ❌ Image logo tidak tampil (broken image)
- ❌ Image yang di-upload tidak bisa diakses
- ❌ Error 404 saat mengakses image dari storage
- ❌ Gambar hilang setelah deployment

### Penyebab Umum

1. Storage link belum dibuat (`public/storage` → `storage/app/public`)
2. Permission folder `storage` tidak writable
3. Folder `storage/app/public` tidak ada
4. Path asset tidak benar
5. Fresh clone deployment menghapus uploaded files

### Solusi

Ada 3 opsi untuk menyimpan file di Laravel Cloud:

| Opsi | Kelebihan | Kekurangan |
|------|-----------|------------|
| **Object Storage (S3/R2)** | Persistent, scalable, tidak hilang saat deploy | Perlu setup credentials |
| **Persistent Storage** | Dikelola Laravel Cloud, otomatis | Terbatas pada plan tertentu |
| **Local Storage + Hooks** | Simple | Perlu backup/restore manual |

---

## Setup Object Storage (S3/R2)

### Prerequisites

1. Package `league/flysystem-aws-s3-v3` sudah terinstall:
```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

2. Buat bucket di Laravel Cloud dashboard:
   - Buka Laravel Cloud dashboard
   - Pilih environment Anda
   - Klik "Add bucket" pada infrastructure canvas
   - Pilih "Laravel Object Storage" sebagai bucket type
   - Beri nama bucket (misal: `kmkb`)
   - Pilih visibility (Public untuk gambar yang perlu diakses publik)
   - Set disk name (misal: `uploads` atau `public`)

### Konfigurasi Environment Variables

Setelah bucket dibuat, Laravel Cloud akan memberikan credentials. Tambahkan ke environment variables:

```env
AWS_BUCKET=fls-xxxx-xxxx-xxxx-xxxx
AWS_DEFAULT_REGION=auto
AWS_ENDPOINT=https://xxxxx.r2.cloudflarestorage.com
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Cara Kerja

Aplikasi akan otomatis menggunakan Object Storage jika credentials AWS tersedia, atau fallback ke local storage (`public` disk) jika tidak ada.

**Helper Functions Tersedia:**

1. **`uploads_disk()`**: Mengembalikan nama disk yang digunakan ('uploads' atau 'public')
2. **`storage_url($path)`**: Mengembalikan URL lengkap untuk file di storage

### Penggunaan di Controller

```php
use Illuminate\Support\Facades\Storage;

// Upload file
$uploadDisk = uploads_disk();
$file->storeAs('hospitals', $filename, $uploadDisk);

// Check exists
if (Storage::disk($uploadDisk)->exists($path)) {
    // File exists
}

// Delete file
Storage::disk($uploadDisk)->delete($path);
```

### Penggunaan di Blade Template

```blade
{{-- Menggunakan helper function --}}
<img src="{{ storage_url($hospital->logo_path) }}" alt="Logo">

{{-- Atau manual --}}
@if(Storage::disk(uploads_disk())->exists($path))
    <img src="{{ Storage::disk(uploads_disk())->url($path) }}" alt="Image">
@endif
```

---

## Setup Local Storage

### Buat Storage Link

Setelah deploy, buat storage link melalui SSH terminal Laravel Cloud:

```bash
php artisan storage:link
```

Verifikasi:
```bash
ls -la public/storage
# Seharusnya menampilkan: lrwxrwxrwx ... public/storage -> ../storage/app/public
```

### Setup Permission

```bash
chmod -R 775 storage bootstrap/cache
mkdir -p storage/app/public/hospitals storage/app/public/references
chmod -R 775 storage/app/public
```

### Otomatisasi via Build Command

Tambahkan di build command Laravel Cloud:

```bash
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public/hospitals storage/app/public/references
chmod -R 775 bootstrap/cache storage
php artisan storage:link
```

---

## Persistent Storage

### Setup Persistent Storage (RECOMMENDED)

**Ini adalah solusi TERBAIK dan PERMANEN** - gambar tidak akan hilang lagi setelah setup ini.

#### Langkah-langkah:

1. **Login ke Laravel Cloud Dashboard**
2. **Buka Settings → Storage**
3. **Tambahkan Persistent Storage untuk Hospitals:**
   - Path: `/storage/app/public/hospitals`
   - Size: `1` GB
   - Description: `Hospital logos storage`
4. **Tambahkan Persistent Storage untuk References:**
   - Path: `/storage/app/public/references`
   - Size: `2` GB
   - Description: `Reference article images storage`

#### Keuntungan:

✅ **Permanen** - File tidak akan hilang saat deploy  
✅ **Otomatis** - Tidak perlu setup hooks atau script  
✅ **Reliable** - Dikelola langsung oleh Laravel Cloud  
✅ **Scalable** - Bisa di-resize sesuai kebutuhan

---

## Migrasi ke Object Storage

### Via Artisan Command

```bash
php artisan storage:migrate-to-s3
```

Atau dengan dry-run untuk melihat file yang akan dimigrasi:

```bash
php artisan storage:migrate-to-s3 --dry-run
```

### Via Web Interface

1. Login sebagai admin
2. Akses route: `POST /migrate-storage`

### Via Tinker

```php
use Illuminate\Support\Facades\Storage;

// Migrate hospitals
$hospitals = \App\Models\Hospital::whereNotNull('logo_path')->get();
foreach ($hospitals as $hospital) {
    $path = $hospital->logo_path;
    
    // Skip jika sudah di Object Storage
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        continue;
    }
    
    // Normalize path
    $normalizedPath = $path;
    if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
        $normalizedPath = ltrim(str_after($path, '/storage/'), '/');
    }
    
    // Upload ke Object Storage
    if (Storage::disk('public')->exists($normalizedPath)) {
        $content = Storage::disk('public')->get($normalizedPath);
        Storage::disk('uploads')->put($normalizedPath, $content);
        echo "✅ Migrated: {$path}\n";
    }
}
```

---

## Upload dan Display Image

### Struktur Folder

```
storage/
├── app/
│   └── public/
│       ├── hospitals/          # Logo hospital
│       │   └── .gitkeep
│       └── references/         # Gambar artikel
│           └── .gitkeep
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/
```

### Upload Image (Controller)

```php
if ($request->hasFile('logo')) {
    $logo = $request->file('logo');
    $logoName = 'hospital-' . $hospital->id . '-' . time() . '.' . $logo->getClientOriginalExtension();
    $logo->storeAs('hospitals', $logoName, 'public');
    $hospital->logo_path = 'hospitals/' . $logoName;
    $hospital->save();
}
```

### Display Image (Blade)

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

---

## Troubleshooting

### Error: Storage Link Already Exists

```bash
rm public/storage
php artisan storage:link
```

### Error: Permission Denied

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Image 404 Not Found

1. **Cek Storage Link:**
   ```bash
   ls -la public/storage
   ```

2. **Buat jika tidak ada:**
   ```bash
   php artisan storage:link
   ```

3. **Cek File:**
   ```bash
   ls -la storage/app/public/hospitals/
   ```

4. **Cek Path di Database:**
   ```php
   $hospital = \App\Models\Hospital::first();
   $path = $hospital->logo_path;
   echo Storage::disk('public')->exists($path) ? 'EXISTS' : 'NOT FOUND';
   ```

### Gambar Broken Setelah Setup Object Storage

1. **Cek apakah file ada di Object Storage:**
   ```php
   Storage::disk('public')->exists('hospitals/logo.png')
   ```

2. **Cek URL yang dihasilkan:**
   ```php
   Storage::disk('public')->url('hospitals/logo.png')
   ```

3. **Cek CORS policy** - Pastikan bucket visibility adalah "Public"

### Error 500 setelah Setup Object Storage

1. **Pastikan package terinstall:**
   ```bash
   composer show league/flysystem-aws-s3-v3
   ```

2. **Jika belum, install:**
   ```bash
   composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
   ```

3. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

### Gambar Hilang Setelah Deploy

**Jika menggunakan Persistent Storage:**
- Pastikan path sudah benar: `/storage/app/public/hospitals`
- Cek mount: `df -h | grep hospitals`

**Jika menggunakan Local Storage:**
- Setup Before Deploy Hook untuk backup
- Setup After Deploy Hook untuk restore
- Lihat file `laravel-cloud-hooks.sh` untuk script lengkap

---

## Quick Fix Commands

Jalankan command berikut via SSH jika ada masalah:

```bash
# 1. Buat folder yang diperlukan
mkdir -p storage/app/public/hospitals storage/app/public/references
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
ls -la storage/app/public/references
```

---

## Deployment Hooks untuk Backup/Restore

### Before Deploy Hook

```bash
BACKUP_BASE="/home/forge/storage_backup"
BACKUP_DIR="$BACKUP_BASE/uploads_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_BASE"

# Backup hospitals
if [ -d "storage/app/public/hospitals" ] && [ "$(ls -A storage/app/public/hospitals 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    mkdir -p "$BACKUP_DIR/hospitals"
    find storage/app/public/hospitals -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/hospitals/" \;
fi

# Backup references
if [ -d "storage/app/public/references" ] && [ "$(ls -A storage/app/public/references 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    mkdir -p "$BACKUP_DIR/references"
    find storage/app/public/references -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/references/" \;
fi

# Keep only last 5 backups
ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | tail -n +6 | xargs rm -rf 2>/dev/null || true
```

### After Deploy Hook

```bash
mkdir -p storage/app/public/hospitals storage/app/public/references

BACKUP_BASE="/home/forge/storage_backup"
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | head -1)

if [ -n "$LATEST_BACKUP" ] && [ -d "$LATEST_BACKUP" ]; then
    [ -d "$LATEST_BACKUP/hospitals" ] && cp -n "$LATEST_BACKUP/hospitals"/* storage/app/public/hospitals/ 2>/dev/null || true
    [ -d "$LATEST_BACKUP/references" ] && cp -n "$LATEST_BACKUP/references"/* storage/app/public/references/ 2>/dev/null || true
fi

touch storage/app/public/hospitals/.gitkeep storage/app/public/references/.gitkeep
php artisan storage:link --force
chmod -R 775 storage/app/public/hospitals storage/app/public/references
```

---

## Checklist

### Setup Object Storage
- [ ] Package `league/flysystem-aws-s3-v3` terinstall
- [ ] Bucket dibuat di Laravel Cloud
- [ ] Credentials AWS di-set di environment variables
- [ ] Config cache di-clear dan di-rebuild
- [ ] Test upload file baru

### Setup Persistent Storage
- [ ] Path `/storage/app/public/hospitals` ditambahkan
- [ ] Path `/storage/app/public/references` ditambahkan
- [ ] Deploy ulang aplikasi
- [ ] Verifikasi file tidak hilang

### Setup Local Storage
- [ ] Storage link dibuat
- [ ] Permission folder 775
- [ ] Before/After Deploy Hook di-setup
- [ ] Test upload dan display image

---

**Selamat! Image storage sudah berhasil di-setup! 🎉**
