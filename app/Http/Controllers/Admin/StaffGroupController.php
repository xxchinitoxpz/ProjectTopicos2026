<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Zone;
use App\Models\Vehicle;
use App\Models\Staff;
use App\Models\StaffType;
use App\Models\StaffGroup;
use Illuminate\Http\Request;

class StaffGroupController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = StaffGroup::query()->with(['shift', 'zone', 'vehicle', 'driver', 'helpers']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('shift', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('zone', fn ($z) => $z->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('vehicle', fn ($v) => $v->where('plate', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
                  ->orWhereHas('driver', fn ($d) => $d->where('name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        $groups = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('admin.staff-groups.index', compact('groups', 'search', 'perPage'));
    }

    public function create()
    {
        $shifts = Shift::orderBy('name')->get();
        $zones = Zone::where('status', 'active')->orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('name')->get();

        // Obtener conductores
        $driverType = StaffType::where('name', 'Conductor')->first();
        $drivers = Staff::where('status', 'active')
            ->when($driverType, function($q) use ($driverType) {
                $q->where('staff_type_id', $driverType->id);
            })
            ->orderBy('name')
            ->get();

        // Obtener ayudantes (todos los demás operarios activos)
        $helpers = Staff::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.staff-groups.create', compact('shifts', 'zones', 'vehicles', 'drivers', 'helpers'));
    }

    public function store(Request $request)
    {
        $this->validateGroup($request);

        // Validar que el conductor no sea también ayudante
        if (in_array($request->driver_id, $request->input('helpers', []))) {
            return back()->withErrors(['helpers' => 'El conductor principal no puede ser seleccionado como ayudante del mismo grupo.'])->withInput();
        }

        $group = StaffGroup::create([
            'name'       => $request->name,
            'shift_id'   => $request->shift_id,
            'zone_id'    => $request->zone_id,
            'vehicle_id' => $request->vehicle_id,
            'driver_id'  => $request->driver_id,
            'days'       => $request->days,
            'status'     => $request->status,
        ]);

        if ($request->has('helpers')) {
            $group->helpers()->sync($request->helpers);
        }

        return redirect()->route('admin.staff-group.index')
            ->with('success', 'Grupo de personal registrado correctamente.');
    }

    public function edit(StaffGroup $staffGroup)
    {
        $staffGroup->load(['shift', 'zone', 'vehicle', 'driver', 'helpers']);
        
        $shifts = Shift::orderBy('name')->get();
        $zones = Zone::where('status', 'active')->orderBy('name')->get();
        $vehicles = Vehicle::where('status', 'active')->orderBy('name')->get();

        // Obtener conductores
        $driverType = StaffType::where('name', 'Conductor')->first();
        $drivers = Staff::where('status', 'active')
            ->when($driverType, function($q) use ($driverType) {
                $q->where('staff_type_id', $driverType->id);
            })
            ->orderBy('name')
            ->get();

        // Obtener ayudantes (todos los demás activos)
        $helpers = Staff::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.staff-groups.edit', compact('staffGroup', 'shifts', 'zones', 'vehicles', 'drivers', 'helpers'));
    }

    public function update(Request $request, StaffGroup $staffGroup)
    {
        $this->validateGroup($request);

        // Validar que el conductor no sea también ayudante
        if (in_array($request->driver_id, $request->input('helpers', []))) {
            return back()->withErrors(['helpers' => 'El conductor principal no puede ser seleccionado como ayudante del mismo grupo.'])->withInput();
        }

        $staffGroup->update([
            'name'       => $request->name,
            'shift_id'   => $request->shift_id,
            'zone_id'    => $request->zone_id,
            'vehicle_id' => $request->vehicle_id,
            'driver_id'  => $request->driver_id,
            'days'       => $request->days,
            'status'     => $request->status,
        ]);

        $staffGroup->helpers()->sync($request->input('helpers', []));

        return redirect()->route('admin.staff-group.index')
            ->with('success', 'Grupo de personal actualizado correctamente.');
    }

    public function destroy(StaffGroup $staffGroup)
    {
        $staffGroup->delete();

        return redirect()->route('admin.staff-group.index')
            ->with('success', 'Grupo de personal eliminado correctamente.');
    }

    private function validateGroup(Request $request): void
    {
        $request->validate([
            'name'       => 'required|string|max:150',
            'shift_id'   => 'required|exists:shifts,id',
            'zone_id'    => 'required|exists:zones,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id'  => 'required|exists:staff,id',
            'days'       => 'required|array|min:1',
            'days.*'     => 'required|string|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'status'     => 'required|in:active,inactive',
            'helpers'    => 'nullable|array',
            'helpers.*'  => 'exists:staff,id',
        ], [
            'name.required'       => 'El nombre del grupo es obligatorio.',
            'shift_id.required'   => 'Debe seleccionar un turno.',
            'zone_id.required'    => 'Debe seleccionar una zona.',
            'vehicle_id.required' => 'Debe seleccionar un vehículo.',
            'driver_id.required'  => 'Debe seleccionar un conductor principal.',
            'days.required'       => 'Debe seleccionar al menos un día de trabajo.',
            'days.min'            => 'Debe seleccionar al menos un día de trabajo.',
        ]);
    }
}
