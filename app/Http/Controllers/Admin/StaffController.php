<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffType;
use App\Support\PublicImageStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Staff::query()->with('staffType');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('staffType', function ($t) use ($search) {
                      $t->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $staffMembers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.staff.index', compact('staffMembers', 'search'));
    }

    public function create()
    {
        $types = StaffType::orderBy('name')->get();
        return view('admin.staff.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $data = $this->staffData($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = PublicImageStorage::store($request->file('photo'), 'staff_photos');
        }

        Staff::create($data);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Personal registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.staff.index')]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Personal registrado correctamente.');
    }

    public function edit(string $id)
    {
        $member = Staff::findOrFail($id);
        $types = StaffType::orderBy('name')->get();
        $imageUrl = $member->photo_url;

        return view('admin.staff.edit', compact('member', 'types', 'imageUrl'));
    }

    public function update(Request $request, string $id)
    {
        $member = Staff::findOrFail($id);

        $request->validate($this->rules($member->id), $this->messages());

        $data = $this->staffData($request);

        if ($request->hasFile('photo')) {
            PublicImageStorage::delete($member->photo);
            $data['photo'] = PublicImageStorage::store($request->file('photo'), 'staff_photos');
        }

        $member->update($data);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Personal actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.staff.index')]);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Personal actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $member = Staff::findOrFail($id);

        PublicImageStorage::delete($member->photo);
        $member->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Personal eliminado correctamente.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'dni' => [
                'required',
                'string',
                'max:15',
                Rule::unique('staff', 'dni')->ignore($ignoreId),
            ],
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:150',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('staff', 'email')->ignore($ignoreId),
            ],
            'birthdate' => 'nullable|date|before:today',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'staff_type_id' => 'required|exists:staff_types,id',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ];
    }

    private function messages(): array
    {
        return [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Ya existe personal registrado con este DNI.',
            'name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'Ya existe personal registrado con este correo.',
            'birthdate.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'staff_type_id.required' => 'Debe seleccionar un tipo de personal.',
            'status.required' => 'Debe seleccionar un estado.',
            'photo.image' => 'El archivo debe ser una imagen válida.',
        ];
    }

    private function staffData(Request $request): array
    {
        return $request->only([
            'dni',
            'name',
            'last_name',
            'email',
            'birthdate',
            'phone',
            'address',
            'staff_type_id',
            'status',
        ]);
    }
}
