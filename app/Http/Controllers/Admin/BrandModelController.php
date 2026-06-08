<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandModel;
use Illuminate\Http\Request;

class BrandModelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = BrandModel::query()->with('brand');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('brand', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        $models = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.brand-models.index', compact('models', 'search'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return view('admin.brand-models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'brand_id.required' => 'Debe seleccionar una marca.',
            'brand_id.exists' => 'La marca seleccionada no es válida.',
        ]);

        BrandModel::create($request->only(['name', 'code', 'description', 'brand_id']));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Modelo registrado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.brand-model.index')]);
        }

        return redirect()->route('admin.brand-model.index')
            ->with('success', 'Modelo registrado correctamente.');
    }

    public function edit(string $id)
    {
        $brandModel = BrandModel::findOrFail($id);
        $brands = Brand::orderBy('name')->get();
        return view('admin.brand-models.edit', compact('brandModel', 'brands'));
    }

    public function update(Request $request, string $id)
    {
        $brandModel = BrandModel::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'brand_id.required' => 'Debe seleccionar una marca.',
            'brand_id.exists' => 'La marca seleccionada no es válida.',
        ]);

        $brandModel->update($request->only(['name', 'code', 'description', 'brand_id']));

        if ($request->wantsTurboStream()) {
            session()->flash('success', 'Modelo actualizado correctamente.');
            return response()->turboStream()
                ->action('redirect')
                ->attributes(['url' => route('admin.brand-model.index')]);
        }

        return redirect()->route('admin.brand-model.index')
            ->with('success', 'Modelo actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $brandModel = BrandModel::findOrFail($id);

        if ($brandModel->vehicles()->exists()) {
            return redirect()->route('admin.brand-model.index')
                ->with('error', 'No se puede eliminar el modelo porque tiene vehículos asociados.');
        }

        $brandModel->delete();

        return redirect()->route('admin.brand-model.index')
            ->with('success', 'Modelo eliminado correctamente.');
    }
}
