<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaffGroup extends Model
{
    protected $fillable = [
        'name',
        'shift_id',
        'zone_id',
        'vehicle_id',
        'driver_id',
        'days',
        'status',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }

    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'staff_group_helpers', 'staff_group_id', 'staff_id')
                    ->withTimestamps();
    }
}
