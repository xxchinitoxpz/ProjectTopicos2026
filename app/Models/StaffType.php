<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}
