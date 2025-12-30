<?php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;

class SecureFileUploadService
{
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/webp'
    ];

    private array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'webp'
    ];

    private int $maxFileSize = 5242880; // 5MB in bytes

    private array $dangerousExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'cmd', 'com', 
        'scr', 'vbs', 'js', 'jar', 'sh', 'ps1', 'py', 'pl', 'rb', 'asp', 
        'aspx', 'jsp', 'cgi', 'htaccess', 'htpasswd', 'ini', 'conf', 'config'
    ];

    /**
     * Secure file upload with comprehensive validation
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads'): array
    {
        try {
            // Step 1: Basic validation
            $validationResult = $this->validateFile($file);
            if (!$validationResult['success']) {
                return $validationResult;
            }

            // Step 2: Security checks
            $securityResult = $this->performSecurityChecks($file);
            if (!$securityResult['success']) {
                return $securityResult;
            }

            // Step 3: Process and store file
            $result = $this->processAndStoreFile($file, $directory);
            
            return $result;

        } catch (\Exception $e) {
            Log::error('Secure file upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'File upload failed due to server error'
            ];
        }
    }

    /**
     * Validate file against basic rules
     */
    private function validateFile(UploadedFile $file): array
    {
        $validator = Validator::make(['file' => $file], [
            'file' => [
                'required',
                'file',
                'max:' . ($this->maxFileSize / 1024), // Convert to KB
                'mimes:' . implode(',', $this->allowedExtensions)
            ],
            'file.*' => [
                function ($attribute, $value, $fail) {
                    if (!in_array($value->getMimeType(), $this->allowedMimeTypes)) {
                        $fail('File type not allowed.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'File validation failed',
                'errors' => $validator->errors()->all()
            ];
        }

        return ['success' => true];
    }

    /**
     * Perform comprehensive security checks
     */
    private function performSecurityChecks(UploadedFile $file): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Check for dangerous extensions
        if (in_array($extension, $this->dangerousExtensions)) {
            return [
                'success' => false,
                'message' => 'File type not allowed for security reasons'
            ];
        }

        // Check filename for suspicious patterns
        if ($this->hasSuspiciousFilename($originalName)) {
            return [
                'success' => false,
                'message' => 'Filename contains suspicious characters'
            ];
        }

        // Verify file content matches extension
        if (!$this->verifyFileContent($file, $extension)) {
            return [
                'success' => false,
                'message' => 'File content does not match its extension'
            ];
        }

        // Scan for malicious content (basic pattern matching)
        if ($this->containsMaliciousContent($file)) {
            return [
                'success' => false,
                'message' => 'File contains potentially malicious content'
            ];
        }

        return ['success' => true];
    }

    /**
     * Check for suspicious filename patterns
     */
    private function hasSuspiciousFilename(string $filename): bool
    {
        $suspiciousPatterns = [
            '/\.\./',           // Directory traversal
            '/[<>:"|?*]/',      // Invalid characters
            '/\s+$/',           // Trailing spaces
            '/^\s+/',           // Leading spaces
            '/CON|PRN|AUX|NUL/i', // Reserved names
            '/COM[1-9]|LPT[1-9]/i' // Reserved device names
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify file content matches the declared extension
     */
    private function verifyFileContent(UploadedFile $file, string $extension): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        // Map extensions to expected MIME types
        $mimeMap = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'webp' => ['image/webp']
        ];

        return isset($mimeMap[$extension]) && 
               in_array($detectedMimeType, $mimeMap[$extension]);
    }

    /**
     * Basic scan for malicious content patterns
     */
    private function containsMaliciousContent(UploadedFile $file): bool
    {
        // Only scan text-based files
        $textBasedMimes = ['application/pdf', 'application/msword', 
                          'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (!in_array($file->getMimeType(), $textBasedMimes)) {
            return false;
        }

        $content = file_get_contents($file->getPathname());
        $maliciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process and securely store the file
     */
    private function processAndStoreFile(UploadedFile $file, string $directory): array
    {
        // Generate secure filename
        $secureFilename = $this->generateSecureFilename($file);
        
        // Create directory if it doesn't exist
        $fullDirectory = $directory . '/' . date('Y/m/d');
        Storage::disk('public')->makeDirectory($fullDirectory);

        // Store the file
        $path = $file->storeAs($fullDirectory, $secureFilename, 'public');

        // Set secure permissions
        $this->setSecurePermissions($path);

        return [
            'success' => true,
            'data' => [
                'filename' => $secureFilename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString()
            ],
            'message' => 'File uploaded successfully'
        ];
    }

    /**
     * Generate a secure filename
     */
    private function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $random = Str::random(16);
        
        return "upload_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Set secure file permissions
     */
    private function setSecurePermissions(string $path): void
    {
        $fullPath = Storage::disk('public')->path($path);
        if (file_exists($fullPath)) {
            chmod($fullPath, 0644); // Read/write for owner, read for others
        }
    }

    /**
     * Delete a file securely
     */
    public function deleteFile(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('File deleted successfully', ['path' => $path]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get file info with security metadata
     */
    public function getFileInfo(string $path): ?array
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                return null;
            }

            $fullPath = Storage::disk('public')->path($path);
            $file = new File($fullPath);

            return [
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'last_modified' => $file->getMTime(),
                'is_readable' => $file->isReadable(),
                'is_writable' => $file->isWritable()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get file info', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
