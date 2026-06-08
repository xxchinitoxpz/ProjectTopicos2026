<?php

namespace App\Models;

use App\Support\PublicImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'description',
        'logo',
    ];

    public function getLogoUrlAttribute(): string
    {
        return PublicImageStorage::url($this->logo);
    }

    public function brandModels(): HasMany
    {
        return $this->hasMany(BrandModel::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
