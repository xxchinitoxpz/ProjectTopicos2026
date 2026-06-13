<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use App\Models\Staff;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $stateFilter = $request->input('state');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Vacation::query()->with('staff');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('staff', function ($s) use ($search) {
                    $s->where('name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%')
                      ->orWhere('dni', 'like', '%' . $search . '%');
                });
            });
        }

        if ($stateFilter) {
            $query->where('state', $stateFilter);
        }

        $vacations = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.vacations.index', compact('vacations', 'search', 'stateFilter', 'perPage'));
    }

    public function create()
    {
        $staffMembers = Staff::where('status', 'active')->get()->filter(function ($staff) {
            return $staff->hasActiveEligibleContract();
        });

        return view('admin.vacations.create', compact('staffMembers'));
    }

    public function store(Request $request)
    {
        return $request;
        $request->validate($this->rules(), $this->messages());

        $staff = Staff::findOrFail($request->staff_id);

        if (!$staff->hasActiveEligibleContract()) {
            return back()->withInput()->withErrors(['staff_id' => 'Solo el personal nombrado y de contrato permanente puede solicitar vacaciones.']);
        }

        if ($staff->hasVacationOverlap($request->date_start, $request->date_end)) {
            return back()->withInput()->withErrors(['date_start' => 'Las fechas solicitadas coinciden con otra solicitud aprobada o pendiente.']);
        }

        if ($staff->vacation_days < $request->days_requested) {
            return back()->withInput()->withErrors(['days_requested' => 'El personal seleccionado solo cuenta con ' . $staff->vacation_days . ' días de vacaciones disponibles.']);
        }

        $data = $this->vacationData($request);
        $data['date_request'] = now()->format('Y-m-d');
        $data['state'] = 'pending';

        Vacation::create($data);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Solicitud de vacaciones registrada.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vacation.index')]);
        }

        return redirect()->route('admin.vacation.index')
            ->with('success', 'Solicitud de vacaciones registrada.');
    }

    public function edit(string $id)
    {
        $vacation = Vacation::findOrFail($id);

        if ($vacation->state !== 'pending') {
            return redirect()->route('admin.vacation.index')
                ->with('error', 'Solo las solicitudes pendientes pueden ser editadas.');
        }

        $staffMembers = Staff::where('status', 'active')->get()->filter(function ($staff) {
            return $staff->hasActiveEligibleContract();
        });

        if (!$staffMembers->contains('id', $vacation->staff_id)) {
            $staffMembers->push($vacation->staff);
        }

        return view('admin.vacations.edit', compact('vacation', 'staffMembers'));
    }

    public function update(Request $request, string $id)
    {
        $vacation = Vacation::findOrFail($id);

        if ($vacation->state !== 'pending') {
            return redirect()->route('admin.vacation.index')
                ->with('error', 'Solo las solicitudes pendientes pueden ser editadas.');
        }

        $request->validate($this->rules(), $this->messages());

        $staff = Staff::findOrFail($request->staff_id);

        if (!$staff->hasActiveEligibleContract()) {
            return back()->withInput()->withErrors(['staff_id' => 'Solo el personal nombrado y de contrato permanente puede solicitar vacaciones.']);
        }

        if ($staff->hasVacationOverlap($request->date_start, $request->date_end, $vacation->id)) {
            return back()->withInput()->withErrors(['date_start' => 'Las fechas solicitadas coinciden con otra solicitud aprobada o pendiente.']);
        }

        if ($staff->vacation_days < $request->days_requested) {
            return back()->withInput()->withErrors(['days_requested' => 'El personal seleccionado solo cuenta con ' . $staff->vacation_days . ' días de vacaciones disponibles.']);
        }

        $vacation->update($this->vacationData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Solicitud de vacaciones actualizada.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vacation.index')]);
        }

        return redirect()->route('admin.vacation.index')
            ->with('success', 'Solicitud de vacaciones actualizada.');
    }

    public function destroy(string $id)
    {
        $vacation = Vacation::findOrFail($id);

        if ($vacation->state !== 'pending') {
            return redirect()->route('admin.vacation.index')
                ->with('error', 'Solo las solicitudes pendientes pueden ser eliminadas.');
        }

        $vacation->delete();

        return redirect()->route('admin.vacation.index')
            ->with('success', 'Solicitud de vacaciones eliminada.');
    }

    public function approve(string $id)
    {
        $vacation = Vacation::findOrFail($id);

        if ($vacation->state !== 'pending') {
            return redirect()->route('admin.vacation.index')
                ->with('error', 'Solo las solicitudes pendientes pueden ser aprobadas.');
        }

        $staff = $vacation->staff;

        if ($staff->vacation_days < $vacation->days_requested) {
            return redirect()->route('admin.vacation.index')
                ->with('error', 'El personal no cuenta con suficientes días de vacaciones disponibles (' . $staff->vacation_days . ' disponibles).');
        }

        $staff->vacation_days -= $vacation->days_requested;
        $staff->save();

        $vacation->update(['state' => 'approved']);

        return redirect()->route('admin.vacation.index')
            ->with('success', 'Solicitud de vacaciones aprobada correctamente.');
    }

    public function reject(string $id)
    {
        $vacation = Vacation::findOrFail($id);

        if ($vacation->state !== 'pending') {
            return redirect()->route('admin.vacation.index')
                ->with('error', 'Solo las solicitudes pendientes pueden ser rechazadas.');
        }

        $vacation->update(['state' => 'rejected']);

        return redirect()->route('admin.vacation.index')
            ->with('success', 'Solicitud de vacaciones rechazada.');
    }

    private function rules(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'days_requested' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'staff_id.required' => 'Debe seleccionar un miembro del personal.',
            'staff_id.exists' => 'El personal seleccionado no es válido.',
            'date_start.required' => 'La fecha de inicio es obligatoria.',
            'date_start.date' => 'La fecha de inicio debe ser una fecha válida.',
            'date_end.required' => 'La fecha de finalización es obligatoria.',
            'date_end.date' => 'La fecha de finalización debe ser una fecha válida.',
            'date_end.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.',
            'days_requested.required' => 'La cantidad de días solicitados es obligatoria.',
            'days_requested.integer' => 'La cantidad de días debe ser un número entero.',
            'days_requested.min' => 'Debe solicitar al menos 1 día de vacaciones.',
        ];
    }

    private function vacationData(Request $request): array
    {
        return $request->only([
            'staff_id',
            'date_start',
            'date_end',
            'days_requested',
            'notes',
        ]);
    }
}
