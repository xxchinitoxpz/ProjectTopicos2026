<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planning extends Model
{
    protected $fillable = [
        'staff_group_id',
        'date_start',
        'date_end',
        'days',
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

    public function planningDays(): HasMany
    {
        return $this->hasMany(PlanningDay::class, 'planning_id');
    }
}
