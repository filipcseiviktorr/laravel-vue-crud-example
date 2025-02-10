<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnail extends Command
{
    protected $signature = 'image:generate-thumbnail {path}';
    protected $description = 'Generate a thumbnail for the given image path';

    public function handle(ImageService $imageService): void
    {
        $path = $this->argument('path');

        if (!Storage::disk('public')->exists($path)) {
            $this->error("Image does not exist at path: $path");
            return;
        }

        try {
            $thumbnailPath = $imageService->getThumbnailPath($path);
            $imageService->createThumbnail($path, $thumbnailPath);
            $this->info("Thumbnail generated at: $thumbnailPath");
        } catch (\Exception $e) {
            $this->error("Failed to generate thumbnail: " . $e->getMessage());
        }
    }
}
