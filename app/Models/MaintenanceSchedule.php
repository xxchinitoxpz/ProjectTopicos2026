<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
        return $this->hasMany(MaintenanceScheduleDay::class, 'maintenance_schedule_id');
    }

    public function regenerateDays(): void
    {
        $maintenance = $this->maintenance()->first();

        if (!$maintenance) {
            return;
        }

        $this->days()->delete();

        $dayOfWeek = $this->dayNumber($this->dia_semana);
        $dates = [];

        foreach (CarbonPeriod::create($maintenance->fecha_inicio, $maintenance->fecha_fin) as $date) {
            if ($date->dayOfWeek !== $dayOfWeek) {
                continue;
            }

            $dates[] = [
                'maintenance_schedule_id' => $this->id,
                'fecha' => $date->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($dates) {
            $this->days()->insert($dates);
        }
    }

    private function dayNumber(string $day): int
    {
        return [
            'domingo' => Carbon::SUNDAY,
            'lunes' => Carbon::MONDAY,
            'martes' => Carbon::TUESDAY,
            'miercoles' => Carbon::WEDNESDAY,
            'jueves' => Carbon::THURSDAY,
            'viernes' => Carbon::FRIDAY,
            'sabado' => Carbon::SATURDAY,
        ][$day];
    }
}
