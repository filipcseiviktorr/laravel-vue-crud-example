<?php

namespace App\Http\Resources;

use App\Models\CarModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;

/** @mixin CarModel */
class CarModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->getImageUrl(),
        ];
    }

    private function getImageUrl(): ?string
    {
        if (!$this->path) {
            return null;
        }

        try {
            return app(ImageService::class)->getImageUrl($this->path);
        } catch (Exception $e) {
            Log::error("Failed to get image URL for path {$this->path}: " . $e->getMessage());
            return null;
        }
    }
}
