<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutePath extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'vehicle_route_id',
    ];

    public function vehicleRoute(): BelongsTo
    {
        return $this->belongsTo(VehicleRoute::class);
    }
}
