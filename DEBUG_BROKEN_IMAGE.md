# Debug Broken Image - Object Storage

## Langkah Debugging

### 1. Cek Konfigurasi Disk

Jalankan di tinker atau buat route debug:

```php
php artisan tinker
```

```php
// Cek disk yang digunakan
$disk = uploads_disk();
echo "Disk: {$disk}\n";

// Cek konfigurasi disk
$config = config("filesystems.disks.{$disk}");
print_r($config);

// Cek apakah driver adalah S3
$driver = $config['driver'] ?? null;
echo "Driver: {$driver}\n";

// Cek credentials
$key = $config['key'] ?? config('filesystems.disks.s3.key') ?? env('AWS_ACCESS_KEY_ID');
echo "AWS Key: " . (!empty($key) ? "SET" : "NOT SET") . "\n";
```

### 2. Cek File di Storage

```php
// Ambil reference dengan image
$ref = \App\Models\Reference::whereNotNull('image_path')->first();
if ($ref) {
    echo "Image Path: {$ref->image_path}\n";

    $disk = uploads_disk();
    $exists = \Illuminate\Support\Facades\Storage::disk($disk)->exists($ref->image_path);
    echo "Exists in disk '{$disk}': " . ($exists ? "YES" : "NO") . "\n";

    // Cek di local storage juga
    $existsLocal = \Illuminate\Support\Facades\Storage::disk('public')->exists($ref->image_path);
    echo "Exists in local 'public': " . ($existsLocal ? "YES" : "NO") . "\n";

    // Cek URL yang dihasilkan
    $url = storage_url($ref->image_path);
    echo "Generated URL: {$url}\n";
}
```

### 3. Cek URL yang Dihasilkan

Buka browser console dan cek:

- URL gambar yang dihasilkan
- Error message (404, 403, CORS, dll)
- Network tab untuk melihat request/response

### 4. Verifikasi Object Storage

```php
// Test upload ke Object Storage
$disk = uploads_disk();
$testPath = 'test/test.txt';
\Illuminate\Support\Facades\Storage::disk($disk)->put($testPath, 'test content');
$exists = \Illuminate\Support\Facades\Storage::disk($disk)->exists($testPath);
echo "Test upload: " . ($exists ? "SUCCESS" : "FAILED") . "\n";

if ($exists) {
    $url = \Illuminate\Support\Facades\Storage::disk($disk)->url($testPath);
    echo "Test URL: {$url}\n";
    // Hapus file test
    \Illuminate\Support\Facades\Storage::disk($disk)->delete($testPath);
}
```

## Kemungkinan Masalah

### 1. File Belum Dimigrasi ke Object Storage

**Gejala:** File ada di local storage tapi tidak ada di Object Storage

**Solusi:** Jalankan migrasi:

- Via web: `https://kmkb.online/migrate-storage`
- Via command: `php artisan storage:migrate-to-s3`

### 2. Disk "public" Belum Di-override oleh Laravel Cloud

**Gejala:** `config('filesystems.disks.public.driver')` masih `'local'` padahal credentials ada

**Solusi:**

- Pastikan bucket sudah di-attach ke environment dengan disk name "public"
- Clear config cache: `php artisan config:clear && php artisan config:cache`

### 3. URL Tidak Benar

**Gejala:** URL yang dihasilkan tidak mengarah ke bucket endpoint

**Solusi:**

- Cek apakah `storage_url()` menggunakan disk yang benar
- Cek apakah path sudah dinormalisasi dengan benar

### 4. CORS atau Visibility Issue

**Gejala:** File ada tapi tidak bisa diakses (403 Forbidden)

**Solusi:**

- Pastikan bucket visibility adalah "Public"
- Cek CORS policy di bucket settings

## Quick Fix

Jika masih broken setelah semua langkah di atas:

1. **Clear semua cache:**

   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

2. **Rebuild cache:**

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Verifikasi migrasi:**

   - Pastikan semua file sudah dimigrasi ke Object Storage
   - Cek apakah file ada di bucket via Laravel Cloud dashboard

4. **Test upload baru:**
   - Upload gambar baru untuk test
   - Cek apakah gambar baru bisa tampil
