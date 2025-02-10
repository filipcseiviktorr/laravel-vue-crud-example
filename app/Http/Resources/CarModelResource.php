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
            'path' => $this->path ? app(ImageService::class)->getImageUrl($this->path) : null,
            'thumbnail_path' => $this->path ? app(ImageService::class)->getThumbnailImageUrl($this->path) : null
        ];
    }
}
