<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assistance;
use App\Models\Planning;
use App\Models\PlanningDay;
use App\Models\PlanningChange;
use App\Models\Shift;
use App\Models\StaffGroup;
use App\Models\Staff;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $latestPlanningDate = PlanningDay::query()->max('date');
        $latestAssistanceDate = Assistance::query()->max(DB::raw('DATE(date_time)'));

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : ($this->latestCarbonDate([$latestPlanningDate, $latestAssistanceDate]) ?? now());
        $selectedDate = $selectedDate->startOfDay();

        $selectedShiftId = $request->filled('shift_id')
            ? (int) $request->input('shift_id')
            : null;

        $shifts = Shift::orderBy('name')->get();

        $groups = StaffGroup::query()
            ->where('status', 'active')
            ->with(['shift', 'zone', 'vehicle', 'driver', 'helpers'])
            ->when($selectedShiftId, fn ($query) => $query->where('shift_id', $selectedShiftId))
            ->orderBy('name')
            ->get();

        $planningDaysByGroup = PlanningDay::query()
            ->with(['planning.staffGroup', 'shift', 'vehicle', 'driver', 'helpers'])
            ->whereDate('date', $selectedDate->toDateString())
            ->get()
            ->keyBy(fn ($planningDay) => $planningDay->planning?->staff_group_id);

        $cards = $groups->map(function (StaffGroup $group) use ($planningDaysByGroup, $selectedDate, $selectedShiftId) {
            $planningDay = $planningDaysByGroup->get($group->id);
            $shift = $planningDay?->shift ?? $group->shift;
            $vehicle = $planningDay?->vehicle ?? $group->vehicle;
            $driver = $planningDay?->driver ?? $group->driver;
            $helpers = $planningDay?->helpers ?? $group->helpers;

            $expectedStaffIds = collect([$driver?->id])
                ->merge($helpers?->pluck('id') ?? collect())
                ->filter()
                ->unique()
                ->values();

            $presentStaffIds = Assistance::query()
                ->whereDate('date_time', $selectedDate->toDateString())
                ->where('state', 'presente')
                ->whereIn('staff_id', $expectedStaffIds)
                ->pluck('staff_id')
                ->unique();

            $presentCount = $presentStaffIds->count();
            $expectedCount = max($expectedStaffIds->count(), 1);
            $missingCount = max($expectedCount - $presentCount, 0);
            $isComplete = $missingCount === 0;

            return [
                'planning_day_id' => $planningDay?->id,
                'group_id' => $group->id,
                'zone_name' => $group->zone?->name ?? 'Sin zona',
                'zone_status' => $isComplete ? 'OK' : 'Incompleto',
                'group_name' => $group->name ?? 'Sin grupo',
                'shift_name' => $shift?->name ?? 'Sin turno',
                'vehicle_name' => $vehicle?->code ?? $vehicle?->plate ?? 'Sin vehiculo',
                'present_count' => $presentCount,
                'missing_count' => $missingCount,
                'is_complete' => $isComplete,
                'show_edit_button' => !$isComplete,
                'detail_url' => route('dashboard.card-details', [
                    'group' => $group->id,
                    'date' => $selectedDate->format('Y-m-d'),
                    'shift_id' => $selectedShiftId,
                ]),
                'edit_url' => $planningDay
                    ? route('admin.planning.edit', $planningDay->id)
                    : null,
            ];
        })->values();

        $totalPlannings = $cards->count();
        $completePlannings = $cards->where('is_complete', true)->count();
        $incompletePlannings = $cards->where('is_complete', false)->count();
        $missingStaffTotal = $cards->sum('missing_count');

        return view('dashboard', [
            'selectedDate' => $selectedDate,
            'selectedDateLabel' => $selectedDate->format('d/m/Y'),
            'selectedShiftId' => $selectedShiftId,
            'selectedShiftName' => $selectedShiftId
                ? optional($shifts->firstWhere('id', $selectedShiftId))->name
                : 'Todos los turnos',
            'shifts' => $shifts,
            'cards' => $cards,
            'totalPlannings' => $totalPlannings,
            'completePlannings' => $completePlannings,
            'incompletePlannings' => $incompletePlannings,
            'missingStaffTotal' => $missingStaffTotal,
        ]);
    }

    private function latestCarbonDate(array $values): ?Carbon
    {
        $latest = null;

        foreach ($values as $value) {
            if (!$value) {
                continue;
            }

            $candidate = Carbon::parse($value);
            if (!$latest || $candidate->gt($latest)) {
                $latest = $candidate;
            }
        }

        return $latest;
    }

    public function cardDetails(Request $request, StaffGroup $group)
    {
        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : now();
        $selectedShiftId = $request->filled('shift_id')
            ? (int) $request->input('shift_id')
            : null;

        $group->load(['shift', 'zone', 'vehicle', 'driver', 'helpers']);

        $planningDay = PlanningDay::query()
            ->with(['planning.staffGroup', 'shift', 'vehicle', 'driver', 'helpers', 'changes.user'])
            ->whereDate('date', $selectedDate->toDateString())
            ->whereHas('planning', fn ($query) => $query->where('staff_group_id', $group->id))
            ->when($selectedShiftId, fn ($query) => $query->where('shift_id', $selectedShiftId))
            ->first();

        if ($planningDay) {
            $group->setRelation('currentPlanningDay', $planningDay);
        }

        $expectedStaffIds = collect([$planningDay?->driver_id ?? $group->driver_id])
            ->merge(($planningDay?->helpers ?? $group->helpers)?->pluck('id') ?? collect())
            ->filter()
            ->unique()
            ->values();

        $presentStaffIds = Assistance::query()
            ->whereDate('date_time', $selectedDate->toDateString())
            ->where('state', 'presente')
            ->whereIn('staff_id', $expectedStaffIds)
            ->pluck('staff_id')
            ->unique();

        $presentCount = $presentStaffIds->count();
        $missingCount = max($expectedStaffIds->count() - $presentCount, 0);

        $shifts = Shift::orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('name')->get();
        $drivers = Staff::where('status', 'active')
            ->whereHas('staffType', fn ($query) => $query->where('name', 'Conductor'))
            ->orderBy('name')
            ->get();
        $helpersPool = Staff::where('status', 'active')
            ->whereHas('staffType', fn ($query) => $query->where('name', '!=', 'Conductor'))
            ->orderBy('name')
            ->get();

        return view('dashboard-card-modal', [
            'group' => $group,
            'planningDay' => $planningDay,
            'selectedDate' => $selectedDate,
            'presentCount' => $presentCount,
            'missingCount' => $missingCount,
            'shifts' => $shifts,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'helpersPool' => $helpersPool,
        ]);
    }

    public function updateCardDetails(Request $request, StaffGroup $group)
    {
        $request->validate([
            'planning_day_id' => 'nullable|exists:planning_days,id',
            'date' => 'required|date',
            'shift_id' => 'required|exists:shifts,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:staff,id',
            'helper_ids' => 'nullable|array',
            'helper_ids.*' => 'nullable|exists:staff,id',
        ]);

        $selectedDate = Carbon::parse($request->input('date'))->startOfDay();
        $helperIds = collect($request->input('helper_ids', []))
            ->filter()
            ->unique()
            ->values()
            ->all();
        sort($helperIds);

        if (in_array((int) $request->input('driver_id'), $helperIds, true)) {
            return back()
                ->withInput()
                ->with('error', 'El conductor no puede repetirse como ayudante.');
        }

        $planningDay = PlanningDay::query()
            ->with(['planning.staffGroup', 'helpers'])
            ->when($request->filled('planning_day_id'), function ($query) use ($request, $group) {
                $query->where('id', $request->input('planning_day_id'))
                    ->whereHas('planning', fn ($planningQuery) => $planningQuery->where('staff_group_id', $group->id));
            })
            ->first();

        if (!$planningDay) {
            $planning = Planning::firstOrCreate(
                [
                    'staff_group_id' => $group->id,
                    'date_start' => $selectedDate->format('Y-m-d'),
                    'date_end' => $selectedDate->format('Y-m-d'),
                ],
                [
                    'days' => [$this->spanishDayName($selectedDate)],
                ]
            );

            $planningDay = PlanningDay::create([
                'planning_id' => $planning->id,
                'date' => $selectedDate->format('Y-m-d'),
                'shift_id' => $group->shift_id,
                'vehicle_id' => $group->vehicle_id,
                'driver_id' => $group->driver_id,
                'state' => 'active',
            ]);
        }

        DB::beginTransaction();
        try {
            $oldValues = [
                'shift_id' => $planningDay->shift_id,
                'vehicle_id' => $planningDay->vehicle_id,
                'driver_id' => $planningDay->driver_id,
                'helpers' => collect($planningDay->helpers->pluck('id')->all())->sort()->values()->all(),
            ];

            $planningDay->update([
                'shift_id' => $request->integer('shift_id'),
                'vehicle_id' => $request->integer('vehicle_id'),
                'driver_id' => $request->integer('driver_id'),
            ]);

            $planningDay->helpers()->sync($helperIds);

            $newValues = [
                'shift_id' => $planningDay->shift_id,
                'vehicle_id' => $planningDay->vehicle_id,
                'driver_id' => $planningDay->driver_id,
                'helpers' => collect($planningDay->helpers()->get()->pluck('id')->all())->sort()->values()->all(),
            ];

            if (
                $oldValues['shift_id'] !== $newValues['shift_id']
                || $oldValues['vehicle_id'] !== $newValues['vehicle_id']
                || $oldValues['driver_id'] !== $newValues['driver_id']
                || $oldValues['helpers'] !== $newValues['helpers']
            ) {
                PlanningChange::create([
                    'planning_day_id' => $planningDay->id,
                    'user_id' => auth()->id(),
                    'change_type' => 'actualizacion',
                    'old_value' => 'Cambios aplicados desde dashboard',
                    'new_value' => 'Programación actualizada',
                    'reason_type' => 'Dashboard',
                    'details' => 'Actualización rápida desde el modal del dashboard.',
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Cambios guardados correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'No se pudieron guardar los cambios: ' . $e->getMessage());
        }
    }

    private function spanishDayName(Carbon $date): string
    {
        return [
            'Monday' => 'lunes',
            'Tuesday' => 'martes',
            'Wednesday' => 'miercoles',
            'Thursday' => 'jueves',
            'Friday' => 'viernes',
            'Saturday' => 'sabado',
            'Sunday' => 'domingo',
        ][$date->format('l')] ?? strtolower($date->format('l'));
    }
}
