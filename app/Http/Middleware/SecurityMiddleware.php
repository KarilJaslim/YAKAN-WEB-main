<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for file uploads
        if ($request->hasFile('design_upload') || $request->hasFile('files')) {
            $this->validateFiles($request);
        }

        return $next($request);
    }

    /**
     * Validate uploaded files for security
     */
    private function validateFiles(Request $request)
    {
        $files = $request->hasFile('files') ? $request->file('files') : [$request->file('design_upload')];
        
        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                abort(422, 'Invalid file upload');
            }

            // Check file size (max 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                abort(422, 'File size exceeds maximum limit of 5MB');
            }

            // Check file type
            $allowedMimes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            if (!in_array($file->getMimeType(), $allowedMimes)) {
                abort(422, 'File type not allowed');
            }

            // Scan for malicious content
            if ($this->scanFileForMalware($file)) {
                abort(422, 'Security scan detected potential threats in the file');
            }

            // Sanitize filename
            $originalName = $file->getClientOriginalName();
            $sanitizedName = $this->sanitizeFilename($originalName);
            
            // Update the file with sanitized name
            $file->originalName = $sanitizedName;
        }
    }

    /**
     * Basic malware scanning (simplified - in production, use a proper antivirus solution)
     */
    private function scanFileForMalware($file)
    {
        // For demonstration purposes, this is a basic check
        // In production, integrate with ClamAV or similar antivirus solution
        
        $content = file_get_contents($file->getPathname());
        
        // Check for common malware signatures
        $malwareSignatures = [
            '<?php',
            'eval(',
            'exec(',
            'system(',
            'shell_exec(',
            'passthru(',
            'base64_decode(',
            'javascript:',
            '<script',
            'onerror=',
            'onload='
        ];

        foreach ($malwareSignatures as $signature) {
            if (stripos($content, $signature) !== false) {
                Log::warning('Potential malware detected in file upload', [
                    'filename' => $file->getClientOriginalName(),
                    'signature' => $signature
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize filename to prevent directory traversal and other attacks
     */
    private function sanitizeFilename($filename)
    {
        // Remove path information
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Limit filename length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($basename, 0, 250 - strlen($extension)) . '.' . $extension;
        }
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . time() . '.dat';
        }
        
        return $filename;
    }
}

class SecureFileHandler
{
    /**
     * Upload file with security measures
     */
    public static function uploadFile($file, $directory = 'custom_designs')
    {
        // Validate file
        self::validateFile($file);
        
        // Generate secure filename
        $filename = self::generateSecureFilename($file);
        
        // Compress if image
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $file = self::compressImage($file);
        }
        
        // Store in cloud storage (S3) or local storage
        $path = self::storeFile($file, $directory, $filename);
        
        // Log the upload
        Log::info('File uploaded securely', [
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);
        
        return $path;
    }

    /**
     * Validate file security
     */
    private static function validateFile($file)
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('Invalid file');
        }

        // Size check
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('File too large');
        }

        // MIME type check
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('File type not allowed');
        }
    }

    /**
     * Generate secure filename
     */
    private static function generateSecureFilename($file)
    {
        $extension = $file->getClientOriginalExtension();
        $basename = uniqid('file_', true);
        
        return $basename . '.' . $extension;
    }

    /**
     * Compress image to reduce storage needs
     */
    private static function compressImage($file)
    {
        try {
            $image = null;
            
            switch ($file->getMimeType()) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file->getPathname());
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file->getPathname());
                    break;
                default:
                    return $file;
            }

            if (!$image) {
                return $file;
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate new dimensions (max 1920x1080)
            $maxWidth = 1920;
            $maxHeight = 1080;
            
            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);
                
                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                
                // Save compressed image
                $tempPath = tempnam(sys_get_temp_dir(), 'compressed_');
                imagejpeg($newImage, $tempPath, 85); // 85% quality
                
                imagedestroy($image);
                imagedestroy($newImage);
                
                // Create new file object
                $compressedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    $file->getClientOriginalName(),
                    'image/jpeg',
                    filesize($tempPath),
                    null,
                    true
                );
                
                return $compressedFile;
            }

            imagedestroy($image);
        } catch (\Exception $e) {
            Log::error('Image compression failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);
        }

        return $file;
    }

    /**
     * Store file in appropriate storage
     */
    private static function storeFile($file, $directory, $filename)
    {
        // Try cloud storage first (S3)
        if (config('filesystems.default') === 's3') {
            try {
                $path = $file->storeAs($directory, $filename, 's3');
                
                // Set appropriate permissions
                Storage::disk('s3')->setVisibility($path, 'private');
                
                return $path;
            } catch (\Exception $e) {
                Log::error('S3 upload failed, falling back to local storage', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to local storage
        $path = $file->storeAs($directory, $filename, 'public');
        
        // Set file permissions
        $fullPath = Storage::disk('public')->path($path);
        chmod($fullPath, 0644);
        
        return $path;
    }

    /**
     * Delete file securely
     */
    public static function deleteFile($path)
    {
        try {
            // Try cloud storage first
            if (config('filesystems.default') === 's3') {
                Storage::disk('s3')->delete($path);
            }
            
            // Also delete from local if exists
            Storage::disk('public')->delete($path);
            
            Log::info('File deleted securely', ['path' => $path]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Generate secure download URL
     */
    public static function generateDownloadUrl($path, $expiresIn = 60)
    {
        if (config('filesystems.default') === 's3') {
            return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($expiresIn));
        }
        
        // For local storage, generate a signed URL
        $signature = hash_hmac('sha256', $path . now()->timestamp, config('app.key'));
        
        return route('files.download', [
            'path' => $path,
            'signature' => $signature,
            'expires' => now()->addMinutes($expiresIn)->timestamp
        ]);
    }
}

class DataProtectionService
{
    /**
     * Encrypt sensitive customer data
     */
    public static function encryptSensitiveData($data)
    {
        if (is_array($data)) {
            return array_map(function ($item) {
                return self::encryptSensitiveData($item);
            }, $data);
        }

        if (!is_string($data)) {
            return $data;
        }

        // Check if data contains sensitive information
        $sensitivePatterns = [
            '/\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b/', // Credit card
            '/\b\d{3}[-\s]?\d{2}[-\s]?\d{4}\b/', // SSN-like
            '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', // Email
            '/\b\d{3}[-\s]?\d{3}[-\s]?\d{4}\b/', // Phone
        ];

        foreach ($sensitivePatterns as $pattern) {
            if (preg_match($pattern, $data)) {
                return encrypt($data);
            }
        }

        return $data;
    }

    /**
     * Decrypt sensitive data
     */
    public static function decryptSensitiveData($data)
    {
        try {
            return decrypt($data);
        } catch (\Exception $e) {
            // If decryption fails, return original data
            return $data;
        }
    }

    /**
     * Mask sensitive data for logging
     */
    public static function maskSensitiveData($data)
    {
        if (is_array($data)) {
            return array_map(function ($item) {
                return self::maskSensitiveData($item);
            }, $data);
        }

        if (!is_string($data)) {
            return $data;
        }

        // Mask credit card numbers
        $data = preg_replace('/\b(\d{4})[-\s]?(\d{4})[-\s]?(\d{4})[-\s]?(\d{4})\b/', '$1-****-****-$4', $data);
        
        // Mask phone numbers
        $data = preg_replace('/\b(\d{3})[-\s]?(\d{3})[-\s]?(\d{4})\b/', '$1-$2-****', $data);
        
        // Mask emails
        $data = preg_replace('/\b([A-Za-z0-9._%+-]+)@([A-Za-z0-9.-]+)\.([A-Z|a-z]{2,})\b/', '$1***@$2.$3', $data);

        return $data;
    }

    /**
     * Create database backup with encryption
     */
    public static function createSecureBackup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Ensure backup directory exists
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            // Create database dump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $backupPath
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                // Encrypt the backup
                $encryptedPath = $backupPath . '.enc';
                $key = config('app.backup_encryption_key');
                
                $data = file_get_contents($backupPath);
                $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
                file_put_contents($encryptedPath, $encrypted);
                
                // Remove unencrypted backup
                unlink($backupPath);
                
                // Set secure permissions
                chmod($encryptedPath, 0600);
                
                Log::info('Secure backup created', ['filename' => $filename . '.enc']);
                
                return $encryptedPath;
            } else {
                throw new \Exception('Database backup failed');
            }
        } catch (\Exception $e) {
            Log::error('Secure backup creation failed', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Restore from encrypted backup
     */
    public static function restoreFromBackup($backupPath)
    {
        try {
            // Decrypt backup
            $key = config('app.backup_encryption_key');
            $encrypted = file_get_contents($backupPath);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
            
            if ($decrypted === false) {
                throw new \Exception('Backup decryption failed');
            }
            
            // Create temporary file
            $tempPath = tempnam(sys_get_temp_dir(), 'restore_');
            file_put_contents($tempPath, $decrypted);
            
            // Restore database
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $tempPath
            );
            
            exec($command, $output, $returnCode);
            
            // Clean up temp file
            unlink($tempPath);
            
            if ($returnCode === 0) {
                Log::info('Database restored from backup', ['backup_path' => $backupPath]);
                return true;
            } else {
                throw new \Exception('Database restore failed');
            }
        } catch (\Exception $e) {
            Log::error('Database restore failed', [
                'backup_path' => $backupPath,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Audit logging for sensitive operations
     */
    public static function auditLog($action, $data = [])
    {
        $auditData = [
            'action' => $action,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'data' => self::maskSensitiveData($data)
        ];
        
        // Log to secure audit trail
        Log::channel('audit')->info('Security audit', $auditData);
        
        // Also store in database for long-term storage
        \App\Models\SecurityAudit::create($auditData);
    }

    /**
     * Check for data breaches or suspicious activity
     */
    public static function checkSecurityThreats()
    {
        $threats = [];
        
        // Check for unusual login patterns
        $recentLogins = \App\Models\SecurityAudit::where('action', 'login')
            ->where('timestamp', '>=', now()->subHours(24))
            ->get()
            ->groupBy('user_id');
        
        foreach ($recentLogins as $userId => $logins) {
            if ($logins->count() > 10) { // More than 10 logins in 24 hours
                $threats[] = [
                    'type' => 'unusual_login_activity',
                    'user_id' => $userId,
                    'count' => $logins->count(),
                    'severity' => 'medium'
                ];
            }
        }
        
        // Check for failed login attempts
        $failedLogins = \App\Models\SecurityAudit::where('action', 'login_failed')
            ->where('timestamp', '>=', now()->subHours(1))
            ->count();
        
        if ($failedLogins > 50) {
            $threats[] = [
                'type' => 'brute_force_attack',
                'count' => $failedLogins,
                'severity' => 'high'
            ];
        }
        
        // Check for unusual file access
        $fileAccess = \App\Models\SecurityAudit::where('action', 'file_access')
            ->where('timestamp', '>=', now()->subHours(24))
            ->get()
            ->groupBy('ip_address');
        
        foreach ($fileAccess as $ip => $accesses) {
            if ($accesses->count() > 100) { // More than 100 file accesses in 24 hours
                $threats[] = [
                    'type' => 'unusual_file_access',
                    'ip_address' => $ip,
                    'count' => $accesses->count(),
                    'severity' => 'medium'
                ];
            }
        }
        
        if (!empty($threats)) {
            Log::warning('Security threats detected', ['threats' => $threats]);
            
            // Send alert to security team
            foreach ($threats as $threat) {
                if ($threat['severity'] === 'high') {
                    self::sendSecurityAlert($threat);
                }
            }
        }
        
        return $threats;
    }

    /**
     * Send security alert
     */
    private static function sendSecurityAlert($threat)
    {
        try {
            $message = "Security threat detected: {$threat['type']}";
            
            // Send email to security team
            \Mail::to(config('security.alert_email'))->send(
                new \App\Mail\SecurityAlert($threat)
            );
            
            // Send SMS if configured
            if (config('security.sms_alerts')) {
                // Implement SMS notification
            }
            
            Log::critical('Security alert sent', ['threat' => $threat]);
        } catch (\Exception $e) {
            Log::error('Failed to send security alert', [
                'threat' => $threat,
                'error' => $e->getMessage()
            ]);
        }
    }
}

class SecurityAudit extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'action',
        'user_id',
        'ip_address',
        'user_agent',
        'timestamp',
        'data'
    ];

    protected $casts = [
        'data' => 'json',
        'timestamp' => 'datetime'
    ];

    protected $table = 'security_audits';
}
