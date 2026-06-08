<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'time_start',
        'time_end',
        'description',
    ];

    public function vehicleRoutes(): HasMany
    {
        return $this->hasMany(VehicleRoute::class);
    }
}
