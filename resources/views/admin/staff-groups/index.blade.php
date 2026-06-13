<x-app-layout>
    <x-slot:title>Grupos de Personal</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Grupos de Personal</h3>
                <p class="text-xs text-gray-400">Administra los equipos de trabajo (conductor, ayudantes, vehículo, zona y horario).</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.staff-group.create') }}"
                   class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Grupo
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Filters -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.staff-group.index') }}" method="GET" class="flex flex-1 gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por grupo, conductor, zona..." class="flex-1 text-sm"/>
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">Buscar</button>
                    @if($search)
                        <a href="{{ route('admin.staff-group.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">Limpiar</a>
                    @endif
                </form>
                <form action="{{ route('admin.staff-group.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500">
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
                            <th class="py-3.5 px-6">Grupo</th>
                            <th class="py-3.5 px-6">Conductor</th>
                            <th class="py-3.5 px-6">Ayudantes</th>
                            <th class="py-3.5 px-6">Vehículo</th>
                            <th class="py-3.5 px-6">Turno</th>
                            <th class="py-3.5 px-6">Zona</th>
                            <th class="py-3.5 px-6 text-center">Días</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($groups as $group)
                            @php
                                $helpersCount = $group->helpers->count();
                            @endphp
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6 font-bold text-gray-900">
                                    {{ $group->name }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center gap-2">
                                        @if($group->driver->photo)
                                            <img src="{{ $group->driver->photo_url }}" class="w-8 h-8 rounded-full object-cover border border-gray-100">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                                {{ substr($group->driver->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-800 text-xs">{{ $group->driver->name }} {{ $group->driver->last_name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $group->driver->dni }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    @if($helpersCount > 0)
                                        <div class="flex flex-col gap-1 max-w-[180px]">
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-700 rounded-full border border-blue-100 w-max">
                                                {{ $helpersCount }} AYUDANTE{{ $helpersCount != 1 ? 'S' : '' }}
                                            </span>
                                            <div class="text-[10px] text-gray-500 truncate" title="{{ $group->helpers->pluck('name')->implode(', ') }}">
                                                {{ $group->helpers->pluck('name')->implode(', ') }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-450 text-xs italic">Sin ayudantes</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs">
                                        <span class="font-bold text-gray-800">{{ $group->vehicle->plate }}</span>
                                        <div class="text-[10px] text-gray-400 truncate">{{ $group->vehicle->name }}</div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs">
                                        <span class="font-semibold text-gray-800">{{ $group->shift->name }}</span>
                                        <div class="text-[10px] text-gray-400">{{ substr($group->shift->check_in, 0, 5) }} - {{ substr($group->shift->check_out, 0, 5) }}</div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs">
                                        <span class="font-semibold text-gray-800">{{ $group->zone->name }}</span>
                                        <div class="text-[10px] text-gray-450">{{ $group->zone->district?->name }}</div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    <div class="flex flex-wrap gap-1 justify-center max-w-[180px] mx-auto">
                                        @foreach($group->days as $day)
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-purple-50 text-purple-700 rounded border border-purple-100 uppercase">
                                                {{ substr($day, 0, 2) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($group->status === 'active')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">ACTIVO</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-full border border-gray-200">INACTIVO</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        {{-- Editar --}}
                                        <a href="{{ route('admin.staff-group.edit', $group->id) }}"
                                           class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                        {{-- Eliminar --}}
                                        <form action="{{ route('admin.staff-group.destroy', $group->id) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar el grupo de personal «{{ $group->name }}»?');" class="inline">
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
                                    No se encontraron grupos de personal registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($groups->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
