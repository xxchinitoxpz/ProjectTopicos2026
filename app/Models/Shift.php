<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'description',
        'check_in',
        'check_out',
    ];

    public function assistances(): HasMany
    {
        return $this->hasMany(Assistance::class);
    }
}
