<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Vehicle;
use App\Models\VehicleColor;
use App\Models\VehicleImage;
use App\Models\VehicleType;
use App\Support\PublicImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Vehicle::query()->with(['brand', 'brandModel', 'vehicleType', 'vehicleColor', 'vehicleImages']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('plate', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('brandModel', fn ($m) => $m->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('vehicleType', fn ($t) => $t->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('vehicleColor', fn ($c) => $c->where('name', 'like', '%' . $search . '%'));
            });
        }

        $vehicles = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.vehicles.index', compact('vehicles', 'search'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $types = VehicleType::orderBy('name')->get();
        $colors = VehicleColor::orderBy('name')->get();

        return view('admin.vehicles.create', compact('brands', 'types', 'colors'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $vehicle = Vehicle::create($this->vehicleData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Vehículo registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vehicle.index')]);
        }

        return redirect()->route('admin.vehicle.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function edit(string $id)
    {
        $vehicle = Vehicle::with('vehicleImages')->findOrFail($id);
        $brands = Brand::orderBy('name')->get();
        $types = VehicleType::orderBy('name')->get();
        $colors = VehicleColor::orderBy('name')->get();
        $brandModels = BrandModel::where('brand_id', $vehicle->brand_id)->orderBy('name')->get();
        $imageUrl = $this->profileImageUrl($vehicle);

        return view('admin.vehicles.edit', compact('vehicle', 'brands', 'types', 'colors', 'brandModels', 'imageUrl'));
    }

    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate($this->rules($vehicle->id), $this->messages());

        $vehicle->update($this->vehicleData($request));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Vehículo actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.vehicle.index')]);
        }

        return redirect()->route('admin.vehicle.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $vehicle = Vehicle::with('vehicleImages')->findOrFail($id);

        if ($vehicle->vehicleRoutes()->exists()) {
            return redirect()->route('admin.vehicle.index')
                ->with('error', 'No se puede eliminar el vehículo porque tiene rutas programadas.');
        }

        foreach ($vehicle->vehicleImages as $image) {
            PublicImageStorage::delete($image->image);
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicle.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    public function brandModelsByBrand(string $brand)
    {
        $models = BrandModel::where('brand_id', $brand)->orderBy('name')->get(['id', 'name']);
        return response()->json($models);
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehicles', 'plate')->ignore($ignoreId),
            ],
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'occupant_capacity' => 'nullable|integer|min:1',
            'load_capacity' => 'nullable|numeric|min:0',
            'combustible_capacity' => 'nullable|numeric|min:0',
            'compaction_capacity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:brand_models,id',
            'type_id' => 'required|exists:vehicle_types,id',
            'color_id' => 'required|exists:vehicle_colors,id',
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'plate.required' => 'La placa es obligatoria.',
            'plate.unique' => 'Ya existe un vehículo con esa placa.',
            'brand_id.required' => 'Debe seleccionar una marca.',
            'model_id.required' => 'Debe seleccionar un modelo.',
            'type_id.required' => 'Debe seleccionar un tipo de vehículo.',
            'color_id.required' => 'Debe seleccionar un color.',
            'status.required' => 'Debe seleccionar un estado.',
        ];
    }

    private function vehicleData(Request $request): array
    {
        return $request->only([
            'name',
            'code',
            'plate',
            'year',
            'occupant_capacity',
            'load_capacity',
            'combustible_capacity',
            'compaction_capacity',
            'description',
            'status',
            'brand_id',
            'model_id',
            'type_id',
            'color_id',
        ]);
    }

    private function storeProfileImage(Vehicle $vehicle, UploadedFile $file): void
    {
        $path = PublicImageStorage::store($file, 'vehicle_images');

        VehicleImage::create([
            'image' => $path,
            'profile' => true,
            'vehicle_id' => $vehicle->id,
        ]);
    }

    private function profileImageUrl(Vehicle $vehicle): string
    {
        $profile = $vehicle->vehicleImages->firstWhere('profile', true)
            ?? $vehicle->vehicleImages->first();

        return $profile ? $profile->url : PublicImageStorage::placeholder();
    }

    private function deleteProfileImage(Vehicle $vehicle): void
    {
        $profile = $vehicle->vehicleImages()->where('profile', true)->first();

        if ($profile) {
            PublicImageStorage::delete($profile->image);
            $profile->delete();
        }
    }
}
