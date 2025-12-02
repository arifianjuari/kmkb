# Solusi: Logo Tenant Hilang Setelah Deployment

## Masalah

Setiap kali ada commit/push baru ke GitHub dan deployment dilakukan, logo tenant yang sudah ada di server production menjadi hilang dan harus di-upload ulang.

## Penyebab

1. **Folder `storage/app/public` di-ignore oleh Git** (benar, karena file upload tidak boleh di-commit)
2. **Laravel Cloud melakukan fresh clone** saat deployment, yang menghapus folder yang tidak di-track oleh Git
3. **Tidak ada mekanisme backup/restore** file yang sudah ada sebelum deployment

## Solusi yang Sudah Diterapkan

### 1. File `.gitkeep` untuk Memastikan Folder Tetap Ada

- File `.gitkeep` di `storage/app/public/hospitals/` memastikan folder tetap ada di repository
- Folder structure tidak akan hilang setelah `git pull` atau deployment

### 2. Deployment Script dengan Backup/Restore

Script `laravel-cloud-deploy.sh` sudah diperbarui untuk:

- **Backup file yang sudah ada** sebelum deployment
- **Restore file setelah deployment** (mencegah logo hilang)
- **Memastikan folder structure tetap ada**

### 3. Update `.gitignore`

`.gitignore` sudah diperbarui agar:

- Folder structure tetap di-track (`.gitkeep` dan `README.md`)
- File upload tetap di-ignore (benar)

## Cara Kerja

### Saat Deployment:

1. **Backup Phase:**

   ```bash
   # Script mem-backup file yang sudah ada
   cp -r storage/app/public/hospitals /tmp/storage_backup/
   ```

2. **Deployment Phase:**

   ```bash
   # Git pull/clone menghapus folder yang tidak di-track
   # Tapi folder structure tetap ada karena .gitkeep
   ```

3. **Restore Phase:**
   ```bash
   # Script mem-restore file yang sudah di-backup
   cp /tmp/storage_backup/hospitals/* storage/app/public/hospitals/
   ```

## Verifikasi

Setelah deployment, pastikan:

1. **Folder structure ada:**

   ```bash
   ls -la storage/app/public/hospitals/
   # Harus menampilkan .gitkeep dan file logo yang sudah ada
   ```

2. **Symlink ada:**

   ```bash
   ls -la public/storage
   # Harus menampilkan symlink ke storage/app/public
   ```

3. **File logo bisa diakses:**
   - Buka aplikasi di browser
   - Cek apakah logo tenant masih muncul
   - Jika tidak, cek permission folder

## Troubleshooting

### Logo Masih Hilang Setelah Deployment?

1. **Cek apakah script deployment dijalankan:**

   ```bash
   # Di Laravel Cloud, pastikan script laravel-cloud-deploy.sh dijalankan
   ```

2. **Cek permission folder:**

   ```bash
   chmod -R 775 storage/app/public
   chown -R www-data:www-data storage/app/public
   ```

3. **Cek apakah symlink ada:**

   ```bash
   php artisan storage:link
   ```

4. **Manual backup sebelum deployment:**
   ```bash
   # Backup manual jika diperlukan
   tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public/hospitals/
   ```

### File Logo Tidak Bisa Diakses?

1. **Cek permission:**

   ```bash
   ls -la storage/app/public/hospitals/
   chmod 644 storage/app/public/hospitals/*.png
   ```

2. **Cek symlink:**

   ```bash
   php artisan storage:link --force
   ```

3. **Cek web server config:**
   - Pastikan web server bisa mengakses folder `storage/app/public`
   - Pastikan symlink `public/storage` tidak diblokir

## Catatan Penting

- **File logo TIDAK di-commit ke Git** (benar, karena data user)
- **Folder structure TETAP di-commit** (dengan `.gitkeep`)
- **Backup/restore otomatis** dilakukan oleh deployment script
- **Selalu backup manual** sebelum deployment besar jika ada file penting

## File yang Terlibat

- `laravel-cloud-deploy.sh` - Script deployment dengan backup/restore
- `storage/app/public/hospitals/.gitkeep` - Memastikan folder tetap ada
- `.gitignore` - Konfigurasi untuk mengabaikan file upload tapi track folder structure
- `storage/app/public/README.md` - Dokumentasi folder storage







