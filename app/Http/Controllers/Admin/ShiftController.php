<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;
        
        $query = Shift::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        $shifts = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('admin.shifts.index', compact('shifts', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        Shift::create($this->shiftData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Turno registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.shift.index')]);
        }

        return redirect()->route('admin.shift.index')
            ->with('success', 'Turno registrado correctamente.');
    }

    public function edit(string $id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, string $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate($this->rules(), $this->messages());

        $shift->update($this->shiftData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Turno actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.shift.index')]);
        }

        return redirect()->route('admin.shift.index')
            ->with('success', 'Turno actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('admin.shift.index')
            ->with('success', 'Turno eliminado correctamente.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i',
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'El nombre del turno es obligatorio.',
            'name.max' => 'El nombre del turno no debe superar los 100 caracteres.',
            'check_in.required' => 'La hora de entrada es obligatoria.',
            'check_in.date_format' => 'La hora de entrada debe tener el formato HH:MM (24 horas).',
            'check_out.required' => 'La hora de salida es obligatoria.',
            'check_out.date_format' => 'La hora de salida debe tener el formato HH:MM (24 horas).',
        ];
    }

    private function shiftData(Request $request): array
    {
        return $request->only([
            'name',
            'description',
            'check_in',
            'check_out',
        ]);
    }
}
