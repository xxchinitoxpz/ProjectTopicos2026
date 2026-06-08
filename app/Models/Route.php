<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = [
        'name',
        'latitude_start',
        'latitude_end',
        'longitude_start',
        'longitude_end',
        'status',
    ];

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class);
    }

    public function vehicleRoutes(): HasMany
    {
        return $this->hasMany(VehicleRoute::class);
    }
}
