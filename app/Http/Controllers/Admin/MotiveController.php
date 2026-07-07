<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motive;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MotiveController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $stateFilter = $request->input('state');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Motive::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($stateFilter) {
            $query->where('status', $stateFilter);
        }

        $motives = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('admin.motives.index', compact('motives', 'search', 'stateFilter', 'perPage'));
    }

    public function create()
    {
        return view('admin.motives.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        Motive::create($this->motiveData($request));

        return redirect()->route('admin.motive.index')
            ->with('success', 'Motivo registrado correctamente.');
    }

    public function edit(string $id)
    {
        $motive = Motive::findOrFail($id);
        return view('admin.motives.edit', compact('motive'));
    }

    public function update(Request $request, string $id)
    {
        $motive = Motive::findOrFail($id);

        $request->validate($this->rules($id), $this->messages());

        $motive->update($this->motiveData($request));

        return redirect()->route('admin.motive.index')
            ->with('success', 'Motivo actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $motive = Motive::findOrFail($id);
        $motive->delete();

        return redirect()->route('admin.motive.index')
            ->with('success', 'Motivo eliminado correctamente.');
    }

    private function rules(?string $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('motives', 'name')->ignore($id),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|in:activo,inactivo',
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'El nombre del motivo es obligatorio.',
            'name.max' => 'El nombre del motivo no debe superar los 150 caracteres.',
            'name.unique' => 'Ya existe un motivo con ese nombre.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }

    private function motiveData(Request $request): array
    {
        return [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'activo'),
        ];
    }
}
