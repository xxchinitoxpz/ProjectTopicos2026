<x-app-layout>
    <x-slot:title>
        Listado de Vehículos
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Vehículos</h3>
                <p class="text-xs text-gray-400">Gestiona la flota de vehículos ecológicos y de recojo.</p>
            </div>
            <div>
                <a href="{{ route('admin.vehicle.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Vehículo
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search Filter -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <form action="{{ route('admin.vehicle.index') }}" method="GET" class="flex max-w-md gap-2">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por placa, nombre, marca, color..." class="w-full text-sm" />
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('admin.vehicle.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6" width="90">Foto</th>
                            <th class="py-3.5 px-6">Vehículo</th>
                            <th class="py-3.5 px-6">Placa / Código</th>
                            <th class="py-3.5 px-6">Marca y Modelo</th>
                            <th class="py-3.5 px-6">Tipo / Color</th>
                            <th class="py-3.5 px-6">Capacidades</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center" width="160">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($vehicles as $vehicle)
                            @php
                                $profileImage = $vehicle->vehicleImages->firstWhere('profile', true) 
                                    ?? $vehicle->vehicleImages->first();
                                $imageUrl = $profileImage ? $profileImage->url : 'https://ui-avatars.com/api/?name=V&color=10B981&background=f0fdf4&size=128';
                            @endphp
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <img src="{{ $imageUrl }}" class="w-12 h-12 object-cover rounded-xl border border-gray-200 shadow-sm" alt="{{ $vehicle->name }}">
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $vehicle->name }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">Año: {{ $vehicle->year ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-usat-blue">{{ $vehicle->plate }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $vehicle->code ?? 'Sin código' }}</div>
                                </td>
                                <td class="py-3.5 px-6 text-xs font-semibold">
                                    <div class="text-gray-800">{{ $vehicle->brand?->name }}</div>
                                    <div class="text-gray-400 text-[10px] mt-0.5">{{ $vehicle->brandModel?->name }}</div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs font-semibold text-gray-800">{{ $vehicle->vehicleType?->name }}</div>
                                    <div class="flex items-center space-x-1.5 mt-1">
                                        <span class="w-2.5 h-2.5 rounded-full border border-gray-200 shadow-xs" style="background-color: {{ $vehicle->vehicleColor?->code }}"></span>
                                        <span class="text-[10px] text-gray-500 font-medium">{{ $vehicle->vehicleColor?->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs text-gray-700 space-y-0.5">
                                        <div>Carga: <strong class="text-gray-900">{{ $vehicle->load_capacity ?? '0' }} TN</strong></div>
                                        <div>Compact.: <strong class="text-gray-900">{{ $vehicle->compaction_capacity ?? '0' }} TN</strong></div>
                                        <div>Combust.: <strong class="text-gray-900">{{ $vehicle->combustible_capacity ?? '0' }} Gl.</strong></div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($vehicle->status === 'active')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">Activo</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-full border border-gray-200">Inactivo</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.vehicle.images.index', $vehicle->id) }}" data-turbo-frame="modal" class="p-2 bg-blue-50 text-usat-blue hover:bg-blue-100 rounded-lg transition duration-150" title="Gestionar Imágenes">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.vehicle.edit', $vehicle->id) }}" data-turbo-frame="modal" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.vehicle.destroy', $vehicle->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este vehículo?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition duration-150" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">
                                    No se encontraron vehículos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vehicles->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
