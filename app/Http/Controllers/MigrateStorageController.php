<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MigrateStorageController extends Controller
{
    /**
     * Show migration page
     */
    public function index()
    {
        // Only allow authenticated admin users
        if (!Auth::check() || !Auth::user()->can('viewAny', \App\Models\Hospital::class)) {
            abort(403);
        }

        return view('migrate-storage');
    }

    /**
     * Migrate files to Object Storage via web interface
     * 
     * IMPORTANT: Remove this route after migration is complete!
     */
    public function migrate(Request $request)
    {
        // Only allow authenticated admin users
        if (!Auth::check() || !Auth::user()->can('viewAny', \App\Models\Hospital::class)) {
            abort(403);
        }

        if (!env('AWS_ACCESS_KEY_ID')) {
            return response()->json([
                'success' => false,
                'message' => 'Object Storage belum dikonfigurasi. Pastikan credentials AWS sudah di-set.'
            ], 400);
        }

        $results = [
            'hospitals' => ['migrated' => 0, 'failed' => 0, 'skipped' => 0],
            'references' => ['migrated' => 0, 'failed' => 0, 'skipped' => 0],
            'errors' => []
        ];

        // Migrate hospitals
        $hospitals = Hospital::whereNotNull('logo_path')->get();
        foreach ($hospitals as $hospital) {
            $result = $this->migrateFile($hospital->logo_path, 'hospital');
            if ($result === 'migrated') {
                $results['hospitals']['migrated']++;
            } elseif ($result === 'failed') {
                $results['hospitals']['failed']++;
            } else {
                $results['hospitals']['skipped']++;
            }
        }

        // Migrate references
        $references = Reference::whereNotNull('image_path')->get();
        foreach ($references as $reference) {
            $result = $this->migrateFile($reference->image_path, 'reference');
            if ($result === 'migrated') {
                $results['references']['migrated']++;
            } elseif ($result === 'failed') {
                $results['references']['failed']++;
            } else {
                $results['references']['skipped']++;
            }
        }

        $totalMigrated = $results['hospitals']['migrated'] + $results['references']['migrated'];
        $totalFailed = $results['hospitals']['failed'] + $results['references']['failed'];
        $totalSkipped = $results['hospitals']['skipped'] + $results['references']['skipped'];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Migrasi selesai!',
                'results' => $results,
                'summary' => [
                    'migrated' => $totalMigrated,
                    'failed' => $totalFailed,
                    'skipped' => $totalSkipped
                ]
            ]);
        }

        return redirect()->route('migrate-storage.index')
            ->with('success', "Migrasi selesai! Migrated: {$totalMigrated}, Failed: {$totalFailed}, Skipped: {$totalSkipped}")
            ->with('results', $results);
    }

    private function migrateFile(string $path, string $type): string
    {
        // Skip jika sudah absolute URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return 'skipped';
        }
        
        // Normalize path
        $normalizedPath = $path;
        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            $normalizedPath = ltrim(str_after($path, '/storage/'), '/');
        }
        
        // Cek apakah file ada di local storage
        if (!Storage::disk('public')->exists($normalizedPath)) {
            return 'failed';
        }
        
        // Cek apakah sudah ada di Object Storage
        if (Storage::disk('uploads')->exists($normalizedPath)) {
            return 'skipped';
        }
        
        // Upload ke Object Storage
        try {
            $content = Storage::disk('public')->get($normalizedPath);
            Storage::disk('uploads')->put($normalizedPath, $content);
            return 'migrated';
        } catch (\Exception $e) {
            return 'failed';
        }
    }
}

