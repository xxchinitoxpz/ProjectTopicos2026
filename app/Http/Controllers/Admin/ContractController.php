<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('contract_type');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;
        
        $query = Contract::query()->with('staff');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('staff', function ($s) use ($search) {
                    $s->where('name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%')
                      ->orWhere('dni', 'like', '%' . $search . '%');
                });
            });
        }

        if ($typeFilter) {
            $query->where('contract_type', $typeFilter);
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.contracts.index', compact('contracts', 'search', 'typeFilter', 'perPage'));
    }

    public function create()
    {
        $staffMembers = Staff::where('status', 'active')->orderBy('name')->get();
        return view('admin.contracts.create', compact('staffMembers'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $data = $this->contractData($request);

        if ($data['contract_type'] !== 'temporal') {
            $data['date_end'] = null;
        }

        Contract::create($data);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Contrato registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.contract.index')]);
        }

        return redirect()->route('admin.contract.index')
            ->with('success', 'Contrato registrado correctamente.');
    }

    public function edit(string $id)
    {
        $contract = Contract::findOrFail($id);
        $staffMembers = Staff::where('status', 'active')
            ->orWhere('id', $contract->staff_id)
            ->orderBy('name')
            ->get();

        return view('admin.contracts.edit', compact('contract', 'staffMembers'));
    }

    public function update(Request $request, string $id)
    {
        $contract = Contract::findOrFail($id);

        $request->validate($this->rules(), $this->messages());

        $data = $this->contractData($request);

        if ($data['contract_type'] !== 'temporal') {
            $data['date_end'] = null;
        }

        $contract->update($data);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Contrato actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.contract.index')]);
        }

        return redirect()->route('admin.contract.index')
            ->with('success', 'Contrato actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        return redirect()->route('admin.contract.index')
            ->with('success', 'Contrato eliminado correctamente.');
    }

    private function rules(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'contract_type' => 'required|in:permanente,nombrado,temporal',
            'date_start' => 'required|date',
            'date_end' => 'required_if:contract_type,temporal|nullable|date|after_or_equal:date_start',
            'salary' => 'required|numeric|min:0',
            'probation' => 'nullable|integer|min:0',
            'state' => 'required|in:active,inactive,expired',
        ];
    }

    private function messages(): array
    {
        return [
            'staff_id.required' => 'Debe seleccionar un miembro del personal.',
            'staff_id.exists' => 'El personal seleccionado no es válido.',
            'contract_type.required' => 'El tipo de contrato es obligatorio.',
            'contract_type.in' => 'El tipo de contrato seleccionado no es válido.',
            'date_start.required' => 'La fecha de inicio es obligatoria.',
            'date_start.date' => 'La fecha de inicio debe ser una fecha válida.',
            'date_end.required_if' => 'La fecha de finalización es obligatoria para contratos temporales.',
            'date_end.date' => 'La fecha de finalización debe ser una fecha válida.',
            'date_end.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.',
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un valor numérico.',
            'salary.min' => 'El salario no puede ser menor a 0.',
            'probation.integer' => 'El período de prueba debe ser un número entero de meses.',
            'probation.min' => 'El período de prueba no puede ser menor a 0.',
            'state.required' => 'El estado del contrato es obligatorio.',
        ];
    }

    private function contractData(Request $request): array
    {
        return $request->only([
            'staff_id',
            'contract_type',
            'date_start',
            'date_end',
            'salary',
            'probation',
            'state',
        ]);
    }
}
