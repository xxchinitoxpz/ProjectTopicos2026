<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Maintenance::query();

        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%');
        }

        $maintenances = $query->orderBy('fecha_inicio', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.maintenances.index', compact('maintenances', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.maintenances.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages(), $this->attributes());

        Maintenance::create($this->maintenanceData($request));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Mantenimiento registrado correctamente.',
                'redirect' => route('admin.maintenance.index'),
            ], 201);
        }

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Mantenimiento registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.maintenance.index')]);
        }

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Mantenimiento registrado correctamente.');
    }

    public function edit(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        return view('admin.maintenances.edit', compact('maintenance'));
    }

    public function update(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $request->validate($this->rules($maintenance->id), $this->messages(), $this->attributes());

        $maintenance->update($this->maintenanceData($request));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Mantenimiento actualizado correctamente.',
                'redirect' => route('admin.maintenance.index'),
            ]);
        }

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Mantenimiento actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.maintenance.index')]);
        }

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Mantenimiento eliminado correctamente.',
            ]);
        }

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Mantenimiento eliminado correctamente.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => [
                'required',
                'date',
                'after_or_equal:fecha_inicio',
                function (string $attribute, mixed $value, \Closure $fail) use ($ignoreId): void {
                    $start = request()->input('fecha_inicio');

                    if (!$start || !$value) {
                        return;
                    }

                    $overlap = Maintenance::query()
                        ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                        ->whereDate('fecha_inicio', '<=', $value)
                        ->whereDate('fecha_fin', '>=', $start)
                        ->exists();

                    if ($overlap) {
                        $fail('Las fechas se solapan con otro mantenimiento registrado.');
                    }
                },
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 150 caracteres.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio no es valida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin no es valida.',
            'fecha_fin.after_or_equal' => 'La fecha de inicio no puede ser mayor a la fecha de fin.',
        ];
    }

    private function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
        ];
    }

    private function maintenanceData(Request $request): array
    {
        return $request->only([
            'nombre',
            'fecha_inicio',
            'fecha_fin',
        ]);
    }
}
