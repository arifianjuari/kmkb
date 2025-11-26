# Migrasi File ke Object Storage

Script ini untuk memigrasikan file dari local storage ke Object Storage (S3/R2 bucket).

## Cara Menggunakan

### 1. Jalankan di Laravel Cloud SSH Terminal

```bash
php artisan tinker
```

### 2. Copy dan paste script berikut:

```php
use Illuminate\Support\Facades\Storage;

// Cek apakah Object Storage tersedia
$hasS3 = env('AWS_ACCESS_KEY_ID');
if (!$hasS3) {
    echo "❌ Object Storage belum dikonfigurasi. Pastikan credentials AWS sudah di-set.\n";
    exit;
}

echo "🚀 Memulai migrasi file ke Object Storage...\n\n";

// Migrate hospitals
$hospitals = \App\Models\Hospital::whereNotNull('logo_path')->get();
$migrated = 0;
$failed = 0;

foreach ($hospitals as $hospital) {
    $path = $hospital->logo_path;
    
    // Skip jika sudah absolute URL (sudah di Object Storage)
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        continue;
    }
    
    // Normalize path
    $normalizedPath = $path;
    if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
        $normalizedPath = ltrim(str_after($path, '/storage/'), '/');
    }
    
    // Cek apakah file ada di local storage
    if (!Storage::disk('public')->exists($normalizedPath)) {
        echo "⚠️  File tidak ditemukan: {$path}\n";
        $failed++;
        continue;
    }
    
    // Upload ke Object Storage
    try {
        $content = Storage::disk('public')->get($normalizedPath);
        Storage::disk('uploads')->put($normalizedPath, $content);
        
        // Update path di database (optional, karena helper function sudah handle)
        // $hospital->update(['logo_path' => $normalizedPath]);
        
        echo "✅ Migrated: {$path}\n";
        $migrated++;
    } catch (\Exception $e) {
        echo "❌ Error migrating {$path}: {$e->getMessage()}\n";
        $failed++;
    }
}

// Migrate references
$references = \App\Models\Reference::whereNotNull('image_path')->get();
foreach ($references as $reference) {
    $path = $reference->image_path;
    
    // Skip jika sudah absolute URL
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        continue;
    }
    
    // Normalize path
    $normalizedPath = $path;
    if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
        $normalizedPath = ltrim(str_after($path, '/storage/'), '/');
    }
    
    // Cek apakah file ada di local storage
    if (!Storage::disk('public')->exists($normalizedPath)) {
        echo "⚠️  File tidak ditemukan: {$path}\n";
        $failed++;
        continue;
    }
    
    // Upload ke Object Storage
    try {
        $content = Storage::disk('public')->get($normalizedPath);
        Storage::disk('uploads')->put($normalizedPath, $content);
        
        echo "✅ Migrated: {$path}\n";
        $migrated++;
    } catch (\Exception $e) {
        echo "❌ Error migrating {$path}: {$e->getMessage()}\n";
        $failed++;
    }
}

echo "\n📊 Summary:\n";
echo "✅ Migrated: {$migrated}\n";
echo "❌ Failed: {$failed}\n";
echo "\n✨ Migrasi selesai!\n";
```

### 3. Atau buat Artisan Command

Buat file `app/Console/Commands/MigrateToObjectStorage.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\Hospital;
use App\Models\Reference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateToObjectStorage extends Command
{
    protected $signature = 'storage:migrate-to-s3';
    protected $description = 'Migrate files from local storage to Object Storage';

    public function handle()
    {
        if (!env('AWS_ACCESS_KEY_ID')) {
            $this->error('Object Storage belum dikonfigurasi. Pastikan credentials AWS sudah di-set.');
            return 1;
        }

        $this->info('🚀 Memulai migrasi file ke Object Storage...');
        
        $migrated = 0;
        $failed = 0;

        // Migrate hospitals
        $hospitals = Hospital::whereNotNull('logo_path')->get();
        $this->info("Found {$hospitals->count()} hospitals with logos");
        
        foreach ($hospitals as $hospital) {
            $result = $this->migrateFile($hospital->logo_path, 'hospital');
            if ($result) {
                $migrated++;
            } else {
                $failed++;
            }
        }

        // Migrate references
        $references = Reference::whereNotNull('image_path')->get();
        $this->info("Found {$references->count()} references with images");
        
        foreach ($references as $reference) {
            $result = $this->migrateFile($reference->image_path, 'reference');
            if ($result) {
                $migrated++;
            } else {
                $failed++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("✅ Migrated: {$migrated}");
        $this->info("❌ Failed: {$failed}");
        
        return 0;
    }

    private function migrateFile(string $path, string $type): bool
    {
        // Skip jika sudah absolute URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return true;
        }
        
        // Normalize path
        $normalizedPath = $path;
        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            $normalizedPath = ltrim(str_after($path, '/storage/'), '/');
        }
        
        // Cek apakah file ada di local storage
        if (!Storage::disk('public')->exists($normalizedPath)) {
            $this->warn("⚠️  File tidak ditemukan: {$path}");
            return false;
        }
        
        // Cek apakah sudah ada di Object Storage
        if (Storage::disk('uploads')->exists($normalizedPath)) {
            $this->info("✓ Already in Object Storage: {$path}");
            return true;
        }
        
        // Upload ke Object Storage
        try {
            $content = Storage::disk('public')->get($normalizedPath);
            Storage::disk('uploads')->put($normalizedPath, $content);
            $this->info("✅ Migrated: {$path}");
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Error migrating {$path}: {$e->getMessage()}");
            return false;
        }
    }
}
```

Kemudian jalankan:
```bash
php artisan storage:migrate-to-s3
```

## Setelah Migrasi

Setelah semua file berhasil dimigrasi ke Object Storage:
1. File baru akan otomatis tersimpan di Object Storage
2. File tidak akan hilang lagi saat deploy
3. Backup local storage tidak diperlukan lagi (tapi tetap ada sebagai safety net)

## Verifikasi

Cek apakah file sudah di Object Storage:
```php
php artisan tinker
```

```php
use Illuminate\Support\Facades\Storage;

// Cek hospital logo
$hospital = \App\Models\Hospital::first();
$path = $hospital->logo_path;
echo Storage::disk('uploads')->exists($path) ? 'EXISTS in S3' : 'NOT in S3';
echo "\n";
echo Storage::disk('uploads')->url($path);
```

