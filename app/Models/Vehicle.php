<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'name',
        'code',
        'plate',
        'year',
        'occupant_capacity',
        'load_capacity',
        'combustible_capacity',
        'compaction_capacity',
        'description',
        'status',
        'brand_id',
        'model_id',
        'type_id',
        'color_id',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function brandModel(): BelongsTo
    {
        return $this->belongsTo(BrandModel::class, 'model_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'type_id');
    }

    public function vehicleColor(): BelongsTo
    {
        return $this->belongsTo(VehicleColor::class, 'color_id');
    }

    public function vehicleImages(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function vehicleRoutes(): HasMany
    {
        return $this->hasMany(VehicleRoute::class);
    }
}
