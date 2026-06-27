<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceScheduleDay extends Model
{
    protected $fillable = [
        'maintenance_schedule_id',
        'fecha',
        'observacion',
        'imagen',
        'realizado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'realizado' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}

