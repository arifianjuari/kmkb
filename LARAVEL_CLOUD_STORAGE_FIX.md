# Solusi Definitif: Logo Tenant Hilang di Laravel Cloud

## Masalah

Logo tenant hilang setiap kali ada deployment di Laravel Cloud, meskipun sudah ada script backup/restore.

## Root Cause

Laravel Cloud melakukan **fresh clone** setiap deployment, yang menghapus semua file yang tidak di-track oleh Git, termasuk file di `storage/app/public/hospitals/`.

Script backup/restore tidak bekerja karena:

1. Script mungkin tidak dijalankan di Laravel Cloud
2. Folder `storage/app` juga ikut terhapus saat fresh clone
3. Backup location (`storage/app/backup_*`) juga ikut terhapus

## Solusi Definitif

### Opsi 1: Gunakan Laravel Cloud Persistent Storage (RECOMMENDED)

Laravel Cloud menyediakan persistent storage yang tidak terhapus saat deployment.

#### Setup di Laravel Cloud Dashboard:

1. **Buka Laravel Cloud Dashboard**
2. **Pilih Environment** → **Settings** → **Storage**
3. **Mount Persistent Storage:**
   - Path: `/storage/app/public/hospitals`
   - Size: Sesuai kebutuhan (minimal 1GB)

Dengan ini, folder `storage/app/public/hospitals` akan **persisten** dan tidak terhapus saat deployment.

### Opsi 2: Gunakan Deployment Hooks (Alternative)

Jika persistent storage tidak tersedia, gunakan deployment hooks:

1. **Buka Laravel Cloud Dashboard**
2. **Pilih Environment** → **Settings** → **Deployment Hooks**
3. **Tambahkan di "Before Deploy":**

   ```bash
   # Backup files ke lokasi yang persisten (di luar project directory)
   if [ -d "storage/app/public/hospitals" ]; then
       mkdir -p /home/forge/storage_backup
       cp -r storage/app/public/hospitals/* /home/forge/storage_backup/ 2>/dev/null || true
   fi
   ```

4. **Tambahkan di "After Deploy":**
   ```bash
   # Restore files
   mkdir -p storage/app/public/hospitals
   if [ -d "/home/forge/storage_backup" ]; then
       cp -r /home/forge/storage_backup/* storage/app/public/hospitals/ 2>/dev/null || true
   fi
   php artisan storage:link
   chmod -R 775 storage/app/public
   ```

### Opsi 3: Gunakan External Storage (S3, etc.)

Gunakan S3 atau storage eksternal untuk file upload:

1. **Update `.env`:**

   ```env
   FILESYSTEM_DISK=s3
   AWS_ACCESS_KEY_ID=your_key
   AWS_SECRET_ACCESS_KEY=your_secret
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=your-bucket
   ```

2. **Update `config/filesystems.php`:**

   ```php
   'disks' => [
       'public' => [
           'driver' => 's3',
           'key' => env('AWS_ACCESS_KEY_ID'),
           'secret' => env('AWS_SECRET_ACCESS_KEY'),
           'region' => env('AWS_DEFAULT_REGION'),
           'bucket' => env('AWS_BUCKET'),
           'url' => env('AWS_URL'),
           'root' => 'hospitals',
       ],
   ],
   ```

3. **Update Controller:**
   ```php
   // Ganti Storage::disk('public') dengan Storage::disk('s3')
   Storage::disk('s3')->put('hospitals/' . $logoName, file_get_contents($logo));
   ```

## Rekomendasi

**Gunakan Opsi 1 (Persistent Storage)** karena:

- ✅ Paling sederhana
- ✅ Tidak perlu script tambahan
- ✅ File benar-benar persisten
- ✅ Tidak ada risiko kehilangan data

## Setup Persistent Storage di Laravel Cloud

### Langkah-langkah:

1. **Login ke Laravel Cloud Dashboard**
2. **Pilih Environment** (Production/Staging)
3. **Klik "Settings"** → **"Storage"**
4. **Klik "Add Storage"**
5. **Isi form:**
   - **Path**: `/storage/app/public/hospitals`
   - **Size**: `1` GB (atau sesuai kebutuhan)
   - **Description**: `Hospital logos storage`
6. **Klik "Save"**

### Verifikasi:

Setelah setup, folder `storage/app/public/hospitals` akan:

- ✅ **Tidak terhapus** saat deployment
- ✅ **Persisten** di semua deployment
- ✅ **Otomatis tersedia** setelah deployment

## Troubleshooting

### Logo Masih Hilang?

1. **Cek apakah persistent storage sudah di-mount:**

   ```bash
   # SSH ke server
   df -h | grep hospitals
   # Harus menampilkan mount point
   ```

2. **Cek permission:**

   ```bash
   ls -la storage/app/public/hospitals/
   chmod -R 775 storage/app/public/hospitals
   ```

3. **Cek symlink:**
   ```bash
   php artisan storage:link --force
   ```

### Persistent Storage Tidak Tersedia?

Jika Laravel Cloud tidak menyediakan persistent storage, gunakan **Opsi 2** atau **Opsi 3**.

## Catatan Penting

- **File logo TIDAK di-commit ke Git** (benar)
- **Folder structure tetap di-track** (dengan `.gitkeep`)
- **Dengan persistent storage, file logo akan tetap ada** setelah deployment
- **Backup manual tetap disarankan** untuk file penting
