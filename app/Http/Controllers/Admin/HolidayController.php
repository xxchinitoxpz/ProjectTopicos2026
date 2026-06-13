<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $stateFilter = $request->input('state');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Holiday::query();

        if ($search) {
            $query->where('description', 'like', '%' . $search . '%');
        }

        if ($stateFilter) {
            $query->where('state', $stateFilter);
        }

        $holidays = $query->orderBy('date', 'desc')
                          ->paginate($perPage)
                          ->withQueryString();

        return view('admin.holidays.index', compact('holidays', 'search', 'stateFilter', 'perPage'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        Holiday::create($this->holidayData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Feriado registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.holiday.index')]);
        }

        return redirect()->route('admin.holiday.index')
            ->with('success', 'Feriado registrado correctamente.');
    }

    public function edit(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, string $id)
    {
        $holiday = Holiday::findOrFail($id);

        $request->validate($this->rules($id), $this->messages());

        $holiday->update($this->holidayData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Feriado actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.holiday.index')]);
        }

        return redirect()->route('admin.holiday.index')
            ->with('success', 'Feriado actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('admin.holiday.index')
            ->with('success', 'Feriado eliminado correctamente.');
    }

    private function rules(?string $id = null): array
    {
        return [
            'date' => [
                'required',
                'date',
                Rule::unique('holidays', 'date')->ignore($id),
            ],
            'description' => 'required|string|max:255',
            'state' => 'required|in:activo,inactivo',
        ];
    }

    private function messages(): array
    {
        return [
            'date.required' => 'La fecha del feriado es obligatoria.',
            'date.date' => 'La fecha ingresada no es válida.',
            'date.unique' => 'Ya existe un feriado registrado en esta fecha.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no debe superar los 255 caracteres.',
            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado seleccionado no es válido.',
        ];
    }

    private function holidayData(Request $request): array
    {
        return $request->only([
            'date',
            'description',
            'state',
        ]);
    }
}
