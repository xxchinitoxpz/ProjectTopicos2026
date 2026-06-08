<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Support\PublicImageStorage;
use Illuminate\Http\Request;

class VehicleImageController extends Controller
{
    public function index(Vehicle $vehicle)
    {
        $images = $vehicle->vehicleImages()->orderByDesc('profile')->orderBy('created_at')->get();
        return view('admin.vehicles.images', compact('vehicle', 'images'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ], [
            'images.required' => 'Debe seleccionar al menos una imagen.',
            'images.*.image' => 'El archivo debe ser una imagen válida.',
            'images.*.mimes' => 'Formatos permitidos: JPEG, JPG, PNG, GIF, WEBP.',
            'images.*.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        $hasProfile = $vehicle->vehicleImages()->where('profile', true)->exists();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = PublicImageStorage::store($file, 'vehicle_images');

                VehicleImage::create([
                    'image' => $path,
                    'profile' => !$hasProfile && $index === 0,
                    'vehicle_id' => $vehicle->id,
                ]);

                if (!$hasProfile && $index === 0) {
                    $hasProfile = true;
                }
            }
        }

        return redirect()->route('admin.vehicle.images.index', $vehicle->id)
            ->with('success', 'Imágenes subidas correctamente.');
    }

    public function destroy(string $id)
    {
        $image = VehicleImage::findOrFail($id);
        $vehicleId = $image->vehicle_id;
        $wasProfile = $image->profile;

        PublicImageStorage::delete($image->image);
        $image->delete();

        if ($wasProfile) {
            $anotherImage = VehicleImage::where('vehicle_id', $vehicleId)->first();
            if ($anotherImage) {
                $anotherImage->update(['profile' => true]);
            }
        }

        return redirect()->route('admin.vehicle.images.index', $vehicleId)
            ->with('success', 'Imagen eliminada correctamente.');
    }

    public function setProfile(string $id)
    {
        $image = VehicleImage::findOrFail($id);
        $vehicleId = $image->vehicle_id;

        VehicleImage::where('vehicle_id', $vehicleId)->update(['profile' => false]);
        $image->update(['profile' => true]);

        return redirect()->route('admin.vehicle.images.index', $vehicleId)
            ->with('success', 'Imagen de perfil seleccionada.');
    }
}
