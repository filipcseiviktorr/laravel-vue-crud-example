<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarModelRequest;
use App\Http\Resources\CarModelResource;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Response;

class CarModelController extends Controller
{
    /**
     * @throws Exception
     */
    public function store(CarModelRequest $request, CarBrand $brand, ImageService $imageService): CarModelResource
    {
        $data = $request->validated();
        $data['path'] = $imageService->handleImageUpload($request->file('image'));
        $carModel = CarModel::create(array_merge($data, ['car_brand_id' => $brand->getKey()]));

        return new CarModelResource($carModel);
    }

    /**
     * @throws Exception
     */
    public function update(CarModelRequest $request, CarModel $carModel, ImageService $imageService): CarModelResource
    {
        $data = $request->validated();

        $data['path'] = $imageService->handleImageUpload($request->file('image'), $carModel->path);
        $carModel->update($data);

        return new CarModelResource($carModel);
    }

    public function destroy(CarModel $carModel, ImageService $imageService): Response
    {
        if ($carModel->path) {
            $imageService->deleteImage($carModel->path);
        }

        $carModel->delete();

        return response()->noContent();
    }
}
