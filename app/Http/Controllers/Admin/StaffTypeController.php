<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = StaffType::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        $types = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.staff-types.index', compact('types', 'search'));
    }

    public function create()
    {
        return view('admin.staff-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:staff_types,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tipo de personal con ese nombre.',
        ]);

        StaffType::create($request->only(['name', 'description']));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Tipo de personal registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.staff-type.index')]);
        }

        return redirect()->route('admin.staff-type.index')
            ->with('success', 'Tipo de personal registrado correctamente.');
    }

    public function edit(string $id)
    {
        $type = StaffType::findOrFail($id);
        return view('admin.staff-types.edit', compact('type'));
    }

    public function update(Request $request, string $id)
    {
        $type = StaffType::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('staff_types', 'name')->ignore($type->id),
            ],
            'description' => 'nullable|string',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tipo de personal con ese nombre.',
        ]);

        $type->update($request->only(['name', 'description']));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Tipo de personal actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.staff-type.index')]);
        }

        return redirect()->route('admin.staff-type.index')
            ->with('success', 'Tipo de personal actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $type = StaffType::findOrFail($id);

        if ($type->staff()->exists()) {
            return redirect()->route('admin.staff-type.index')
                ->with('error', 'No se puede eliminar el tipo de personal porque tiene miembros asociados.');
        }

        $type->delete();

        return redirect()->route('admin.staff-type.index')
            ->with('success', 'Tipo de personal eliminado correctamente.');
    }
}
