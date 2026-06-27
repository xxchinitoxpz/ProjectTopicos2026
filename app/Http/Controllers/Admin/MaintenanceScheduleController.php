<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceSchedule;
use App\Models\Staff;
use App\Models\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceScheduleController extends Controller
{
    private const DAYS = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miercoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sabado',
        'domingo' => 'Domingo',
    ];

    private const TYPES = [
        'preventivo' => 'Preventivo',
        'limpieza' => 'Limpieza',
        'reparacion' => 'Reparacion',
    ];

    public function index(Maintenance $maintenance)
    {
        $schedules = $maintenance->schedules()
            ->with([
                'vehicle',
                'responsible',
                'days' => fn ($query) => $query->orderBy('fecha'),
            ])
            ->orderByRaw("FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')")
            ->orderBy('hora_inicio')
            ->get();

        return view('admin.maintenances.schedules.index', compact('maintenance', 'schedules'));
    }

    public function create(Maintenance $maintenance)
    {
        return view('admin.maintenances.schedules.create', $this->formData($maintenance));
    }

    public function checkOverlap(Request $request, Maintenance $maintenance)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'dia_semana' => ['required', Rule::in(array_keys(self::DAYS))],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'schedule_id' => ['nullable', 'integer'],
        ]);

        $overlap = $this->hasOverlap(
            $maintenance,
            (int) $data['vehicle_id'],
            $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['schedule_id'] ?? null
        );

        return response()->json([
            'overlap' => $overlap,
            'message' => $overlap ? 'El horario se solapa con otro horario del mismo vehiculo.' : null,
        ]);
    }

    public function store(Request $request, Maintenance $maintenance)
    {
        $request->validate($this->rules($maintenance), $this->messages(), $this->attributes());

        $schedule = $maintenance->schedules()->create($this->scheduleData($request));
        $this->generateScheduleDays($maintenance, $schedule);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Horario registrado correctamente.',
                'redirect' => route('admin.maintenance.schedule.index', $maintenance),
            ], 201);
        }

        return redirect()->route('admin.maintenance.schedule.index', $maintenance)
            ->with('success', 'Horario registrado correctamente.');
    }

    public function edit(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $this->ensureScheduleBelongsToMaintenance($maintenance, $schedule);

        return view('admin.maintenances.schedules.edit', $this->formData($maintenance, $schedule));
    }

    public function update(Request $request, Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $this->ensureScheduleBelongsToMaintenance($maintenance, $schedule);

        $request->validate($this->rules($maintenance, $schedule->id), $this->messages(), $this->attributes());

        $schedule->update($this->scheduleData($request));
        $this->generateScheduleDays($maintenance, $schedule);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Horario actualizado correctamente.',
                'redirect' => route('admin.maintenance.schedule.index', $maintenance),
            ]);
        }

        return redirect()->route('admin.maintenance.schedule.index', $maintenance)
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Request $request, Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $this->ensureScheduleBelongsToMaintenance($maintenance, $schedule);

        $schedule->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Horario eliminado correctamente.',
            ]);
        }

        return redirect()->route('admin.maintenance.schedule.index', $maintenance)
            ->with('success', 'Horario eliminado correctamente.');
    }

    private function formData(Maintenance $maintenance, ?MaintenanceSchedule $schedule = null): array
    {
        $schedule?->loadMissing('days');

        return [
            'maintenance' => $maintenance,
            'schedule' => $schedule,
            'vehicles' => Vehicle::orderBy('name')->get(),
            'responsibles' => Staff::orderBy('name')->orderBy('last_name')->get(),
            'days' => self::DAYS,
            'types' => self::TYPES,
            'calendarMonths' => $schedule ? $this->calendarMonths($maintenance, $schedule) : [],
        ];
    }

    private function rules(Maintenance $maintenance, ?int $ignoreId = null): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'responsible_id' => ['required', 'exists:staff,id'],
            'tipo_mantenimiento' => ['required', Rule::in(array_keys(self::TYPES))],
            'dia_semana' => ['required', Rule::in(array_keys(self::DAYS))],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio',
                function (string $attribute, mixed $value, \Closure $fail) use ($maintenance, $ignoreId): void {
                    $vehicleId = request()->input('vehicle_id');
                    $day = request()->input('dia_semana');
                    $start = request()->input('hora_inicio');

                    if (!$vehicleId || !$day || !$start || !$value) {
                        return;
                    }

                    if ($this->hasOverlap($maintenance, (int) $vehicleId, $day, $start, $value, $ignoreId)) {
                        $fail('El horario se solapa con otro horario del mismo vehiculo.');
                    }
                },
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'vehicle_id.required' => 'Debe seleccionar un vehiculo.',
            'vehicle_id.exists' => 'El vehiculo seleccionado no es valido.',
            'responsible_id.required' => 'Debe seleccionar un responsable.',
            'responsible_id.exists' => 'El responsable seleccionado no es valido.',
            'tipo_mantenimiento.required' => 'Debe seleccionar un tipo de mantenimiento.',
            'tipo_mantenimiento.in' => 'El tipo de mantenimiento seleccionado no es valido.',
            'dia_semana.required' => 'Debe seleccionar un dia de la semana.',
            'dia_semana.in' => 'El dia seleccionado no es valido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio no es valida.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin no es valida.',
            'hora_fin.after' => 'La hora de inicio debe ser menor que la hora de fin.',
        ];
    }

    private function attributes(): array
    {
        return [
            'vehicle_id' => 'vehiculo',
            'responsible_id' => 'responsable',
            'tipo_mantenimiento' => 'tipo de mantenimiento',
            'dia_semana' => 'dia de la semana',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
        ];
    }

    private function scheduleData(Request $request): array
    {
        return $request->only([
            'vehicle_id',
            'responsible_id',
            'tipo_mantenimiento',
            'dia_semana',
            'hora_inicio',
            'hora_fin',
        ]);
    }

    private function ensureScheduleBelongsToMaintenance(Maintenance $maintenance, MaintenanceSchedule $schedule): void
    {
        abort_unless($schedule->maintenance_id === $maintenance->id, 404);
    }

    private function hasOverlap(Maintenance $maintenance, int $vehicleId, string $day, string $start, string $end, ?int $ignoreId = null): bool
    {
        return $maintenance->schedules()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('vehicle_id', $vehicleId)
            ->where('dia_semana', $day)
            ->where('hora_inicio', '<', $end)
            ->where('hora_fin', '>', $start)
            ->exists();
    }

    private function generateScheduleDays(Maintenance $maintenance, MaintenanceSchedule $schedule): void
    {
        $schedule->days()->delete();

        $dayOfWeek = $this->dayNumber($schedule->dia_semana);
        $dates = [];

        foreach (CarbonPeriod::create($maintenance->fecha_inicio, $maintenance->fecha_fin) as $date) {
            if ($date->dayOfWeek !== $dayOfWeek) {
                continue;
            }

            $dates[] = [
                'maintenance_schedule_id' => $schedule->id,
                'fecha' => $date->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($dates) {
            $schedule->days()->insert($dates);
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

    private function calendarMonths(Maintenance $maintenance, MaintenanceSchedule $schedule): array
    {
        $markedDates = $schedule->days
            ->pluck('fecha')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->flip();

        $months = [];
        $cursor = $maintenance->fecha_inicio->copy()->startOfMonth();
        $lastMonth = $maintenance->fecha_fin->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($lastMonth)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();
            $weeks = [];
            $week = array_fill(0, $monthStart->dayOfWeekIso - 1, null);

            foreach (CarbonPeriod::create($monthStart, $monthEnd) as $date) {
                $dateKey = $date->format('Y-m-d');
                $week[] = [
                    'day' => $date->day,
                    'date' => $dateKey,
                    'in_range' => $date->betweenIncluded($maintenance->fecha_inicio, $maintenance->fecha_fin),
                    'marked' => $markedDates->has($dateKey),
                ];

                if (count($week) === 7) {
                    $weeks[] = $week;
                    $week = [];
                }
            }

            if ($week) {
                $weeks[] = array_pad($week, 7, null);
            }

            $months[] = [
                'label' => $this->monthLabel($cursor),
                'weeks' => $weeks,
            ];

            $cursor->addMonth();
        }

        return $months;
    }

    private function monthLabel(Carbon $date): string
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $months[$date->month] . ' ' . $date->year;
    }
}
