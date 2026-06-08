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
        'description',
        'sector_id',
        'district_id',
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
