<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GenerateThumbnail extends Command
{
    protected $signature = 'generate:thumbnail {originalImagePath} {thumbnailImagePath}';
    protected $description = 'Generate a thumbnail for the given image';

    public function handle(): void
    {
        $originalImagePath = $this->argument('originalImagePath');
        $thumbnailImagePath = $this->argument('thumbnailImagePath');
        $thumbnailDir = dirname($thumbnailImagePath);

        if (!Storage::disk('public')->exists($originalImagePath)) {
            $this->error("Original image does not exist: $originalImagePath");
            return;
        }

        if (!Storage::disk('public')->exists($thumbnailDir)) {
            Storage::disk('public')->makeDirectory($thumbnailDir);
        }

        $manager = new ImageManager(new Driver());
        $imageContent = Storage::disk('public')->get($originalImagePath);
        $image = $manager->read($imageContent);
        $image->scale(300);

        $image->save(Storage::disk('public')->path($thumbnailImagePath));

        $this->info("Thumbnail generated: $thumbnailImagePath");
    }
}
