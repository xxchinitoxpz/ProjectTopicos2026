<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleRoute extends Model
{
    protected $fillable = [
        'date_route',
        'time_route',
        'description',
        'vehicle_id',
        'route_id',
        'schedule_id',
    ];

    protected function casts(): array
    {
        return [
            'date_route' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function routePaths(): HasMany
    {
        return $this->hasMany(RoutePath::class);
    }
}
