<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'date',
        'description',
        'state',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Obtiene el nombre del día de la semana en español.
     *
     * @return string
     */
    public function getDayNameAttribute(): string
    {
        if (!$this->date) {
            return '';
        }

        $days = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
        ];

        return $days[$this->date->dayOfWeek] ?? '';
    }
}
