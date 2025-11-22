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
