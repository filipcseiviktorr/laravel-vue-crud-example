<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarModel> $carModels
 * @property-read int|null $car_models_count
 * @method static \Database\Factories\CarBrandFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarBrand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CarBrand extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }
}
