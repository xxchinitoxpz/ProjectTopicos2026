<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = VehicleType::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        $types = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.vehicle-types.index', compact('types', 'search'));
    }

    public function create()
    {
        return view('admin.vehicle-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:vehicle_types,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tipo con ese nombre.',
        ]);

        VehicleType::create($request->only(['name', 'description']));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Tipo de vehículo registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vehicle-type.index')]);
        }

        return redirect()->route('admin.vehicle-type.index')
            ->with('success', 'Tipo de vehículo registrado correctamente.');
    }

    public function edit(string $id)
    {
        $type = VehicleType::findOrFail($id);
        return view('admin.vehicle-types.edit', compact('type'));
    }

    public function update(Request $request, string $id)
    {
        $type = VehicleType::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehicle_types', 'name')->ignore($type->id),
            ],
            'description' => 'nullable|string',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tipo con ese nombre.',
        ]);

        $type->update($request->only(['name', 'description']));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Tipo de vehículo actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vehicle-type.index')]);
        }

        return redirect()->route('admin.vehicle-type.index')
            ->with('success', 'Tipo de vehículo actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $type = VehicleType::findOrFail($id);

        if ($type->vehicles()->exists()) {
            return redirect()->route('admin.vehicle-type.index')
                ->with('error', 'No se puede eliminar el tipo porque tiene vehículos asociados.');
        }

        $type->delete();

        return redirect()->route('admin.vehicle-type.index')
            ->with('success', 'Tipo de vehículo eliminado correctamente.');
    }
}
