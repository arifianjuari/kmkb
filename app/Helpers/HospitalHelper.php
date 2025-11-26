<?php

if (!function_exists('hospital')) {
    /**
     * Get the current hospital or hospital property.
     *
     * @param string|null $property
     * @return mixed
     */
    function hospital($property = null)
    {
        // Get the hospital ID from session or user
        $hospitalId = session('hospital_id', auth()->user()->hospital_id ?? null);
        
        if (!$hospitalId) {
            return null;
        }
        
        // Get the hospital model
        $hospital = \App\Models\Hospital::find($hospitalId);
        
        if (!$hospital) {
            return null;
        }
        
        // If no property specified, return the hospital model
        if (is_null($property)) {
            return $hospital;
        }
        
        // Return the specific property
        return $hospital->{$property};
    }
}

if (!function_exists('hospital_cache_key')) {
    /**
     * Generate a cache key prefixed with the current hospital ID.
     *
     * @param string $key
     * @return string
     */
    function hospital_cache_key($key)
    {
        $hospitalId = hospital('id');
        
        if (!$hospitalId) {
            return $key;
        }
        
        return "hospital_{$hospitalId}_{$key}";
    }
}

if (!function_exists('hospital_storage_path')) {
    /**
     * Generate a storage path prefixed with the current hospital ID.
     *
     * @param string $path
     * @return string
     */
    function hospital_storage_path($path = '')
    {
        $hospitalId = hospital('id');
        
        if (!$hospitalId) {
            return storage_path($path);
        }
        
        // Create tenant-specific storage directory if it doesn't exist
        $tenantPath = "framework/tenant_{$hospitalId}";
        if (!is_dir(storage_path($tenantPath))) {
            mkdir(storage_path($tenantPath), 0755, true);
        }
        
        return storage_path("{$tenantPath}/{$path}");
    }
}

if (!function_exists('uploads_disk')) {
    /**
     * Get the disk name for uploads.
     * 
     * Jika Object Storage dikonfigurasi dengan disk name "public" di Laravel Cloud,
     * maka disk "public" akan otomatis menggunakan S3.
     * 
     * @return string
     */
    function uploads_disk(): string
    {
        // Check if AWS credentials are available
        $awsKey = config('filesystems.disks.public.key') 
                ?? config('filesystems.disks.uploads.key') 
                ?? config('filesystems.disks.s3.key') 
                ?? env('AWS_ACCESS_KEY_ID');
        
        // Cek apakah disk "public" sudah di-override menjadi S3 oleh Laravel Cloud
        $publicDiskDriver = config('filesystems.disks.public.driver');
        
        // Jika credentials ada DAN disk "public" sudah menggunakan driver 's3', gunakan 'public'
        // Jika credentials ada tapi disk "public" masih 'local', gunakan 's3' langsung
        // Jika credentials tidak ada, gunakan 'public' (local storage)
        if (!empty($awsKey)) {
            if ($publicDiskDriver === 's3') {
                return 'public'; // Disk "public" sudah di-override menjadi S3
            } else {
                return 's3'; // Gunakan disk "s3" langsung karena credentials ada
            }
        }
        
        return 'public'; // Fallback ke local storage
    }
}

if (!function_exists('storage_url')) {
    /**
     * Get the URL for a file in uploads storage.
     * 
     * Jika menggunakan Object Storage (S3), URL akan dihasilkan dari bucket endpoint.
     * Jika menggunakan local storage, URL akan dihasilkan dari APP_URL/storage.
     *
     * @param string $path
     * @return string
     */
    function storage_url(string $path): string
    {
        $disk = uploads_disk();
        
        // Normalize path - hapus prefix yang tidak perlu
        $normalizedPath = ltrim($path, '/');
        $normalizedPath = str_replace('storage/', '', $normalizedPath); // Hapus prefix storage/ jika ada
        
        try {
            $storage = \Illuminate\Support\Facades\Storage::disk($disk);
            $diskConfig = config("filesystems.disks.{$disk}");
            $isS3 = ($diskConfig['driver'] ?? null) === 's3';
            
            if ($isS3) {
                // Untuk S3, gunakan url() yang akan menghasilkan URL dari bucket endpoint
                // Cek apakah file ada di S3
                if ($storage->exists($normalizedPath)) {
                    $url = $storage->url($normalizedPath);
                    // Pastikan URL tidak mengandung double slashes
                    $url = preg_replace('#([^:])//+#', '$1/', $url);
                    return $url;
                } else {
                    // Jika file tidak ada di S3, coba cek di local storage sebagai fallback
                    $publicStorage = \Illuminate\Support\Facades\Storage::disk('public');
                    if ($publicStorage->exists($normalizedPath)) {
                        return $publicStorage->url($normalizedPath);
                    }
                    // Jika tidak ada di kedua tempat, return URL S3 anyway (mungkin file belum dimigrasi)
                    // Tapi pastikan URL valid
                    $url = $storage->url($normalizedPath);
                    $url = preg_replace('#([^:])//+#', '$1/', $url);
                    return $url;
                }
            } else {
                // Untuk local storage
                $url = $storage->url($normalizedPath);
                // Pastikan URL tidak mengandung double slashes
                $url = preg_replace('#([^:])//+#', '$1/', $url);
                return $url;
            }
        } catch (\Exception $e) {
            // Log error jika debug mode
            if (config('app.debug')) {
                \Log::warning('storage_url error', [
                    'path' => $path,
                    'normalized_path' => $normalizedPath ?? null,
                    'disk' => $disk,
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Fallback ke local storage jika error
            try {
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($normalizedPath ?? $path);
                $url = preg_replace('#([^:])//+#', '$1/', $url);
                return $url;
            } catch (\Exception $e2) {
                // Jika masih error, return path as-is dengan prefix /storage/
                return asset('storage/' . ltrim($normalizedPath ?? $path, '/'));
            }
        }
    }
}
