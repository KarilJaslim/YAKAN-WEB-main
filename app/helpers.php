<?php

if (!function_exists('storageAsset')) {
    /**
     * Get asset URL that works with both old (storage/) and new (uploads/) paths
     * 
     * @param string|null $path
     * @return string
     */
    function storageAsset($path)
    {
        if (empty($path)) {
            return '';
        }
        
        // If it's already a full URL or data URI, return as is
        if (str_starts_with($path, 'http') || str_starts_with($path, 'data:')) {
            return $path;
        }
        
        // Check if file exists in new uploads directory
        if (file_exists(public_path('uploads/' . $path))) {
            return asset('uploads/' . $path);
        }
        
        // Fallback to storage directory (for old files)
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        
        // Default to uploads (for newly created paths)
        return asset('uploads/' . $path);
    }
}
