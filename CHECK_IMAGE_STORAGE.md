# Checklist: Cek Image Storage

## Langkah 1: Cek Path di Database

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

## Langkah 2: Cek File di Storage

```php
$path = $ref->image_path; // contoh: 'references/image-name.jpg'

// Cek disk yang digunakan
$disk = uploads_disk();
echo "Disk: {$disk}\n";

// Cek konfigurasi disk
$config = config("filesystems.disks.{$disk}");
echo "Driver: " . ($config['driver'] ?? 'null') . "\n";

// Cek apakah file ada di disk utama
$exists = \Illuminate\Support\Facades\Storage::disk($disk)->exists($path);
echo "Exists in disk '{$disk}': " . ($exists ? "YES" : "NO") . "\n";

// Cek apakah file ada di local storage
$existsLocal = \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
echo "Exists in local 'public': " . ($existsLocal ? "YES" : "NO") . "\n";

// Cek URL yang dihasilkan
$url = storage_url($path);
echo "Generated URL: {$url}\n";
```

## Langkah 3: Test URL di Browser

1. Copy URL yang dihasilkan dari tinker
2. Buka di browser (tab baru)
3. Cek apakah:
   - File bisa diakses (200 OK)
   - File tidak bisa diakses (404 Not Found)
   - File tidak bisa diakses (403 Forbidden - CORS issue)

## Langkah 4: Cek Object Storage (jika menggunakan S3)

```php
// Test koneksi ke S3
$disk = 's3'; // atau disk yang digunakan
$testPath = 'test-connection.txt';
try {
    \Illuminate\Support\Facades\Storage::disk($disk)->put($testPath, 'test');
    echo "Upload test: SUCCESS\n";
    
    $exists = \Illuminate\Support\Facades\Storage::disk($disk)->exists($testPath);
    echo "File exists: " . ($exists ? "YES" : "NO") . "\n";
    
    if ($exists) {
        $url = \Illuminate\Support\Facades\Storage::disk($disk)->url($testPath);
        echo "Test URL: {$url}\n";
        
        // Hapus file test
        \Illuminate\Support\Facades\Storage::disk($disk)->delete($testPath);
        echo "Test file deleted\n";
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
```

## Langkah 5: Migrasi File (jika belum)

Jika file tidak ada di Object Storage tapi ada di local storage:

1. **Via Web Interface:**
   - Login sebagai admin
   - Akses: `https://kmkb.online/migrate-storage`
   - Klik "Mulai Migrasi"

2. **Via Artisan Command:**
   ```bash
   php artisan storage:migrate-to-s3
   ```

## Langkah 6: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
```

## Troubleshooting

### File tidak ada di kedua storage

**Solusi:** Upload ulang gambar atau hapus reference yang tidak memiliki file.

### File ada tapi URL salah

**Solusi:** 
- Cek konfigurasi disk di `config/filesystems.php`
- Cek apakah bucket sudah di-attach dengan benar
- Clear config cache

### File ada tapi 403 Forbidden

**Solusi:**
- Cek bucket visibility (harus Public)
- Cek CORS policy di bucket settings
- Pastikan file memiliki permission yang benar

### File ada tapi 404 Not Found

**Solusi:**
- Cek path di database vs path di storage
- Pastikan path sudah dinormalisasi dengan benar
- Cek apakah file benar-benar ada di lokasi yang diharapkan

