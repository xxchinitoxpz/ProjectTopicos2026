<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'maintenance_id',
        'vehicle_id',
        'responsible_id',
        'tipo_mantenimiento',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'responsible_id');
    }

    public function days(): HasMany
    {
        return $this->hasMany(MaintenanceScheduleDay::class);
    }
}
