# Setup Upload Gambar References di Laravel Cloud

Panduan lengkap untuk memastikan fitur upload gambar references berjalan dengan baik di Laravel Cloud.

## 📋 Daftar Isi

1. [Persiapan](#persiapan)
2. [Setup Deployment Scripts](#setup-deployment-scripts)
3. [Setup Storage Link](#setup-storage-link)
4. [Verifikasi](#verifikasi)
5. [Troubleshooting](#troubleshooting)

---

## Persiapan

### 1.1 Pastikan Folder Storage Sudah Ada

Folder berikut harus ada di repository:
- `storage/app/public/references/` (dengan file `.gitkeep`)

Jika belum ada, folder sudah dibuat otomatis saat migration. Pastikan file `.gitkeep` ada:

```bash
touch storage/app/public/references/.gitkeep
```

### 1.2 Commit File .gitkeep

Pastikan file `.gitkeep` ter-commit ke repository:

```bash
git add storage/app/public/references/.gitkeep
git commit -m "Add .gitkeep for references storage folder"
git push
```

---

## Setup Deployment Scripts

### 2.1 Update Build Command (Opsional)

Jika menggunakan Build Command di Laravel Cloud, pastikan include:

```bash
# Create storage directories
mkdir -p storage/app/public/references
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force
```

### 2.2 Setup After Deploy Hook (Recommended)

**Cara 1: Menggunakan Script yang Sudah Ada**

File `laravel-cloud-hooks.sh` sudah di-update untuk handle folder `references`. Copy bagian **"AFTER DEPLOY HOOK"** ke Laravel Cloud:

1. Login ke Laravel Cloud Dashboard
2. Pilih environment (Production/Staging)
3. Klik **"Settings"** → **"Deployment Hooks"**
4. Di bagian **"After Deploy"**, paste script berikut:

```bash
# Restore backed up files
echo "📥 Restoring uploaded files..."
mkdir -p storage/app/public/hospitals
mkdir -p storage/app/public/references

# Find latest backup
BACKUP_BASE="/home/forge/storage_backup"
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | head -1)
restored_total=0

if [ -n "$LATEST_BACKUP" ] && [ -d "$LATEST_BACKUP" ]; then
    # Restore hospitals
    if [ -d "$LATEST_BACKUP/hospitals" ] && [ "$(ls -A "$LATEST_BACKUP/hospitals" 2>/dev/null)" ]; then
        restored_count=0
        for file in "$LATEST_BACKUP/hospitals"/*; do
            if [ -f "$file" ]; then
                filename=$(basename "$file")
                if [ ! -f "storage/app/public/hospitals/$filename" ]; then
                    cp "$file" "storage/app/public/hospitals/" 2>/dev/null && restored_count=$((restored_count + 1)) || true
                fi
            fi
        done
        if [ $restored_count -gt 0 ]; then
            echo "✅ Restored $restored_count hospital file(s)"
            restored_total=$((restored_total + restored_count))
        fi
    fi
    
    # Restore references
    if [ -d "$LATEST_BACKUP/references" ] && [ "$(ls -A "$LATEST_BACKUP/references" 2>/dev/null)" ]; then
        restored_count=0
        for file in "$LATEST_BACKUP/references"/*; do
            if [ -f "$file" ]; then
                filename=$(basename "$file")
                if [ ! -f "storage/app/public/references/$filename" ]; then
                    cp "$file" "storage/app/public/references/" 2>/dev/null && restored_count=$((restored_count + 1)) || true
                fi
            fi
        done
        if [ $restored_count -gt 0 ]; then
            echo "✅ Restored $restored_count reference file(s)"
            restored_total=$((restored_total + restored_count))
        fi
    fi
    
    if [ $restored_total -eq 0 ]; then
        echo "ℹ️  All files already exist, no restore needed"
    fi
else
    echo "ℹ️  No backup found to restore"
fi

# Ensure .gitkeep exists
touch storage/app/public/hospitals/.gitkeep
touch storage/app/public/references/.gitkeep

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>/dev/null || php artisan storage:link

# Set permissions
chmod -R 775 storage/app/public/hospitals 2>/dev/null || true
chmod -R 775 storage/app/public/references 2>/dev/null || true
```

**Cara 2: Minimal Setup (Jika Tidak Pakai Backup)**

Jika tidak perlu backup/restore, cukup tambahkan di **After Deploy Hook**:

```bash
mkdir -p storage/app/public/references
touch storage/app/public/references/.gitkeep
php artisan storage:link --force
chmod -R 775 storage/app/public/references
```

---

## Setup Storage Link

### 3.1 Manual Setup (Setelah Deploy Pertama)

Setelah deploy pertama kali, jalankan via SSH:

1. **Akses SSH Terminal:**
   - Di Laravel Cloud dashboard, klik tab **"SSH"** atau **"Terminal"**

2. **Jalankan Command:**
   ```bash
   php artisan storage:link
   ```

3. **Verifikasi:**
   ```bash
   ls -la public/storage
   ```
   
   Seharusnya menampilkan symlink ke `storage/app/public`

### 3.2 Otomatis via After Deploy Hook

Jika sudah setup After Deploy Hook seperti di atas, storage link akan dibuat otomatis setiap deploy.

---

## Verifikasi

### 4.1 Test Upload Gambar

1. Login ke aplikasi
2. Buka halaman **References** → **Tambah Referensi**
3. Upload gambar JPEG/PNG
4. Simpan referensi
5. Cek apakah gambar tampil di halaman detail

### 4.2 Test Storage Link

Akses URL gambar langsung di browser:
```
https://your-app.laravelcloud.com/storage/references/nama-file.jpg
```

Jika gambar tampil, berarti storage link sudah benar.

### 4.3 Cek Folder di Server

Via SSH, cek apakah folder dan file ada:

```bash
ls -la storage/app/public/references
ls -la public/storage/references
```

---

## Troubleshooting

### 5.1 Gambar Tidak Tampil (404 Error)

**Penyebab:** Storage link belum dibuat atau salah.

**Solusi:**
```bash
# Hapus link lama (jika ada)
rm public/storage

# Buat link baru
php artisan storage:link

# Verifikasi
ls -la public/storage
```

### 5.2 Upload Gagal (Permission Denied)

**Penyebab:** Permission folder tidak writable.

**Solusi:**
```bash
chmod -R 775 storage/app/public/references
chown -R www-data:www-data storage/app/public/references
```

### 5.3 Gambar Hilang Setelah Deploy

**Penyebab:** Folder storage terhapus saat deploy.

**Solusi:**
1. Pastikan file `.gitkeep` ada di `storage/app/public/references/`
2. Setup Before Deploy Hook untuk backup (lihat `laravel-cloud-hooks.sh`)
3. Setup After Deploy Hook untuk restore (sudah include di script di atas)

### 5.4 Storage Link Tidak Terbuat Otomatis

**Penyebab:** After Deploy Hook belum di-setup atau error.

**Solusi:**
1. Cek log deployment di Laravel Cloud dashboard
2. Pastikan script After Deploy Hook sudah benar
3. Buat storage link manual via SSH:
   ```bash
   php artisan storage:link
   ```

### 5.5 Gambar Tampil di Local tapi Tidak di Production

**Penyebab:** 
- Storage link belum dibuat di production
- Path berbeda antara local dan production

**Solusi:**
1. Pastikan storage link dibuat di production:
   ```bash
   php artisan storage:link
   ```
2. Gunakan helper `Storage::disk('public')->url()` di view (sudah digunakan di code)

---

## Checklist Deploy

Sebelum deploy, pastikan:

- [ ] File `.gitkeep` ada di `storage/app/public/references/`
- [ ] File `.gitkeep` ter-commit ke repository
- [ ] After Deploy Hook sudah di-setup di Laravel Cloud
- [ ] Build Command include setup storage (jika menggunakan)
- [ ] Migration sudah dijalankan (`php artisan migrate`)
- [ ] Storage link dibuat (`php artisan storage:link`)

Setelah deploy:

- [ ] Cek log deployment untuk memastikan tidak ada error
- [ ] Test upload gambar via aplikasi
- [ ] Verifikasi gambar tampil di halaman detail
- [ ] Test akses gambar langsung via URL
- [ ] Cek permission folder storage (775)

---

## Best Practices

### 6.1 Struktur Folder

```
storage/
├── app/
│   └── public/
│       ├── hospitals/          # Logo hospital
│       │   └── .gitkeep
│       └── references/          # Gambar references
│           └── .gitkeep
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/
```

### 6.2 Upload Image

**Di Controller (sudah diimplementasi):**
```php
if ($request->hasFile('image')) {
    $image = $request->file('image');
    $imageName = Str::slug($data['title']) . '-' . time() . '.' . $image->getClientOriginalExtension();
    Storage::disk('public')->makeDirectory('references');
    $image->storeAs('references', $imageName, 'public');
    $data['image_path'] = 'references/' . $imageName;
}
```

### 6.3 Display Image

**Di Blade Template (sudah diimplementasi):**
```blade
@if($reference->image_path)
    <img src="{{ Storage::disk('public')->url($reference->image_path) }}" 
         alt="{{ $reference->title }}">
@endif
```

---

## Quick Fix Commands

Jika ada masalah, jalankan command berikut via SSH:

```bash
# 1. Buat folder yang diperlukan
mkdir -p storage/app/public/references
touch storage/app/public/references/.gitkeep

# 2. Set permission
chmod -R 775 storage/app/public/references

# 3. Buat storage link
php artisan storage:link --force

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
ls -la storage/app/public/references
```

---

**Selamat! Setup upload gambar references sudah selesai! 🎉**

Jika masih ada masalah, cek bagian [Troubleshooting](#troubleshooting) atau hubungi Laravel Cloud support.

