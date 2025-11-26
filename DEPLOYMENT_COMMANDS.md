# Laravel Cloud Deployment Commands

## Build Command

```bash
# Install Composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Install NPM dependencies
npm ci --audit false

# Build frontend assets
npm run build

# Ensure required directories exist and are writable
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public/hospitals storage/app/public/references
chmod -R 775 bootstrap/cache storage

# Create storage link
php artisan storage:link

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Backup local storage files (for fallback/migration purposes)
# Note: If using Object Storage, files will be stored in S3/R2, but we still backup local files
BACKUP_BASE="/home/forge/storage_backup"
BACKUP_DIR="$BACKUP_BASE/uploads_$(date +%Y%m%d_%H%M%S)"

if [ -d "storage/app/public/hospitals" ] && [ "$(ls -A storage/app/public/hospitals 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up hospital logos..."
    mkdir -p "$BACKUP_DIR/hospitals"
    find storage/app/public/hospitals -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/hospitals/" \; 2>/dev/null || true
    if [ "$(ls -A "$BACKUP_DIR/hospitals" 2>/dev/null)" ]; then
        echo "✅ Hospitals backup created: $(ls -1 "$BACKUP_DIR/hospitals" | wc -l) file(s)"
    fi
fi

if [ -d "storage/app/public/references" ] && [ "$(ls -A storage/app/public/references 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up reference images..."
    mkdir -p "$BACKUP_DIR/references"
    find storage/app/public/references -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/references/" \; 2>/dev/null || true
    if [ "$(ls -A "$BACKUP_DIR/references" 2>/dev/null)" ]; then
        echo "✅ References backup created: $(ls -1 "$BACKUP_DIR/references" | wc -l) file(s)"
    fi
fi

# Keep only last 5 backups
if [ -d "$BACKUP_BASE" ]; then
    ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | tail -n +6 | xargs rm -rf 2>/dev/null || true
fi
```

## Deploy Command

```bash
php artisan migrate --force
mkdir -p storage/app/public/hospitals storage/app/public/references
touch storage/app/public/hospitals/.gitkeep storage/app/public/references/.gitkeep
php artisan storage:link --force 2>/dev/null || php artisan storage:link
chmod -R 775 storage/app/public/hospitals storage/app/public/references 2>/dev/null || true
BACKUP_BASE="/home/forge/storage_backup"
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | head -1)
if [ -n "$LATEST_BACKUP" ] && [ -d "$LATEST_BACKUP" ]; then
    [ -d "$LATEST_BACKUP/hospitals" ] && [ "$(ls -A "$LATEST_BACKUP/hospitals" 2>/dev/null)" ] && cp -n "$LATEST_BACKUP/hospitals"/* storage/app/public/hospitals/ 2>/dev/null || true
    [ -d "$LATEST_BACKUP/references" ] && [ "$(ls -A "$LATEST_BACKUP/references" 2>/dev/null)" ] && cp -n "$LATEST_BACKUP/references"/* storage/app/public/references/ 2>/dev/null || true
fi
```

## Catatan Penting

### Object Storage (S3/R2 Bucket)

**Jika credentials AWS sudah dikonfigurasi di Laravel Cloud:**

- ✅ File baru (hospitals & references) akan **otomatis tersimpan di Object Storage bucket**
- ✅ File di bucket **tidak akan hilang** saat deployment (bucket persisten)
- ✅ Tidak perlu khawatir file hilang karena bucket dikelola oleh Laravel Cloud
- ✅ Backup/restore local storage hanya untuk **data lama** yang mungkin masih ada di local storage

**Cara kerja:**

1. Aplikasi otomatis mendeteksi credentials AWS dari environment variables
2. Jika `AWS_ACCESS_KEY_ID` tersedia → menggunakan disk `uploads` (S3/R2)
3. Jika tidak ada → fallback ke disk `public` (local storage)

**Setup Object Storage di Laravel Cloud:**

1. Buka Laravel Cloud Dashboard → Environment → Infrastructure
2. Klik "Add bucket" → Pilih "Laravel Object Storage"
3. Set bucket name, visibility (Public untuk gambar), dan disk name
4. Credentials akan otomatis di-set oleh Laravel Cloud
5. Tidak perlu konfigurasi tambahan di deployment commands

### Backup Local Storage

Backup/restore local storage masih diperlukan untuk:

- **Data lama** yang mungkin masih ada di local storage sebelum migrasi ke Object Storage
- **Fallback** jika Object Storage tidak tersedia (temporary)
- **Migrasi data** dari local ke Object Storage (one-time)

**Catatan:** Jika sudah sepenuhnya menggunakan Object Storage, backup local storage hanya untuk safety net. File baru tidak akan tersimpan di local storage.

### Deployment Commands

1. **Backup Location**: Backup disimpan di `/home/forge/storage_backup` dan hanya menyimpan 5 backup terakhir.

2. **Storage Link**: `php artisan storage:link` masih diperlukan untuk:

   - Fallback jika Object Storage tidak tersedia
   - Mengakses file lama yang masih di local storage

3. **Permissions**: Pastikan permissions `775` untuk folder storage agar aplikasi bisa menulis file (jika fallback ke local storage).

4. **Storage Directories**: Direktori `storage/app/public/hospitals` dan `storage/app/public/references` tetap dibuat untuk:
   - Fallback jika Object Storage tidak tersedia
   - Menyimpan file lama sebelum migrasi ke Object Storage
