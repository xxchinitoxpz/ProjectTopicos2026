<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assistance;
use App\Models\Staff;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssistanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $stateFilter = $request->input('state');
        $dateFilter = $request->input('date');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Assistance::query()->with(['staff', 'shift']);

        if ($search) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('dni', 'like', '%' . $search . '%');
            });
        }

        if ($stateFilter) {
            $query->where('state', $stateFilter);
        }

        if ($dateFilter) {
            $query->whereDate('date_time', $dateFilter);
        }

        $assistances = $query->orderBy('date_time', 'desc')
                             ->paginate($perPage)
                             ->withQueryString();

        return view('admin.assistances.index', compact('assistances', 'search', 'stateFilter', 'dateFilter', 'perPage'));
    }

    public function create()
    {
        $staffMembers = Staff::where('status', 'active')->orderBy('name')->get();
        $shifts = Shift::all();
        return view('admin.assistances.create', compact('staffMembers', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('time'));
        
        $determination = Assistance::determineShiftAndType($request->input('time'));

        Assistance::create([
            'staff_id' => $request->input('staff_id'),
            'date_time' => $dateTime,
            'type' => $determination['type'],
            'state' => $request->input('state'),
            'shift_id' => $determination['shift_id'],
            'notes' => $request->input('notes'),
        ]);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Asistencia registrada correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.assistance.index')]);
        }

        return redirect()->route('admin.assistance.index')
            ->with('success', 'Asistencia registrada correctamente.');
    }

    public function edit(string $id)
    {
        $assistance = Assistance::findOrFail($id);
        
        $staffMembers = Staff::where('status', 'active')
            ->orWhere('id', $assistance->staff_id)
            ->orderBy('name')
            ->get();
            
        $shifts = Shift::all();
        
        $date = $assistance->date_time->format('Y-m-d');
        $time = $assistance->date_time->format('H:i');

        return view('admin.assistances.edit', compact('assistance', 'staffMembers', 'shifts', 'date', 'time'));
    }

    public function update(Request $request, string $id)
    {
        $assistance = Assistance::findOrFail($id);

        $request->validate($this->rules(), $this->messages());

        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('time'));
        
        $determination = Assistance::determineShiftAndType($request->input('time'));

        $assistance->update([
            'staff_id' => $request->input('staff_id'),
            'date_time' => $dateTime,
            'type' => $determination['type'],
            'state' => $request->input('state'),
            'shift_id' => $determination['shift_id'],
            'notes' => $request->input('notes'),
        ]);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Asistencia actualizada correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.assistance.index')]);
        }

        return redirect()->route('admin.assistance.index')
            ->with('success', 'Asistencia actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $assistance = Assistance::findOrFail($id);
        $assistance->delete();

        return redirect()->route('admin.assistance.index')
            ->with('success', 'Asistencia eliminada correctamente.');
    }

    private function rules(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'state' => 'required|in:presente,ausente',
            'notes' => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'staff_id.required' => 'Debe seleccionar un miembro del personal.',
            'staff_id.exists' => 'El miembro del personal seleccionado no es válido.',
            'date.required' => 'La fecha de la asistencia es obligatoria.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'time.required' => 'La hora de la asistencia es obligatoria.',
            'time.date_format' => 'La hora debe tener el formato HH:MM.',
            'state.required' => 'El estado de la asistencia es obligatorio.',
            'state.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
