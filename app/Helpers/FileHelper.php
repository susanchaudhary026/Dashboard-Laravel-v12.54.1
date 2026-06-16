<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            // Validate file
            self::validateImage($file);

            // Generate unique filename
            $filename = self::generateUniqueFilename($file);

            //image intervention use
            $manager = new ImageManager(new Driver());
            $img = $manager->read($file->getPathname());
            $img->cover(200, 200);

            // Store in storage 
            $path = Storage::disk('public')->putFileAs('uploads/articles', $file, $filename);

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
            return; // it skips if extension is not in the allowed list it will be caught by previous validation
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
     * Delete image
     * 
     * @param string|null $path
     */
    public static function deleteImage(?string $path): void
    {
        if ($path && !filter_var($path, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
