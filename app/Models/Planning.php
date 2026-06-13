<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planning extends Model
{
    protected $fillable = [
        'staff_group_id',
        'driver_id',
        'date_start',
        'date_end',
        'days',
        'state',
    ];

    protected $casts = [
        'days' => 'array',
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function staffGroup(): BelongsTo
    {
        return $this->belongsTo(StaffGroup::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }

    public function helpers(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'planning_helpers', 'planning_id', 'staff_id')
                    ->withTimestamps();
    }

    public function changes(): HasMany
    {
        return $this->hasMany(PlanningChange::class, 'planning_id');
    }
}
