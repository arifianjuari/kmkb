<?php

namespace App\Console\Commands;

use App\Models\Hospital;
use App\Models\Reference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateToObjectStorage extends Command
{
    protected $signature = 'storage:migrate-to-s3 {--dry-run : Run without actually migrating files}';
    protected $description = 'Migrate files from local storage to Object Storage';

    public function handle()
    {
        if (!env('AWS_ACCESS_KEY_ID')) {
            $this->error('❌ Object Storage belum dikonfigurasi. Pastikan credentials AWS sudah di-set.');
            $this->info('💡 Setup Object Storage di Laravel Cloud Dashboard → Environment → Infrastructure → Add bucket');
            return 1;
        }

        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - Tidak akan memigrasikan file, hanya menampilkan file yang akan dimigrasi');
        } else {
            $this->info('🚀 Memulai migrasi file ke Object Storage...');
        }
        
        $migrated = 0;
        $failed = 0;
        $skipped = 0;

        // Migrate hospitals
        $hospitals = Hospital::whereNotNull('logo_path')->get();
        $this->info("\n📋 Found {$hospitals->count()} hospitals with logos");
        
        foreach ($hospitals as $hospital) {
            $result = $this->migrateFile($hospital->logo_path, 'hospital', $isDryRun);
            if ($result === 'migrated') {
                $migrated++;
            } elseif ($result === 'failed') {
                $failed++;
            } else {
                $skipped++;
            }
        }

        // Migrate references
        $references = Reference::whereNotNull('image_path')->get();
        $this->info("\n📋 Found {$references->count()} references with images");
        
        foreach ($references as $reference) {
            $result = $this->migrateFile($reference->image_path, 'reference', $isDryRun);
            if ($result === 'migrated') {
                $migrated++;
            } elseif ($result === 'failed') {
                $failed++;
            } else {
                $skipped++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("✅ Migrated: {$migrated}");
        $this->info("⏭️  Skipped: {$skipped}");
        $this->info("❌ Failed: {$failed}");
        
        if ($migrated > 0 && !$isDryRun) {
            $this->info("\n✨ Migrasi selesai! File sekarang tersimpan di Object Storage dan tidak akan hilang saat deploy.");
        }
        
        return 0;
    }

    private function migrateFile(string $path, string $type, bool $isDryRun): string
    {
        // Skip jika sudah absolute URL (sudah di Object Storage)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $this->line("⏭️  Already in Object Storage: {$path}");
            return 'skipped';
        }
        
        // Normalize path
        $normalizedPath = $path;
        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            $normalizedPath = ltrim(str_after($path, '/storage/'), '/');
        }
        
        // Cek apakah file ada di local storage
        if (!Storage::disk('public')->exists($normalizedPath)) {
            $this->warn("⚠️  File tidak ditemukan di local storage: {$path}");
            return 'failed';
        }
        
        // Cek apakah sudah ada di Object Storage
        if (Storage::disk('uploads')->exists($normalizedPath)) {
            $this->line("✓ Already in Object Storage: {$path}");
            return 'skipped';
        }
        
        if ($isDryRun) {
            $this->info("📤 Would migrate: {$path}");
            return 'migrated';
        }
        
        // Upload ke Object Storage
        try {
            $content = Storage::disk('public')->get($normalizedPath);
            Storage::disk('uploads')->put($normalizedPath, $content);
            $this->info("✅ Migrated: {$path}");
            return 'migrated';
        } catch (\Exception $e) {
            $this->error("❌ Error migrating {$path}: {$e->getMessage()}");
            return 'failed';
        }
    }
}

