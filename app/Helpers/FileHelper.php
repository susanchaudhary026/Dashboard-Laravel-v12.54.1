<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * Allowed MIME types for images
     */
    private static $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    /**
     * Allowed file extensions
     */
    private static $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Max file size in MB
     */
    private static $maxSizeMB = 2;

    /**
     * Store uploaded image securely
     * 
     * @param UploadedFile $file
     * @param string|null $oldFile
     * @return string|null
     * @throws \Exception
     */
    public static function storeImage(UploadedFile $file, ?string $oldFile = null): ?string
    {
        try {
            // Delete old file if exists
            if ($oldFile && Storage::disk('private')->exists($oldFile)) {
                Storage::disk('private')->delete($oldFile);
            }

            // Validate file
            self::validateImage($file);

            // Generate unique filename
            $filename = self::generateUniqueFilename($file);

            // Store in private storage (not publicly accessible)
            $path = Storage::disk('private')->putFileAs('articles', $file, $filename);

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate uploaded image
     * 
     * @param UploadedFile $file
     * @throws \Exception
     */
    private static function validateImage(UploadedFile $file): void
    {
        // Check file size
        $fileSizeMB = $file->getSize() / (1024 * 1024);
        if ($fileSizeMB > self::$maxSizeMB) {
            throw new \Exception("File size must not exceed " . self::$maxSizeMB . "MB. Current: " . round($fileSizeMB, 2) . "MB");
        }

        // Check MIME type (XSS prevention)
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::$allowedMimes)) {
            throw new \Exception("Invalid file type. MIME: {$mimeType}. Allowed: " . implode(', ', self::$allowedMimes));
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::$allowedExtensions)) {
            throw new \Exception("Invalid file extension. Allowed: " . implode(', ', self::$allowedExtensions));
        }

        // Additional: Check actual file content to prevent spoofing
        self::validateFileMagicBytes($file);
    }

    /**
     * Validate file magic bytes signature to prevent spoofing
     * 
     * @param UploadedFile $file
     * @throws \Exception
     */
    private static function validateFileMagicBytes(UploadedFile $file): void
    {
        $validSignatures = [
            'jpeg' => ['FF', 'D8', 'FF'],  
            'png'  => ['89', '50', '4E', '47'],  
            'gif'  => ['47', '49', '46'],  
            'webp' => ['52', '49', '46', '46']  
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!isset($validSignatures[$extension])) {
            return; // it skips if extension is not in the allowed list, as it will be caught by previous validation
        }

        $handle = fopen($file->getPathname(), 'r');
        $firstBytes = [];
        for ($i = 0; $i < count($validSignatures[$extension]); $i++) {
            $byte = fread($handle, 1);
            $firstBytes[] = strtoupper(bin2hex($byte));
        }
        fclose($handle);

        $expected = $validSignatures[$extension];
        if ($firstBytes !== $expected) {
            throw new \Exception("File content does not match the {$extension} format. Possible malicious file.");
        }
    }

    /**
     * Generate unique filename with timestamp
     * 
     * @param UploadedFile $file
     * @return string
     */
    private static function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $random = Str::random(8);
        
        return "article_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get full URL for stored image
     * 
     * @param string $path
     * @return string|null
     */
    public static function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // If it's a URL (media_link), return as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Return signed URL for private storage
        return Storage::disk('private')->temporaryUrl(
            $path,
            now()->addHours(24)  // URL valid for 24 hours
        );
    }

    /**
     * Delete image
     * 
     * @param string|null $path
     */
    public static function deleteImage(?string $path): void
    {
        if ($path && !filter_var($path, FILTER_VALIDATE_URL) && Storage::disk('private')->exists($path)) {
            Storage::disk('private')->delete($path);
        }
    }
}
