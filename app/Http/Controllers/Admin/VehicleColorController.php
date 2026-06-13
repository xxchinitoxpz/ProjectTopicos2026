<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleColor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleColorController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = VehicleColor::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        $colors = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('admin.vehicle-colors.index', compact('colors', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.vehicle-colors.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        VehicleColor::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
        ]);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Color registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vehicle-color.index')]);
        }

        return redirect()->route('admin.vehicle-color.index')
            ->with('success', 'Color registrado correctamente.');
    }

    public function edit(string $id)
    {
        $color = VehicleColor::findOrFail($id);
        return view('admin.vehicle-colors.edit', compact('color'));
    }

    public function update(Request $request, string $id)
    {
        $color = VehicleColor::findOrFail($id);

        $request->validate($this->rules($color->id), $this->messages());

        $color->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
        ]);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Color actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vehicle-color.index')]);
        }

        return redirect()->route('admin.vehicle-color.index')
            ->with('success', 'Color actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $color = VehicleColor::findOrFail($id);

        if ($color->vehicles()->exists()) {
            return redirect()->route('admin.vehicle-color.index')
                ->with('error', 'No se puede eliminar el color porque tiene vehículos asociados.');
        }

        $color->delete();

        return redirect()->route('admin.vehicle-color.index')
            ->with('success', 'Color eliminado correctamente.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehicle_colors', 'name')->ignore($ignoreId),
            ],
            'code' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un color con ese nombre.',
            'code.required' => 'Debe seleccionar un color en la paleta.',
            'code.regex' => 'El código debe tener formato hexadecimal, por ejemplo: #808080.',
        ];
    }
}
