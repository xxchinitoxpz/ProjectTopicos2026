<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanningDay extends Model
{
    protected $fillable = [
        'planning_id',
        'date',
        'shift_id',
        'vehicle_id',
        'driver_id',
        'state',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = [
        'staff_group_id',
        'date_start',
        'date_end',
        'days',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
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
        return $this->belongsToMany(Staff::class, 'planning_day_helpers', 'planning_day_id', 'staff_id')
                    ->withTimestamps();
    }

    public function changes(): HasMany
    {
        return $this->hasMany(PlanningChange::class, 'planning_day_id');
    }

    // --- Accessors for compatibility with single planning resource views ---

    public function getStaffGroupAttribute()
    {
        return $this->planning?->staffGroup;
    }

    public function getStaffGroupIdAttribute()
    {
        return $this->planning?->staff_group_id;
    }

    public function getDateStartAttribute()
    {
        return $this->date;
    }

    public function getDateEndAttribute()
    {
        return $this->date;
    }

    public function getDaysAttribute()
    {
        $map = [
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo',
        ];
        $englishDay = strtolower($this->date->format('l'));
        return [$map[$englishDay] ?? $englishDay];
    }
}
