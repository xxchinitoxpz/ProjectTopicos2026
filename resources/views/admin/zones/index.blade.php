<x-app-layout>
    <x-slot:title>Zonas</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Gestión de Zonas</h3>
                <p class="text-xs text-gray-400">Define y administra las zonas de recolección con sus perímetros geográficos.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.zone.all-map') }}"
                   class="inline-flex items-center px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-usat-blue text-sm font-semibold rounded-xl transition border border-blue-100">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Ver Mapa de Zonas
                </a>
                <a href="{{ route('admin.zone.create') }}"
                   class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Zona
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Filters -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.zone.index') }}" method="GET" class="flex flex-1 gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, distrito, provincia..." class="flex-1 text-sm"/>
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">Buscar</button>
                    @if($search)
                        <a href="{{ route('admin.zone.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">Limpiar</a>
                    @endif
                </form>
                <form action="{{ route('admin.zone.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <span>Mostrar</span>
                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Nombre</th>
                            <th class="py-3.5 px-6">Distrito</th>
                            <th class="py-3.5 px-6">Provincia</th>
                            <th class="py-3.5 px-6">Departamento</th>
                            <th class="py-3.5 px-6">Descripción</th>
                            <th class="py-3.5 px-6 text-center">Coordenadas</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center">Creado</th>
                            <th class="py-3.5 px-6 text-center" width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($zones as $zone)
                            @php
                                $province   = $zone->district?->province;
                                $department = $province?->department;
                                $coordCount = $zone->zoneCoords->count();
                            @endphp
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6 font-bold text-gray-900">{{ $zone->name }}</td>
                                <td class="py-3.5 px-6 text-xs text-gray-700">{{ $zone->district?->name ?? '—' }}</td>
                                <td class="py-3.5 px-6 text-xs text-gray-500">{{ $province?->name ?? '—' }}</td>
                                <td class="py-3.5 px-6 text-xs text-gray-500">{{ $department?->name ?? '—' }}</td>
                                <td class="py-3.5 px-6 text-xs text-gray-500 max-w-[180px] truncate">{{ $zone->description ?? '—' }}</td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($coordCount > 0)
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                                            {{ $coordCount }} PUNTO{{ $coordCount != 1 ? 'S' : '' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Sin coords</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($zone->status === 'active')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">ACTIVO</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-full border border-gray-200">INACTIVO</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center text-xs text-gray-400">{{ $zone->created_at->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        {{-- Ver mapa --}}
                                        @if($coordCount > 0)
                                        <a href="{{ route('admin.zone.show-map', $zone->id) }}" data-turbo-frame="modal"
                                           class="p-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition" title="Ver Mapa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                        </a>
                                        @endif
                                        {{-- Editar --}}
                                        <a href="{{ route('admin.zone.edit', $zone->id) }}"
                                           class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                        {{-- Eliminar --}}
                                        <form action="{{ route('admin.zone.destroy', $zone->id) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar la zona «{{ $zone->name }}»?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-gray-400">
                                    No se encontraron zonas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($zones->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $zones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
