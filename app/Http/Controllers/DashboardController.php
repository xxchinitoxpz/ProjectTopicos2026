<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assistance;
use App\Models\PlanningDay;
use App\Models\StaffGroup;
use App\Models\Shift;
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

        $cards = $groups->map(function (StaffGroup $group) use ($planningDaysByGroup, $selectedDate) {
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
                'zone_name' => $group->zone?->name ?? 'Sin zona',
                'zone_status' => $isComplete ? 'OK' : 'Incompleto',
                'group_name' => $group->name ?? 'Sin grupo',
                'shift_name' => $shift?->name ?? 'Sin turno',
                'vehicle_name' => $vehicle?->code ?? $vehicle?->plate ?? 'Sin vehiculo',
                'present_count' => $presentCount,
                'missing_count' => $missingCount,
                'is_complete' => $isComplete,
                'show_edit_button' => !$isComplete,
                'detail_url' => $planningDay
                    ? route('admin.planning.edit', $planningDay->id)
                    : route('admin.staff-group.edit', $group->id),
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
}
