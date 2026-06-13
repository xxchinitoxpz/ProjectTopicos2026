<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use App\Models\Zone;
use App\Models\ZoneCoord;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = in_array($request->input('per_page', 10), [10, 25, 50, 100])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Zone::query()->with(['district.province.department', 'zoneCoords']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('district', fn ($d) => $d->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('district.province', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('district.province.department', fn ($dep) => $dep->where('name', 'like', "%{$search}%"));
            });
        }

        $zones = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('admin.zones.index', compact('zones', 'search', 'perPage'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $refZones    = Zone::with('zoneCoords')->whereHas('zoneCoords')->get();

        return view('admin.zones.create', compact('departments', 'refZones'));
    }

    public function store(Request $request)
    {
        $this->validateZone($request);

        $zone = Zone::create([
            'name'         => $request->name,
            'district_id'  => $request->district_id,
            'description'  => $request->description,
            'avg_waste_kg' => $request->avg_waste_kg,
            'status'       => $request->status,
            'area'         => $request->area ?? null,
            'sector_id'    => null,
        ]);

        $this->saveCoords($zone, $request->input('coords', []));

        return redirect()->route('admin.zone.index')
            ->with('success', 'Zona registrada correctamente.');
    }

    public function edit(Zone $zone)
    {
        $zone->load(['district.province.department', 'zoneCoords']);
        $departments = Department::orderBy('name')->get();
        $provinces   = Province::where('department_id', $zone->district->province->department_id)->orderBy('name')->get();
        $districts   = District::where('province_id', $zone->district->province_id)->orderBy('name')->get();
        $refZones    = Zone::with('zoneCoords')->whereHas('zoneCoords')->where('id', '!=', $zone->id)->get();

        return view('admin.zones.edit', compact('zone', 'departments', 'provinces', 'districts', 'refZones'));
    }

    public function update(Request $request, Zone $zone)
    {
        $this->validateZone($request);

        $zone->update([
            'name'         => $request->name,
            'district_id'  => $request->district_id,
            'description'  => $request->description,
            'avg_waste_kg' => $request->avg_waste_kg,
            'status'       => $request->status,
            'area'         => $request->area ?? $zone->area,
        ]);

        $zone->zoneCoords()->delete();
        $this->saveCoords($zone, $request->input('coords', []));

        return redirect()->route('admin.zone.index')
            ->with('success', 'Zona actualizada correctamente.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();

        return redirect()->route('admin.zone.index')
            ->with('success', 'Zona eliminada correctamente.');
    }

    public function showMap(Zone $zone)
    {
        $zone->load(['district.province.department', 'zoneCoords']);

        return view('admin.zones.map-modal', compact('zone'));
    }

    public function allZonesMap()
    {
        $zones = Zone::with(['district.province.department', 'zoneCoords'])
            ->whereHas('zoneCoords')
            ->get();

        $zonesJson = $zones->map(fn ($z) => [
            'id'       => $z->id,
            'name'     => $z->name,
            'district' => $z->district?->name,
            'status'   => $z->status,
            'waste'    => $z->avg_waste_kg,
            'coords'   => $z->zoneCoords->map(fn ($c) => [(float) $c->latitude, (float) $c->longitude])->toArray(),
        ])->values();

        return view('admin.zones.all-map', compact('zones', 'zonesJson'));
    }

    public function provincesByDepartment(Department $department)
    {
        return response()->json(
            Province::where('department_id', $department->id)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function districtsByProvince(Province $province)
    {
        return response()->json(
            District::where('province_id', $province->id)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function zonesGeoJson()
    {
        $zones = Zone::with('zoneCoords')->whereHas('zoneCoords')->get();

        $features = $zones->map(function ($zone) {
            $coords = $zone->zoneCoords->map(fn ($c) => [(float) $c->longitude, (float) $c->latitude])->toArray();
            if (count($coords) >= 3) {
                $coords[] = $coords[0];
            }
            return [
                'type'       => 'Feature',
                'properties' => ['id' => $zone->id, 'name' => $zone->name],
                'geometry'   => ['type' => 'Polygon', 'coordinates' => [$coords]],
            ];
        })->values();

        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }

    private function validateZone(Request $request): void
    {
        $request->validate([
            'name'                => 'required|string|max:150',
            'district_id'         => 'required|exists:districts,id',
            'description'         => 'nullable|string',
            'avg_waste_kg'        => 'nullable|numeric|min:0',
            'status'              => 'required|in:active,inactive',
            'area'                => 'nullable|numeric|min:0',
            'coords'              => 'required|array|min:3',
            'coords.*.latitude'   => 'required|numeric|between:-90,90',
            'coords.*.longitude'  => 'required|numeric|between:-180,180',
        ], [
            'name.required'       => 'El nombre de la zona es obligatorio.',
            'district_id.required'=> 'Debe seleccionar un distrito.',
            'coords.required'     => 'Debe definir al menos 3 coordenadas.',
            'coords.min'          => 'Se requieren mínimo 3 coordenadas para definir el perímetro.',
        ]);
    }

    private function saveCoords(Zone $zone, array $coords): void
    {
        foreach ($coords as $coord) {
            ZoneCoord::create([
                'zone_id'   => $zone->id,
                'latitude'  => $coord['latitude'],
                'longitude' => $coord['longitude'],
            ]);
        }
    }
}
