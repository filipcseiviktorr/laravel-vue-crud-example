<?php

namespace App\Http\Resources;

use App\Models\CarBrand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CarBrand */
class CarBrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'car_models' => $this->whenLoaded('carModels', function () {
                return $this->carModels->isNotEmpty() ? CarModelResource::collection($this->carModels->sortByDesc('id')) : [];
            }),
        ];
    }
}
