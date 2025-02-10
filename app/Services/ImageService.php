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
            $this->deleteImageIfExists($existingPath);
            return null;
        }

        if ($this->isSameImage($image, $existingPath)) {
            return $existingPath;
        }

        $this->deleteImageIfExists($existingPath);

        return $this->storeImage($image);
    }

    public function deleteImage(string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            $this->deleteFile($path);
            $this->deleteFile($this->getThumbnailPath($path));
        } catch (Exception $e) {
            Log::error("Exception when deleting image: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getThumbnailImageUrl(string $path): string
    {
        return $this->retrieveImageUrl($path, true);
    }

    /**
     * @throws Exception
     */
    public function getImageUrl(string $path): string
    {
        return $this->retrieveImageUrl($path);
    }

    /**
     * @throws Exception
     */
    private function retrieveImageUrl(string $path, bool $isThumbnail = false): string
    {
        if (!$this->imageExists($path)) {
            Log::error("Image does not exist: $path");
            return '';
        }

        try {
            if ($isThumbnail) {
                return $this->retrieveThumbnailUrl($path);
            } else {
                return Storage::disk('public')->url($path);
            }
        } catch (Exception $e) {
            Log::error("Failed to get image URL: " . $e->getMessage());
            throw new Exception("Failed to get image URL", 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function retrieveThumbnailUrl(string $path): string
    {
        File::ensureDirectoryExists(Storage::disk('public')->path($this->thumbnailsDir));

        $thumbnailPath = $this->getThumbnailPath($path);
        if (!$this->imageExists($thumbnailPath)) {
            $this->createThumbnail($path, $thumbnailPath);
        }

        return Storage::disk('public')->url($thumbnailPath);
    }

    public function getThumbnailPath(string $path): string
    {
        return "{$this->thumbnailsDir}/{$this->thumbnailPrefix}" . basename($path);
    }

    /**
     * @throws Exception
     */
    public function createThumbnail(string $originalImagePath, string $thumbnailImagePath): void
    {
        try {
            $manager = new ImageManager(new Driver());
            $imageContent = Storage::disk('public')->get($originalImagePath);
            $image = $manager->read($imageContent);

            $width = $image->width();
            $height = $image->height();

            if ($width > $height) {
                // Landscape
                $image->contain($this->thumbnailScale, intval($this->thumbnailScale * $height / $width));
            } else {
                // Portrait
                $image->contain(intval($this->thumbnailScale * $width / $height), $this->thumbnailScale);
            }
            $image->save(Storage::disk('public')->path($thumbnailImagePath));
        } catch (Exception $e) {
            Log::error("Failed to create thumbnail: " . $e->getMessage());
            throw new Exception("Failed to create thumbnail", 0, $e);
        }
    }

    private function deleteImageIfExists(?string $path): void
    {
        if ($path) {
            $this->deleteImage($path);
        }
    }

    private function isSameImage($image, ?string $existingPath): bool
    {
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            $existingHash = md5(Storage::disk('public')->get($existingPath));
            $uploadedHash = md5($image->getContent());

            return $existingHash === $uploadedHash;
        }

        return false;
    }

    private function storeImage($image): string
    {
        try {
            return $image->store($this->carModelsDir, 'public');
        } catch (Exception $e) {
            Log::error("Failed to upload image: " . $e->getMessage());
            throw new Exception("Failed to upload image", 0, $e);
        }
    }

    private function imageExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    private function deleteFile(string $path): void
    {
        if (Storage::disk('public')->exists($path) && !Storage::disk('public')->delete($path)) {
            Log::error("Failed to delete file: $path");
        }
    }
}
