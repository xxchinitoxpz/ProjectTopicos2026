<?php

namespace App\Models;

use App\Support\PublicImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImage extends Model
{
    protected $fillable = [
        'image',
        'profile',
        'vehicle_id',
    ];

    protected function casts(): array
    {
        return [
            'profile' => 'boolean',
        ];
    }

    public function getUrlAttribute(): string
    {
        return PublicImageStorage::url($this->image);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
