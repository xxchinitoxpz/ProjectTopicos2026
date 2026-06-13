<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\PlanningChange;
use App\Models\StaffGroup;
use App\Models\Staff;
use App\Models\Vacation;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $stateFilter = $request->input('state');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Planning::query()->with(['staffGroup.zone', 'driver', 'helpers']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('staffGroup', function ($g) use ($search) {
                    $g->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('driver', function ($d) use ($search) {
                    $d->where('name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('helpers', function ($h) use ($search) {
                    $h->where('name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($dateStart) {
            $query->where('date_start', '>=', $dateStart);
        }

        if ($dateEnd) {
            $query->where('date_end', '<=', $dateEnd);
        }

        if ($stateFilter) {
            $query->where('state', $stateFilter);
        }

        $plannings = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        $groups = StaffGroup::where('status', 'active')->orderBy('name')->get();

        return view('admin.plannings.index', compact('plannings', 'groups', 'search', 'dateStart', 'dateEnd', 'stateFilter', 'perPage'));
    }

    public function create()
    {
        // Load active groups with relations
        $groups = StaffGroup::where('status', 'active')
            ->with(['driver', 'helpers', 'shift', 'zone', 'vehicle'])
            ->orderBy('name')
            ->get();

        // Load all active drivers
        $drivers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', 'Conductor');
            })
            ->orderBy('name')
            ->get();

        // Load active helpers
        $helpers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', '!=', 'Conductor');
            })
            ->orderBy('name')
            ->get();

        return view('admin.plannings.create', compact('groups', 'drivers', 'helpers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_group_id' => 'required|exists:staff_groups,id',
            'driver_id' => 'required|exists:staff,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'days' => 'required|array|min:1',
            'helpers' => 'nullable|array',
            'helpers.*' => 'exists:staff,id',
        ]);

        // Backend availability check
        $validationResponse = $this->runAvailabilityCheck(
            $request->date_start,
            $request->date_end,
            $request->driver_id,
            $request->input('helpers', []),
            $request->days,
            $request->staff_group_id
        );

        if (!$validationResponse['valid']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['availability' => 'No se pudo guardar debido a inconsistencias de disponibilidad.']);
        }

        DB::beginTransaction();
        try {
            $planning = Planning::create([
                'staff_group_id' => $request->staff_group_id,
                'driver_id' => $request->driver_id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'days' => $request->days,
                'state' => 'active',
            ]);

            if ($request->has('helpers')) {
                $planning->helpers()->sync($request->helpers);
            }

            PlanningChange::create([
                'planning_id' => $planning->id,
                'user_id' => auth()->id(),
                'action' => 'created',
                'details' => 'Programación individual creada.',
            ]);

            DB::commit();

            if ($request->wantsTurboStream()) {
                session()->flash('success', 'Programación registrada correctamente.');
                return response()->turboStream()
                    ->action('redirect')
                    ->attributes(['url' => route('admin.planning.index')]);
            }

            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al guardar la programación: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $planning = Planning::with('helpers')->findOrFail($id);

        if ($planning->state !== 'active') {
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se puede editar una programación finalizada.');
        }

        $groups = StaffGroup::where('status', 'active')
            ->orWhere('id', $planning->staff_group_id)
            ->with(['driver', 'helpers', 'shift', 'zone', 'vehicle'])
            ->orderBy('name')
            ->get();

        $drivers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', 'Conductor');
            })
            ->orWhere('id', $planning->driver_id)
            ->orderBy('name')
            ->get();

        $helpers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', '!=', 'Conductor');
            })
            ->orWhereIn('id', $planning->helpers->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('admin.plannings.edit', compact('planning', 'groups', 'drivers', 'helpers'));
    }

    public function update(Request $request, string $id)
    {
        $planning = Planning::findOrFail($id);

        if ($planning->state !== 'active') {
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se puede editar una programación finalizada.');
        }

        $request->validate([
            'driver_id' => 'required|exists:staff,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'days' => 'required|array|min:1',
            'helpers' => 'nullable|array',
            'helpers.*' => 'exists:staff,id',
        ]);

        // Backend availability check
        $validationResponse = $this->runAvailabilityCheck(
            $request->date_start,
            $request->date_end,
            $request->driver_id,
            $request->input('helpers', []),
            $request->days,
            $planning->staff_group_id,
            $planning->id
        );

        if (!$validationResponse['valid']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['availability' => 'No se pudo actualizar debido a inconsistencias de disponibilidad.']);
        }

        DB::beginTransaction();
        try {
            $changesLog = [];

            if (Carbon::parse($planning->date_start)->format('Y-m-d') !== $request->date_start) {
                $changesLog[] = "Fecha inicio: " . Carbon::parse($planning->date_start)->format('d/m/Y') . " -> " . Carbon::parse($request->date_start)->format('d/m/Y');
            }
            if (Carbon::parse($planning->date_end)->format('Y-m-d') !== $request->date_end) {
                $changesLog[] = "Fecha fin: " . Carbon::parse($planning->date_end)->format('d/m/Y') . " -> " . Carbon::parse($request->date_end)->format('d/m/Y');
            }
            if ($planning->driver_id != $request->driver_id) {
                $oldDriver = Staff::find($planning->driver_id);
                $newDriver = Staff::find($request->driver_id);
                $changesLog[] = "Conductor: " . ($oldDriver ? $oldDriver->name : 'Ninguno') . " -> " . ($newDriver ? $newDriver->name : 'Ninguno');
            }

            $oldHelpers = $planning->helpers->pluck('id')->toArray();
            $newHelpers = array_map('intval', $request->input('helpers', []));
            sort($oldHelpers);
            sort($newHelpers);
            if ($oldHelpers !== $newHelpers) {
                $changesLog[] = "Se actualizaron los ayudantes del grupo.";
            }

            $oldDays = $planning->days ?? [];
            $newDays = $request->days ?? [];
            sort($oldDays);
            sort($newDays);
            if ($oldDays !== $newDays) {
                $changesLog[] = "Días programados: [" . implode(', ', $oldDays) . "] -> [" . implode(', ', $newDays) . "]";
            }

            $planning->update([
                'driver_id' => $request->driver_id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'days' => $request->days,
            ]);

            $planning->helpers()->sync($request->input('helpers', []));

            if (!empty($changesLog)) {
                PlanningChange::create([
                    'planning_id' => $planning->id,
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'details' => implode("\n", $changesLog),
                ]);
            }

            DB::commit();

            if ($request->wantsTurboStream()) {
                session()->flash('success', 'Programación actualizada correctamente.');
                return response()->turboStream()
                    ->action('redirect')
                    ->attributes(['url' => route('admin.planning.index')]);
            }

            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar: ' . $e->getMessage());
        }
    }

    public function finish(string $id)
    {
        $planning = Planning::findOrFail($id);

        if ($planning->state === 'finished') {
            return redirect()->route('admin.planning.index')
                ->with('error', 'La programación ya se encuentra finalizada.');
        }

        DB::beginTransaction();
        try {
            $planning->update(['state' => 'finished']);

            PlanningChange::create([
                'planning_id' => $planning->id,
                'user_id' => auth()->id(),
                'action' => 'finished',
                'details' => 'Programación finalizada.',
            ]);

            DB::commit();
            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación finalizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se pudo finalizar la programación: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $planning = Planning::findOrFail($id);

        DB::beginTransaction();
        try {
            PlanningChange::create([
                'planning_id' => null, // detach logging info since the planning is going to be deleted
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'details' => 'Programación del grupo ID ' . $planning->staff_group_id . ' (' . ($planning->staffGroup?->name ?? 'Grupo Eliminado') . ') eliminada permanentemente.',
            ]);

            $planning->delete();

            DB::commit();
            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se pudo eliminar la programación: ' . $e->getMessage());
        }
    }

    public function history(string $id)
    {
        $planning = Planning::findOrFail($id);
        $history = PlanningChange::where('planning_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.plannings.history-modal', compact('planning', 'history'));
    }

    public function validateAvailability(Request $request)
    {
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $driverId = $request->input('driver_id');
        $helperIds = $request->input('helpers', []);
        $days = $request->input('days', []);
        $groupId = $request->input('staff_group_id');
        $planningId = $request->input('planning_id');

        $result = $this->runAvailabilityCheck($dateStart, $dateEnd, $driverId, $helperIds, $days, $groupId, $planningId);

        return response()->json($result);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:staff_groups,id',
        ]);

        $dateStart = $request->date_start;
        $dateEnd = $request->date_end;
        $groupIds = $request->group_ids;

        $createdCount = 0;
        $skippedGroups = [];

        DB::beginTransaction();
        try {
            foreach ($groupIds as $groupId) {
                $group = StaffGroup::with(['helpers'])->find($groupId);
                if (!$group) continue;

                $driverId = $group->driver_id;
                $helperIds = $group->helpers->pluck('id')->toArray();
                $days = $group->days;

                // Validate each group's availability
                $check = $this->runAvailabilityCheck($dateStart, $dateEnd, $driverId, $helperIds, $days, $groupId);

                if (!$check['valid']) {
                    $skippedGroups[] = $group->name . ' (' . implode(', ', $check['errors']) . ')';
                    continue;
                }

                $planning = Planning::create([
                    'staff_group_id' => $groupId,
                    'driver_id' => $driverId,
                    'date_start' => $dateStart,
                    'date_end' => $dateEnd,
                    'days' => $days,
                    'state' => 'active',
                ]);

                $planning->helpers()->sync($helperIds);

                PlanningChange::create([
                    'planning_id' => $planning->id,
                    'user_id' => auth()->id(),
                    'action' => 'created',
                    'details' => 'Programación masiva registrada.',
                ]);

                $createdCount++;
            }

            DB::commit();

            $msg = "Se generaron {$createdCount} programaciones exitosamente.";
            if (count($skippedGroups) > 0) {
                $msg .= " Se omitieron los siguientes grupos por inconsistencias de disponibilidad:\n- " . implode("\n- ", $skippedGroups);
                return redirect()->route('admin.planning.index')->with('success', $msg);
            }

            return redirect()->route('admin.planning.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.planning.index')
                ->with('error', 'Ocurrió un error en la programación masiva: ' . $e->getMessage());
        }
    }

    // --- Helper Validation Methods ---

    protected function runAvailabilityCheck($dateStart, $dateEnd, $driverId, $helperIds, $days, $groupId, $planningId = null): array
    {
        if (!$dateStart || !$dateEnd || empty($days)) {
            return [
                'valid' => false,
                'errors' => ['Debe completar fecha de inicio, fin y seleccionar días.'],
                'suggestions' => []
            ];
        }

        $scheduledDates = $this->getScheduledDates($dateStart, $dateEnd, $days);

        if (empty($scheduledDates)) {
            return [
                'valid' => false,
                'errors' => ['No existen días hábiles coincidentes en el rango seleccionado.'],
                'suggestions' => []
            ];
        }

        $errors = [];
        $suggestions = [];

        // Validate Driver
        if ($driverId) {
            $driver = Staff::find($driverId);
            if ($driver) {
                $driverCheck = $this->checkStaffAvailability($driver, $scheduledDates, $dateStart, $dateEnd, $planningId);
                if (!$driverCheck['available']) {
                    $errors = array_merge($errors, $driverCheck['errors']);
                    $suggestions = array_merge($suggestions, $this->getDriverSuggestions($driver, $scheduledDates, $dateStart, $dateEnd, $planningId));
                }
            }
        }

        // Validate Helpers
        foreach ($helperIds as $helperId) {
            $helper = Staff::find($helperId);
            if ($helper) {
                $helperCheck = $this->checkStaffAvailability($helper, $scheduledDates, $dateStart, $dateEnd, $planningId);
                if (!$helperCheck['available']) {
                    $errors = array_merge($errors, $helperCheck['errors']);
                    $suggestions = array_merge($suggestions, $this->getHelperSuggestions($helper, $scheduledDates, $dateStart, $dateEnd, $planningId));
                }
            }
        }

        // Validate Vehicle Overlap
        if ($groupId) {
            $group = StaffGroup::with('vehicle')->find($groupId);
            if ($group && $group->vehicle) {
                $vehicleOverlapDates = [];

                $overlappingPlannings = Planning::where('id', '!=', $planningId)
                    ->where('state', 'active')
                    ->where(function ($q) use ($dateStart, $dateEnd) {
                        $q->whereBetween('date_start', [$dateStart, $dateEnd])
                          ->orWhereBetween('date_end', [$dateStart, $dateEnd])
                          ->orWhere(function ($sq) use ($dateStart, $dateEnd) {
                              $sq->where('date_start', '<=', $dateStart)
                                 ->where('date_end', '>=', $dateEnd);
                          });
                    })
                    ->whereHas('staffGroup', function ($g) use ($group) {
                        $g->where('vehicle_id', $group->vehicle_id);
                    })
                    ->get();

                foreach ($overlappingPlannings as $op) {
                    $opDates = $this->getScheduledDates($op->date_start, $op->date_end, $op->days);
                    $opFormatted = array_map(fn($d) => $d->format('Y-m-d'), $opDates);

                    foreach ($scheduledDates as $sd) {
                        if (in_array($sd->format('Y-m-d'), $opFormatted)) {
                            $vehicleOverlapDates[] = $sd->format('d/m/Y');
                        }
                    }
                }

                if (!empty($vehicleOverlapDates)) {
                    $uniqueDates = array_values(array_unique($vehicleOverlapDates));
                    $firstDate = $uniqueDates[0];
                    $extraCount = count($uniqueDates) - 1;
                    $affText = $extraCount > 0 ? " y {$extraCount} días más" : "";
                    $errors[] = "Vehículo (Placa {$group->vehicle->plate}): No disponible por cruce con otra programación activa (afecta {$firstDate}{$affText})";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'suggestions' => $suggestions
        ];
    }

    protected function getScheduledDates($dateStart, $dateEnd, array $days): array
    {
        $start = Carbon::parse($dateStart);
        $end = Carbon::parse($dateEnd);
        $dates = [];

        $dayMap = [
            'lunes' => 'Monday',
            'martes' => 'Tuesday',
            'miercoles' => 'Wednesday',
            'jueves' => 'Thursday',
            'viernes' => 'Friday',
            'sabado' => 'Saturday',
            'domingo' => 'Sunday',
        ];

        $targetDays = array_map(function ($d) use ($dayMap) {
            return $dayMap[strtolower($d)] ?? null;
        }, $days);
        $targetDays = array_filter($targetDays);

        if (empty($targetDays)) {
            return [];
        }

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (in_array($date->format('l'), $targetDays)) {
                $dates[] = $date->copy();
            }
        }

        return $dates;
    }

    protected function checkStaffAvailability(Staff $staff, array $scheduledDates, $dateStart, $dateEnd, $planningId = null): array
    {
        $errors = [];
        $name = $staff->name . ' ' . $staff->last_name;

        // A. Contract Validity Check
        $contracts = Contract::where('staff_id', $staff->id)
            ->where('state', 'active')
            ->get();

        $uncoveredDates = [];
        foreach ($scheduledDates as $sd) {
            $covered = false;
            foreach ($contracts as $contract) {
                $cStart = Carbon::parse($contract->date_start);
                $cEnd = $contract->date_end ? Carbon::parse($contract->date_end) : null;
                if ($sd->gte($cStart) && (!$cEnd || $sd->lte($cEnd))) {
                    $covered = true;
                    break;
                }
            }
            if (!$covered) {
                $uncoveredDates[] = $sd->format('d/m/Y');
            }
        }

        if (!empty($uncoveredDates)) {
            $firstDate = $uncoveredDates[0];
            $extraCount = count($uncoveredDates) - 1;
            $affText = $extraCount > 0 ? " y {$extraCount} días más" : "";
            $errors[] = "{$name}: Empleado no disponible por razones de contrato (afecta {$firstDate}{$affText})";
        }

        // B. Vacations Check
        $vacations = Vacation::where('staff_id', $staff->id)
            ->where('state', 'approved')
            ->where(function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('date_start', [$dateStart, $dateEnd])
                  ->orWhereBetween('date_end', [$dateStart, $dateEnd])
                  ->orWhere(function ($sq) use ($dateStart, $dateEnd) {
                      $sq->where('date_start', '<=', $dateStart)
                         ->where('date_end', '>=', $dateEnd);
                  });
            })
            ->get();

        $vacationConflicts = [];
        foreach ($vacations as $vacation) {
            $vStart = Carbon::parse($vacation->date_start);
            $vEnd = Carbon::parse($vacation->date_end);

            foreach ($scheduledDates as $sd) {
                if ($sd->gte($vStart) && $sd->lte($vEnd)) {
                    $vacationConflicts[] = $sd->format('d/m/Y');
                }
            }
        }

        if (!empty($vacationConflicts)) {
            $uniqueDates = array_values(array_unique($vacationConflicts));
            $firstDate = $uniqueDates[0];
            $extraCount = count($uniqueDates) - 1;
            $affText = $extraCount > 0 ? " y {$extraCount} días más" : "";
            $errors[] = "{$name}: Tiene vacaciones aprobadas para la fecha {$firstDate} (afecta {$firstDate}{$affText})";
        }

        // C. Cruces de Programaciones Activas
        $overlappingPlannings = Planning::where('id', '!=', $planningId)
            ->where('state', 'active')
            ->where(function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('date_start', [$dateStart, $dateEnd])
                  ->orWhereBetween('date_end', [$dateStart, $dateEnd])
                  ->orWhere(function ($sq) use ($dateStart, $dateEnd) {
                      $sq->where('date_start', '<=', $dateStart)
                         ->where('date_end', '>=', $dateEnd);
                  });
            })
            ->where(function ($q) use ($staff) {
                $q->where('driver_id', $staff->id)
                  ->orWhereHas('helpers', function ($h) use ($staff) {
                      $h->where('staff_id', $staff->id);
                  });
            })
            ->get();

        $planningConflicts = [];
        foreach ($overlappingPlannings as $op) {
            $opDates = $this->getScheduledDates($op->date_start, $op->date_end, $op->days);
            $opFormatted = array_map(fn($d) => $d->format('Y-m-d'), $opDates);

            foreach ($scheduledDates as $sd) {
                if (in_array($sd->format('Y-m-d'), $opFormatted)) {
                    $planningConflicts[] = $sd->format('d/m/Y');
                }
            }
        }

        if (!empty($planningConflicts)) {
            $uniqueDates = array_values(array_unique($planningConflicts));
            $firstDate = $uniqueDates[0];
            $extraCount = count($uniqueDates) - 1;
            $affText = $extraCount > 0 ? " y {$extraCount} días más" : "";
            $errors[] = "{$name}: Empleado ya programado en otro grupo activo (afecta {$firstDate}{$affText})";
        }

        return [
            'available' => empty($errors),
            'errors' => $errors
        ];
    }

    protected function getDriverSuggestions(Staff $driver, array $scheduledDates, $dateStart, $dateEnd, $planningId = null): array
    {
        $suggestions = [];
        $candidates = Staff::where('id', '!=', $driver->id)
            ->where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', 'Conductor');
            })
            ->get();

        $count = 0;
        foreach ($candidates as $candidate) {
            $res = $this->checkStaffAvailability($candidate, $scheduledDates, $dateStart, $dateEnd, $planningId);
            if ($res['available']) {
                $suggestions[] = [
                    'conflicting_staff_id' => $driver->id,
                    'conflicting_staff_name' => $driver->name . ' ' . $driver->last_name,
                    'suggested_staff_id' => $candidate->id,
                    'suggested_staff_name' => $candidate->name . ' ' . $candidate->last_name,
                    'type' => 'driver'
                ];
                $count++;
                if ($count >= 3) break;
            }
        }

        return $suggestions;
    }

    protected function getHelperSuggestions(Staff $helper, array $scheduledDates, $dateStart, $dateEnd, $planningId = null): array
    {
        $suggestions = [];
        $candidates = Staff::where('id', '!=', $helper->id)
            ->where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', '!=', 'Conductor');
            })
            ->get();

        $count = 0;
        foreach ($candidates as $candidate) {
            $res = $this->checkStaffAvailability($candidate, $scheduledDates, $dateStart, $dateEnd, $planningId);
            if ($res['available']) {
                $suggestions[] = [
                    'conflicting_staff_id' => $helper->id,
                    'conflicting_staff_name' => $helper->name . ' ' . $helper->last_name,
                    'suggested_staff_id' => $candidate->id,
                    'suggested_staff_name' => $candidate->name . ' ' . $candidate->last_name,
                    'type' => 'helper'
                ];
                $count++;
                if ($count >= 3) break;
            }
        }

        return $suggestions;
    }
}
