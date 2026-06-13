<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'area',
        'avg_waste_kg',
        'description',
        'status',
        'sector_id',
        'district_id',
    ];

    protected $casts = [
        'area'         => 'decimal:6',
        'avg_waste_kg' => 'decimal:2',
    ];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class);
    }

    public function zoneCoords(): HasMany
    {
        return $this->hasMany(ZoneCoord::class);
    }
}
