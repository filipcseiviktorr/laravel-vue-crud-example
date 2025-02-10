<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Exception;

class ImageService
{
    private string $carModelsDir;
    private string $thumbnailsDir;
    private string $thumbnailPrefix;
    private int $thumbnailScale;

    public function __construct()
    {
        $this->carModelsDir = config('image.car_models_dir');
        $this->thumbnailsDir = config('image.thumbnails_dir');
        $this->thumbnailPrefix = config('image.thumbnail_prefix');
        $this->thumbnailScale = config('image.thumbnail_scale');
    }

    /**
     * @throws Exception
     */
    public function handleImageUpload($image, string $existingPath = null): ?string
    {
        if (!$image) {
            if ($existingPath) {
                $this->deleteImage($existingPath);
            }

            return null;
        }

        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            $existingHash = md5(Storage::disk('public')->get($existingPath));
            $uploadedHash = md5($image->getContent());

            if ($existingHash === $uploadedHash) {
                return $existingPath;
            }
        }

        if ($existingPath) {
            $this->deleteImage($existingPath);
        }

        try {
            return $image->store($this->carModelsDir, 'public');
        } catch (Exception $e) {
            Log::error("Failed to upload image: " . $e->getMessage());
            throw new Exception("Failed to upload image", 0, $e);
        }
    }

    public function deleteImage(string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            if (Storage::disk('public')->exists($path) && !Storage::disk('public')->delete($path)) {
                Log::error("Failed to delete image: $path");
            }

            $thumbnailPath = $this->getThumbnailPath($path);
            if (Storage::disk('public')->exists($thumbnailPath) && !Storage::disk('public')->delete($thumbnailPath)) {
                Log::error("Failed to delete thumbnail: $thumbnailPath");
            }
        } catch (Exception $e) {
            Log::error("Exception when deleting image: " . $e->getMessage());
        }
    }


    /**
     * @throws Exception
     */
    public function getThumbnailImageUrl(string $path): string
    {
        return $this->getImageUrlInternal($path, true);
    }

    /**
     * @throws Exception
     */
    public function getImageUrl(string $path): string
    {
        return $this->getImageUrlInternal($path);
    }

    private function getImageUrlInternal(string $path, bool $isThumbnail = false): string
    {
        if (!$this->imageExists($path)) {
            Log::error("Image does not exist: $path");
            return '';
        }

        try {
            if ($isThumbnail) {
                File::ensureDirectoryExists(Storage::disk('public')->path($this->thumbnailsDir));

                $thumbnailPath = $this->getThumbnailPath($path);
                if (!$this->imageExists($thumbnailPath)) {
                    $this->createThumbnail($path, $thumbnailPath);
                }

                return Storage::disk('public')->url($thumbnailPath);
            } else {
                return Storage::disk('public')->url($path);
            }
        } catch (Exception $e) {
            Log::error("Failed to get image URL: " . $e->getMessage());
            throw new Exception("Failed to get image URL", 0, $e);
        }
    }

    private function getThumbnailPath(string $path): string
    {
        return "{$this->thumbnailsDir}/{$this->thumbnailPrefix}" . basename($path);
    }

    private function imageExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * @throws Exception
     */
    private function createThumbnail(string $originalImagePath, string $thumbnailImagePath): void
    {
        try {
            $manager = new ImageManager(new Driver());
            $imageContent = Storage::disk('public')->get($originalImagePath);
            $image = $manager->read($imageContent);
            $image->contain($this->thumbnailScale, $this->thumbnailScale);
            $image->save(Storage::disk('public')->path($thumbnailImagePath));
        } catch (Exception $e) {
            Log::error("Failed to create thumbnail: " . $e->getMessage());
            throw new Exception("Failed to create thumbnail", 0, $e);
        }
    }

    private function isValidImage($image): bool
    {
        $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($image->getClientMimeType(), $validMimeTypes);
    }
}
