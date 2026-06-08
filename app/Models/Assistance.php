<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assistance extends Model
{
    protected $fillable = [
        'staff_id',
        'date_time',
        'type',
        'state',
        'shift_id',
        'notes',
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Determina el turno y tipo de asistencia basado en la hora proporcionada.
     *
     * @param string $time Formato HH:MM o HH:MM:SS
     * @return array ['shift_id' => int|null, 'type' => 'ingreso'|'salida']
     */
    public static function determineShiftAndType(string $time): array
    {
        $shifts = Shift::all();
        if ($shifts->isEmpty()) {
            return [
                'shift_id' => null,
                'type' => 'ingreso',
            ];
        }

        // Convertir hora dada a minutos desde la medianoche
        $timeParts = explode(':', $time);
        $timeInMinutes = ((int)$timeParts[0]) * 60 + ((int)$timeParts[1]);

        $minDiff = null;
        $bestShiftId = null;
        $bestType = 'ingreso';

        foreach ($shifts as $shift) {
            // Diferencia con check_in (entrada)
            $inParts = explode(':', $shift->check_in);
            $inMinutes = ((int)$inParts[0]) * 60 + ((int)$inParts[1]);
            $diffIn = abs($timeInMinutes - $inMinutes);

            if ($minDiff === null || $diffIn < $minDiff) {
                $minDiff = $diffIn;
                $bestShiftId = $shift->id;
                $bestType = 'ingreso';
            }

            // Diferencia con check_out (salida)
            $outParts = explode(':', $shift->check_out);
            $outMinutes = ((int)$outParts[0]) * 60 + ((int)$outParts[1]);
            $diffOut = abs($timeInMinutes - $outMinutes);

            if ($diffOut < $minDiff) {
                $minDiff = $diffOut;
                $bestShiftId = $shift->id;
                $bestType = 'salida';
            }
        }

        return [
            'shift_id' => $bestShiftId,
            'type' => $bestType,
        ];
    }
}
