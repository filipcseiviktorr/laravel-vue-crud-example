<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarBrandRequest;
use App\Http\Resources\CarBrandResource;
use App\Models\CarBrand;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CarBrandController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CarBrandResource::collection(
            CarBrand::with(['carModels'])->orderByDesc('id')->get()
        );
    }

    public function store(CarBrandRequest $request): CarBrandResource
    {
        $carBrand = CarBrand::create($request->validated());
        return new CarBrandResource($carBrand);
    }

    public function update(CarBrandRequest $request, CarBrand $carBrand): CarBrandResource
    {
        $carBrand->update($request->validated());
        $carBrand->load('carModels');

        return new CarBrandResource($carBrand);
    }

    public function destroy(CarBrand $carBrand): Response
    {
        $carBrand->delete();
        return response()->noContent();
    }
}
