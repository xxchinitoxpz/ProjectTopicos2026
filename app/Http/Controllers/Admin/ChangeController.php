<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motive;
use App\Models\PlanningChange;
use App\Models\PlanningDay;
use App\Models\Shift;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateStart = $request->input('date_start', now()->startOfMonth()->format('Y-m-d'));
        $dateEnd = $request->input('date_end', now()->format('Y-m-d'));
        $changeType = $request->input('change_type');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = PlanningChange::query()
            ->with([
                'user',
                'planningDay.planning.staffGroup',
                'planningDay.shift',
                'planningDay.vehicle',
                'planningDay.driver',
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('change_type', 'like', '%' . $search . '%')
                    ->orWhere('reason_type', 'like', '%' . $search . '%')
                    ->orWhere('old_value', 'like', '%' . $search . '%')
                    ->orWhere('new_value', 'like', '%' . $search . '%')
                    ->orWhere('details', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('planningDay.planning.staffGroup', function ($groupQuery) use ($search) {
                        $groupQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($dateStart) {
            $query->whereDate('created_at', '>=', $dateStart);
        }

        if ($dateEnd) {
            $query->whereDate('created_at', '<=', $dateEnd);
        }

        if ($changeType) {
            $query->where('change_type', $changeType);
        }

        $changes = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('admin.changes.index', compact('changes', 'search', 'dateStart', 'dateEnd', 'changeType', 'perPage'));
    }

    public function create()
    {
        $motives = Motive::where('status', 'activo')->orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $zones = Zone::where('status', 'active')->orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('code')->get();
        $planningDaysCount = PlanningDay::count();
        $drivers = Staff::where('status', 'active')
            ->whereHas('staffType', function ($query) {
                $query->where('name', 'Conductor');
            })
            ->orderBy('name')
            ->orderBy('last_name')
            ->get();

        return view('admin.changes.create', compact('motives', 'shifts', 'zones', 'vehicles', 'drivers', 'planningDaysCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'change_type' => ['required', 'in:turno,vehiculo,conductor'],
            'zone_ids' => ['nullable', 'array'],
            'zone_ids.*' => ['integer', 'exists:zones,id'],
            'old_target_id' => ['required', 'integer'],
            'new_target_id' => ['required', 'integer', 'different:old_target_id'],
            'reason_type' => ['required', 'string', 'max:150'],
            'additional_description' => ['nullable', 'string', 'max:255'],
            'full_description' => ['required', 'string'],
            'details' => ['nullable', 'string'],
        ], [
            'date_start.required' => 'La fecha de inicio es obligatoria.',
            'date_end.required' => 'La fecha de fin es obligatoria.',
            'date_end.after_or_equal' => 'La fecha fin no puede ser menor que la fecha inicio.',
            'change_type.required' => 'El tipo de cambio es obligatorio.',
            'old_target_id.required' => 'Debe seleccionar el valor a reemplazar.',
            'new_target_id.required' => 'Debe seleccionar el nuevo valor.',
            'new_target_id.different' => 'El nuevo valor debe ser diferente al valor a reemplazar.',
            'reason_type.required' => 'Debe seleccionar un motivo.',
            'full_description.required' => 'La descripcion completa del cambio es obligatoria.',
        ]);

        $planningDays = PlanningDay::query()
            ->with(['planning.staffGroup', 'shift', 'vehicle', 'driver'])
            ->whereBetween('date', [$validated['date_start'], $validated['date_end']])
            ->whereIn('state', ['active', 'reprogramado'])
            ->orderBy('date')
            ->when(!empty($validated['zone_ids']), function ($query) use ($validated) {
                $query->whereHas('planning.staffGroup', function ($planningQuery) use ($validated) {
                    $planningQuery->whereIn('zone_id', $validated['zone_ids']);
                });
            });

        if ($validated['change_type'] === 'turno') {
            $planningDays->where('shift_id', $validated['old_target_id']);
        } elseif ($validated['change_type'] === 'vehiculo') {
            $planningDays->where('vehicle_id', $validated['old_target_id']);
        } else {
            $planningDays->where('driver_id', $validated['old_target_id']);
        }

        $planningDays = $planningDays->get();

        if ($planningDays->isEmpty()) {
            if (PlanningDay::count() === 0) {
                return redirect()->route('admin.change.index')
                    ->with('error', 'No se registro el cambio porque no existen programaciones registradas para modificar.');
            }

            return redirect()->route('admin.change.index')
                ->with('error', 'No se encontraron programaciones que coincidan con los filtros seleccionados.');
        }

        $applied = 0;

        DB::beginTransaction();

        try {
            foreach ($planningDays as $planningDay) {
                if ($validated['change_type'] === 'turno') {
                    $oldShift = Shift::find($validated['old_target_id']);
                    $newShift = Shift::find($validated['new_target_id']);
                    if (!$oldShift || !$newShift || (int) $planningDay->shift_id !== (int) $oldShift->id) {
                        continue;
                    }

                    $oldValue = $oldShift->name;
                    $planningDay->update(['shift_id' => $newShift->id, 'state' => 'reprogramado']);

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'turno',
                        'old_value' => $oldValue,
                        'new_value' => $newShift->name,
                        'reason_type' => $validated['reason_type'],
                        'details' => $validated['full_description'],
                    ]);

                    $applied++;
                    continue;
                }

                if ($validated['change_type'] === 'vehiculo') {
                    $oldVehicle = Vehicle::find($validated['old_target_id']);
                    $newVehicle = Vehicle::find($validated['new_target_id']);
                    if (!$oldVehicle || !$newVehicle || (int) $planningDay->vehicle_id !== (int) $oldVehicle->id) {
                        continue;
                    }

                    $oldValue = $oldVehicle->code . ($oldVehicle->name ? ' - ' . $oldVehicle->name : '');
                    $newValue = $newVehicle->code . ($newVehicle->name ? ' - ' . $newVehicle->name : '');

                    $planningDay->update(['vehicle_id' => $newVehicle->id, 'state' => 'reprogramado']);

                    PlanningChange::create([
                        'planning_day_id' => $planningDay->id,
                        'user_id' => auth()->id(),
                        'change_type' => 'vehiculo',
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                        'reason_type' => $validated['reason_type'],
                        'details' => $validated['full_description'],
                    ]);

                    $applied++;
                    continue;
                }

                $oldDriver = Staff::find($validated['old_target_id']);
                $newDriver = Staff::find($validated['new_target_id']);
                if (!$oldDriver || !$newDriver || (int) $planningDay->driver_id !== (int) $oldDriver->id) {
                    continue;
                }

                $oldValue = trim($oldDriver->name . ' ' . $oldDriver->last_name);
                $newValue = trim($newDriver->name . ' ' . $newDriver->last_name);

                $planningDay->update(['driver_id' => $newDriver->id, 'state' => 'reprogramado']);

                PlanningChange::create([
                    'planning_day_id' => $planningDay->id,
                    'user_id' => auth()->id(),
                    'change_type' => 'conductor',
                    'old_value' => $oldValue ?: 'Sin conductor',
                    'new_value' => $newValue,
                    'reason_type' => $validated['reason_type'],
                    'details' => $validated['full_description'],
                ]);

                $applied++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('admin.change.index')
                ->with('error', 'No se pudo registrar el cambio masivo: ' . $e->getMessage());
        }

        if ($applied === 0) {
            return redirect()->route('admin.change.index')
                ->with('error', 'No se aplicaron cambios porque los nuevos valores ya estaban asignados.');
        }

        return redirect()->route('admin.change.index')
            ->with('success', "Cambio masivo aplicado correctamente a {$applied} programaciones.");
    }

    public function show(string $id)
    {
        $change = PlanningChange::with([
            'user',
            'planningDay.planning.staffGroup',
            'planningDay.shift',
            'planningDay.vehicle',
            'planningDay.driver',
        ])->findOrFail($id);

        return view('admin.changes.show', compact('change'));
    }

    public function destroy(string $id)
    {
        $change = PlanningChange::findOrFail($id);
        $change->delete();

        return redirect()->route('admin.change.index')
            ->with('success', 'Cambio eliminado correctamente.');
    }
}
