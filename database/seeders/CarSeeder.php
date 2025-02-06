<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            'Audi' => ['A1', 'A3', 'A4', 'Q5', 'Q7'],
            'Toyota' => ['Corolla', 'Camry', 'Yaris', 'Land Cruiser'],
            'Volkswagen' => ['Golf', 'Polo', 'Passat', 'Tiguan'],
            'BMW' => ['X5', 'X3', '3 Series', '5 Series'],
            'Mercedes-Benz' => ['A-Class', 'C-Class', 'E-Class', 'S-Class'],
            'Ford' => ['Fiesta', 'Focus', 'Mustang', 'Explorer']
        ];

        foreach ($cars as $brand => $models) {
            $carBrand = CarBrand::firstOrCreate(['name' => $brand]);

            foreach ($models as $model) {
                CarModel::firstOrCreate(
                    ['car_brand_id' => $carBrand->id, 'name' => $model],
                    ['path' => 'cars/' . strtolower(str_replace(' ', '-', $model) . '.jpg')]
                );
            }
        }
    }
}
