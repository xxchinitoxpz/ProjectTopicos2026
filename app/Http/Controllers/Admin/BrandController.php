<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Support\PublicImageStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Brand::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        $brands = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.brands.index', compact('brands', 'search'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:brands,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe una marca con ese nombre.',
            'logo.image' => 'El logo debe ser una imagen válida.',
            'logo.mimes' => 'El logo debe ser JPG, PNG, GIF o WEBP.',
        ]);

        Brand::create([
            'name' => $request->name,
            'description' => $request->description,
            'logo' => $request->hasFile('logo')
                ? PublicImageStorage::store($request->file('logo'), 'brand_logos')
                : null,
        ]);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Marca registrada correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.brand.index')]);
        }

        return redirect()->route('admin.brand.index')
            ->with('success', 'Marca registrada correctamente.');
    }

    public function edit(string $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('brands', 'name')->ignore($brand->id),
            ],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe una marca con ese nombre.',
            'logo.image' => 'El logo debe ser una imagen válida.',
            'logo.mimes' => 'El logo debe ser JPG, PNG, GIF o WEBP.',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('logo')) {
            PublicImageStorage::delete($brand->logo);
            $data['logo'] = PublicImageStorage::store($request->file('logo'), 'brand_logos');
        }

        $brand->update($data);

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Marca actualizada correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.brand.index')]);
        }

        return redirect()->route('admin.brand.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->brandModels()->exists() || $brand->vehicles()->exists()) {
            return redirect()->route('admin.brand.index')
                ->with('error', 'No se puede eliminar la marca porque tiene modelos o vehículos asociados.');
        }

        PublicImageStorage::delete($brand->logo);
        $brand->delete();

        return redirect()->route('admin.brand.index')
            ->with('success', 'Marca eliminada correctamente.');
    }
}
