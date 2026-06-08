<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleColor extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function getHexForPickerAttribute(): string
    {
        if (empty($this->code)) {
            return '#ffffff';
        }

        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $this->code)) {
            return strtolower($this->code);
        }

        if (preg_match('/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/i', $this->code, $match)) {
            $toHex = fn ($n) => str_pad(dechex((int) $n), 2, '0', STR_PAD_LEFT);

            return '#' . $toHex($match[1]) . $toHex($match[2]) . $toHex($match[3]);
        }

        return '#ffffff';
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'color_id');
    }
}
