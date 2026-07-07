<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\PlanningDay;
use App\Models\PlanningChange;
use App\Models\Motive;
use App\Models\StaffGroup;
use App\Models\Staff;
use App\Models\Vacation;
use App\Models\Contract;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateStart = $request->input('date_start', now()->toDateString());
        $dateEnd = $request->input('date_end', now()->copy()->addDays(14)->toDateString());
        $stateFilter = $request->input('state');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = PlanningDay::query()->with(['planning.staffGroup.zone', 'shift', 'vehicle', 'driver', 'helpers']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('planning.staffGroup', function ($g) use ($search) {
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
            $query->where('date', '>=', $dateStart);
        }

        if ($dateEnd) {
            $query->where('date', '<=', $dateEnd);
        }

        if ($stateFilter) {
            $query->where('state', $stateFilter);
        }

        $plannings = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        
        // Load active groups for mass scheduling
        $groups = StaffGroup::where('status', 'active')
            ->with(['driver', 'helpers', 'shift', 'zone', 'vehicle'])
            ->orderBy('name')
            ->get();

        // Load all active conductors and helpers for bulk customization
        $drivers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', 'Conductor');
            })
            ->orderBy('name')
            ->get();

        $helpers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($q) {
                $q->where('name', '!=', 'Conductor');
            })
            ->orderBy('name')
            ->get();

        return view('admin.plannings.index', compact('plannings', 'groups', 'drivers', 'helpers', 'search', 'dateStart', 'dateEnd', 'stateFilter', 'perPage'));
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
            $group = StaffGroup::findOrFail($request->staff_group_id);

            // Create Parent Planning Header
            $planning = Planning::create([
                'staff_group_id' => $request->staff_group_id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'days' => $request->days,
            ]);

            // Get scheduled dates in range
            $dates = $this->getScheduledDates($request->date_start, $request->date_end, $request->days);

            foreach ($dates as $date) {
                // Create PlanningDay detail, copying template group defaults initially
                $planningDay = PlanningDay::create([
                    'planning_id' => $planning->id,
                    'date' => $date->format('Y-m-d'),
                    'shift_id' => $group->shift_id,
                    'vehicle_id' => $group->vehicle_id,
                    'driver_id' => $request->driver_id,
                    'state' => 'active',
                ]);

                // Sync helpers
                if ($request->has('helpers')) {
                    $planningDay->helpers()->sync($request->helpers);
                }

                // Log creation
                PlanningChange::create([
                    'planning_day_id' => $planningDay->id,
                    'user_id' => auth()->id(),
                    'change_type' => 'creacion',
                    'old_value' => 'N/A',
                    'new_value' => 'Creado',
                    'reason_type' => 'Programación Individual',
                    'details' => 'Programación del día creada.',
                ]);
            }

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
        $planning = PlanningDay::with(['helpers', 'planning.staffGroup', 'shift', 'vehicle'])->findOrFail($id);

        if ($planning->state !== 'active' && $planning->state !== 'reprogramado') {
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se puede editar una programación finalizada.');
        }

        $groups = StaffGroup::where('status', 'active')
            ->orWhere('id', $planning->planning->staff_group_id)
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

        $allStaff = Staff::where('status', 'active')->orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('plate')->get();

        $motives = Motive::where('status', 'activo')->orderBy('name')->get();

        return view('admin.plannings.edit', compact('planning', 'groups', 'drivers', 'allStaff', 'shifts', 'vehicles', 'motives'));
    }

    public function update(Request $request, string $id)
    {
        $planningDay = PlanningDay::findOrFail($id);

        if ($planningDay->state !== 'active' && $planningDay->state !== 'reprogramado') {
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se puede editar una programación finalizada.');
        }

        $request->validate([
            'changes' => 'required|string', // JSON changes array
        ]);

        $changes = json_decode($request->input('changes'), true);

        if (empty($changes)) {
            return redirect()->route('admin.planning.index')
                ->with('info', 'No se registraron cambios para guardar.');
        }

        DB::beginTransaction();
        try {
            foreach ($changes as $change) {
                $type = $change['type']; // 'turno', 'vehiculo', 'conductor', 'helper'
                $newId = $change['new_id'];
                $oldId = $change['old_id'];
                $reasonType = $change['reason_type'];
                $details = $change['details'] ?? '';

                if ($type === 'turno') {
                    $planningDay->update(['shift_id' => $newId]);
                    $oldShift = Shift::find($oldId);
                    $newShift = Shift::find($newId);

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'turno',
                        'old_value' => $oldShift ? $oldShift->name : 'N/A',
                        'new_value' => $newShift ? $newShift->name : 'N/A',
                        'reason_type' => $reasonType,
                        'details' => $details,
                    ]);
                } elseif ($type === 'vehiculo') {
                    $planningDay->update(['vehicle_id' => $newId]);
                    $oldVehicle = Vehicle::find($oldId);
                    $newVehicle = Vehicle::find($newId);

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'vehiculo',
                        'old_value' => $oldVehicle ? "Vehículo {$oldVehicle->plate}" : 'N/A',
                        'new_value' => $newVehicle ? "Vehículo {$newVehicle->plate}" : 'N/A',
                        'reason_type' => $reasonType,
                        'details' => $details,
                    ]);
                } elseif ($type === 'conductor') {
                    $planningDay->update(['driver_id' => $newId]);
                    $oldDriver = Staff::find($oldId);
                    $newDriver = Staff::find($newId);

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'conductor',
                        'old_value' => $oldDriver ? "{$oldDriver->name} {$oldDriver->last_name}" : 'Ninguno',
                        'new_value' => $newDriver ? "{$newDriver->name} {$newDriver->last_name}" : 'Ninguno',
                        'reason_type' => $reasonType,
                        'details' => $details,
                    ]);
                } elseif ($type === 'helper') {
                    $planningDay->helpers()->detach($oldId);
                    $planningDay->helpers()->attach($newId);

                    $oldHelper = Staff::find($oldId);
                    $newHelper = Staff::find($newId);

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'helper',
                        'old_value' => $oldHelper ? "{$oldHelper->name} {$oldHelper->last_name}" : 'Ninguno',
                        'new_value' => $newHelper ? "{$newHelper->name} {$newHelper->last_name}" : 'Ninguno',
                        'reason_type' => $reasonType,
                        'details' => $details,
                    ]);
                }
            }

            // Update status to reprogramado
            $planningDay->update(['state' => 'reprogramado']);

            DB::commit();

            if ($request->wantsTurboStream()) {
                session()->flash('success', 'Programación modificada correctamente.');
                return response()->turboStream()
                    ->action('redirect')
                    ->attributes(['url' => route('admin.planning.index')]);
            }

            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación modificada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Ocurrió un error al guardar los cambios: ' . $e->getMessage());
        }
    }

    public function finish(string $id)
    {
        $planningDay = PlanningDay::findOrFail($id);

        if ($planningDay->state === 'finished') {
            return redirect()->route('admin.planning.index')
                ->with('error', 'La programación de este día ya se encuentra finalizada.');
        }

        DB::beginTransaction();
        try {
            $planningDay->update(['state' => 'finished']);

            PlanningChange::create([
                'planning_day_id' => $planningDay->id,
                'user_id' => auth()->id(),
                'change_type' => 'finalizacion',
                'old_value' => 'Activa',
                'new_value' => 'Finalizada',
                'reason_type' => 'Estado',
                'details' => 'Programación del día finalizada.',
            ]);

            DB::commit();
            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación del día finalizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se pudo finalizar la programación: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $planningDay = PlanningDay::findOrFail($id);

        DB::beginTransaction();
        try {
            // Cascade delete deletes logs and helpers relationships automatically
            $planningDay->delete();

            DB::commit();
            return redirect()->route('admin.planning.index')
                ->with('success', 'Programación del día eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.planning.index')
                ->with('error', 'No se pudo eliminar la programación: ' . $e->getMessage());
        }
    }

    public function history(string $id)
    {
        $planning = PlanningDay::with(['helpers', 'planning.staffGroup'])->findOrFail($id);
        $history = PlanningChange::where('planning_day_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.plannings.history-modal', compact('planning', 'history'));
    }

    public function getHolidays(Request $request)
    {
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        if (!$dateStart || !$dateEnd) {
            return response()->json([]);
        }

        $holidays = Holiday::where('state', 'activo')
            ->whereBetween('date', [$dateStart, $dateEnd])
            ->orderBy('date')
            ->get()
            ->map(function ($h) {
                return [
                    'date' => $h->date->format('Y-m-d'),
                    'formatted_date' => $h->date->format('d/m/Y'),
                    'description' => $h->description,
                    'day_name' => $h->day_name
                ];
            });

        return response()->json($holidays);
    }

    public function validateAvailability(Request $request)
    {
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $driverId = $request->input('driver_id');
        $helperIds = $request->input('helpers', []);
        $days = $request->input('days', []);
        $groupId = $request->input('staff_group_id');
        $planningDayId = $request->input('planning_id');
        $excludedHolidays = $request->input('excluded_holidays', []);

        // Bulk validation mode
        $groups = $request->input('groups');
        if (is_string($groups)) {
            $groups = json_decode($groups, true);
        }

        if ($groups && is_array($groups)) {
            $results = [];
            $allValid = true;

            foreach ($groups as $gInfo) {
                $gId = $gInfo['staff_group_id'];
                $gDriverId = $gInfo['driver_id'];
                $gHelperIds = $gInfo['helpers'] ?? [];
                
                $groupModel = StaffGroup::find($gId);
                if (!$groupModel) continue;

                $gDays = !empty($gInfo['days']) && is_array($gInfo['days'])
                    ? $gInfo['days']
                    : $groupModel->days;
                $gScheduledDates = $this->getScheduledDates($dateStart, $dateEnd, $gDays);
                $gScheduledDates = array_filter($gScheduledDates, function ($date) use ($excludedHolidays) {
                    return !in_array($date->format('Y-m-d'), $excludedHolidays);
                });

                $check = $this->runAvailabilityCheckForDates($gScheduledDates, $gDriverId, $gHelperIds, $gId);
                
                $start = Carbon::parse($dateStart);
                $end = Carbon::parse($dateEnd);
                $totalDays = $start->diffInDays($end) + 1;
                $scheduledCount = count($gScheduledDates);
                $uncoveredCount = $totalDays - $scheduledCount;

                $warnings = [];
                if ($uncoveredCount > 0) {
                    $warnings[] = "{$uncoveredCount} días no cubiertos (el grupo solo trabaja: " . implode(', ', array_map('ucfirst', $gDays)) . ")";
                }

                // Check overlap
                $existingPlannings = PlanningDay::where('state', 'active')
                    ->whereIn('date', array_map(fn($d) => $d->format('Y-m-d'), $gScheduledDates))
                    ->whereHas('planning', function ($q) use ($gId) {
                        $q->where('staff_group_id', $gId);
                    })
                    ->pluck('date')
                    ->map(fn($d) => $d->format('d/m/Y'))
                    ->toArray();

                if (!empty($existingPlannings)) {
                    $firstEx = $existingPlannings[0];
                    $extraEx = count($existingPlannings) - 1;
                    $exText = $extraEx > 0 ? " y {$extraEx} de más" : "";
                    $warnings[] = "Programaciones existentes: {$firstEx}{$exText}";
                }

                $results[$gId] = [
                    'group_name' => $groupModel->name,
                    'valid' => $check['valid'],
                    'errors' => $check['errors'],
                    'warnings' => $warnings,
                    'suggestions' => $check['suggestions']
                ];

                if (!$check['valid']) {
                    $allValid = false;
                }
            }

            return response()->json([
                'valid' => $allValid,
                'results' => $results
            ]);
        }

        // Single group validation mode
        $result = $this->runAvailabilityCheck($dateStart, $dateEnd, $driverId, $helperIds, $days, $groupId, $planningDayId, $excludedHolidays);
        return response()->json($result);
    }

    public function bulkStore(Request $request)
    {
        if (is_string($request->input('group_assignments'))) {
            $request->merge([
                'group_assignments' => json_decode($request->input('group_assignments'), true)
            ]);
        }

        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:staff_groups,id',
            'excluded_holidays' => 'nullable|array',
            'group_assignments' => 'nullable|array',
        ]);

        $dateStart = $request->date_start;
        $dateEnd = $request->date_end;
        $groupIds = $request->group_ids;
        $excludedHolidays = $request->input('excluded_holidays', []);
        $groupAssignments = $request->input('group_assignments', []);

        $createdCount = 0;
        $skippedGroups = [];

        DB::beginTransaction();
        try {
            foreach ($groupIds as $groupId) {
                $group = StaffGroup::with(['helpers'])->find($groupId);
                if (!$group) continue;

                $driverId = isset($groupAssignments[$groupId]['driver_id']) && $groupAssignments[$groupId]['driver_id']
                    ? $groupAssignments[$groupId]['driver_id']
                    : $group->driver_id;

                $helperIds = isset($groupAssignments[$groupId]['helpers']) && is_array($groupAssignments[$groupId]['helpers'])
                    ? $groupAssignments[$groupId]['helpers']
                    : $group->helpers->pluck('id')->toArray();

                $days = isset($groupAssignments[$groupId]['days']) && is_array($groupAssignments[$groupId]['days']) && count($groupAssignments[$groupId]['days']) > 0
                    ? $groupAssignments[$groupId]['days']
                    : $group->days;

                $dates = $this->getScheduledDates($dateStart, $dateEnd, $days);
                
                $dates = array_filter($dates, function ($date) use ($excludedHolidays) {
                    return !in_array($date->format('Y-m-d'), $excludedHolidays);
                });

                if (empty($dates)) {
                    $skippedGroups[] = "{$group->name} (Sin fechas tras excluir feriados)";
                    continue;
                }

                $check = $this->runAvailabilityCheckForDates($dates, $driverId, $helperIds, $groupId);

                if (!$check['valid']) {
                    $skippedGroups[] = $group->name . ' (' . implode(', ', $check['errors']) . ')';
                    continue;
                }

                $planning = Planning::create([
                    'staff_group_id' => $groupId,
                    'date_start' => $dateStart,
                    'date_end' => $dateEnd,
                    'days' => $days,
                ]);

                foreach ($dates as $date) {
                    $planningDay = PlanningDay::create([
                        'planning_id' => $planning->id,
                        'date' => $date->format('Y-m-d'),
                        'shift_id' => $group->shift_id,
                        'vehicle_id' => $group->vehicle_id,
                        'driver_id' => $driverId,
                        'state' => 'active',
                    ]);

                    if (!empty($helperIds)) {
                        $planningDay->helpers()->sync($helperIds);
                    }

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'creacion',
                        'old_value' => 'N/A',
                        'new_value' => 'Creado',
                        'reason_type' => 'Asignación Masiva',
                        'details' => 'Programación diaria registrada mediante asignación masiva.',
                    ]);
                }

                $createdCount++;
            }

            DB::commit();

            $msg = "Se generaron programaciones de días para {$createdCount} grupos exitosamente.";
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

    protected function runAvailabilityCheck($dateStart, $dateEnd, $driverId, $helperIds, $days, $groupId, $planningDayId = null, $excludedHolidays = []): array
    {
        if (!$dateStart || !$dateEnd || empty($days)) {
            return [
                'valid' => false,
                'errors' => ['Debe completar fecha de inicio, fin y seleccionar días.'],
                'suggestions' => []
            ];
        }

        $scheduledDates = $this->getScheduledDates($dateStart, $dateEnd, $days);
        
        $scheduledDates = array_filter($scheduledDates, function ($date) use ($excludedHolidays) {
            return !in_array($date->format('Y-m-d'), $excludedHolidays);
        });

        return $this->runAvailabilityCheckForDates($scheduledDates, $driverId, $helperIds, $groupId, $planningDayId);
    }

    protected function runAvailabilityCheckForDates(array $scheduledDates, $driverId, $helperIds, $groupId, $planningDayId = null): array
    {
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
                $driverCheck = $this->checkStaffAvailability($driver, $scheduledDates, $planningDayId);
                if (!$driverCheck['available']) {
                    $errors = array_merge($errors, $driverCheck['errors']);
                    $suggestions = array_merge($suggestions, $this->getDriverSuggestions($driver, $scheduledDates, $planningDayId));
                }
            }
        }

        // Validate Helpers
        foreach ($helperIds as $helperId) {
            $helper = Staff::find($helperId);
            if ($helper) {
                $helperCheck = $this->checkStaffAvailability($helper, $scheduledDates, $planningDayId);
                if (!$helperCheck['available']) {
                    $errors = array_merge($errors, $helperCheck['errors']);
                    $suggestions = array_merge($suggestions, $this->getHelperSuggestions($helper, $scheduledDates, $planningDayId));
                }
            }
        }

        // Validate Vehicle Overlap
        if ($groupId) {
            $group = StaffGroup::with('vehicle')->find($groupId);
            if ($group && $group->vehicle) {
                $vehicleOverlapDates = [];

                foreach ($scheduledDates as $sd) {
                    $overlap = PlanningDay::where('id', '!=', $planningDayId)
                        ->where('state', 'active')
                        ->where('date', $sd->format('Y-m-d'))
                        ->whereHas('planning.staffGroup', function ($g) use ($group) {
                            $g->where('vehicle_id', $group->vehicle_id);
                        })
                        ->exists();

                    if ($overlap) {
                        $vehicleOverlapDates[] = $sd->format('d/m/Y');
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

    protected function checkStaffAvailability(Staff $staff, array $scheduledDates, $planningDayId = null): array
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
        $minDate = min($scheduledDates)->format('Y-m-d');
        $maxDate = max($scheduledDates)->format('Y-m-d');

        $vacations = Vacation::where('staff_id', $staff->id)
            ->where('state', 'approved')
            ->where(function ($q) use ($minDate, $maxDate) {
                $q->whereBetween('date_start', [$minDate, $maxDate])
                  ->orWhereBetween('date_end', [$minDate, $maxDate])
                  ->orWhere(function ($sq) use ($minDate, $maxDate) {
                      $sq->where('date_start', '<=', $minDate)
                         ->where('date_end', '>=', $maxDate);
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
        $planningConflicts = [];
        foreach ($scheduledDates as $sd) {
            $overlap = PlanningDay::where('id', '!=', $planningDayId)
                ->where('state', 'active')
                ->where('date', $sd->format('Y-m-d'))
                ->where(function ($q) use ($staff) {
                    $q->where('driver_id', $staff->id)
                      ->orWhereHas('helpers', function ($h) use ($staff) {
                          $h->where('staff_id', $staff->id);
                      });
                })
                ->exists();

            if ($overlap) {
                $planningConflicts[] = $sd->format('d/m/Y');
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

    protected function getDriverSuggestions(Staff $driver, array $scheduledDates, $planningDayId = null): array
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
            $res = $this->checkStaffAvailability($candidate, $scheduledDates, $planningDayId);
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

    protected function getHelperSuggestions(Staff $helper, array $scheduledDates, $planningDayId = null): array
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
            $res = $this->checkStaffAvailability($candidate, $scheduledDates, $planningDayId);
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
