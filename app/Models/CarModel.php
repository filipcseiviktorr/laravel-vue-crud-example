<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $car_brand_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CarBrand $carBrand
 * @method static \Database\Factories\CarModelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel whereCarBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel whereUpdatedAt($value)
 * @property string|null $path
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarModel wherePath($value)
 * @mixin \Eloquent
 */
class CarModel extends Model
{
    use HasFactory;

    protected $fillable = ['car_brand_id', 'name', 'path'];

    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }
}
