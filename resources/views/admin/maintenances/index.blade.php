<x-app-layout>
    <x-slot:title>
        Listado de Mantenimientos
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Mantenimientos</h3>
                <p class="text-xs text-gray-400">Gestiona la programacion de mantenimiento de los vehiculos.</p>
            </div>
            <div>
                <a href="{{ route('admin.maintenance.create') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Mantenimiento
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.maintenance.index') }}" method="GET" class="flex flex-1 gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre..." class="flex-1 text-sm" />
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('admin.maintenance.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>
                <form action="{{ route('admin.maintenance.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500">
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

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Nombre</th>
                            <th class="py-3.5 px-6">Inicio</th>
                            <th class="py-3.5 px-6">Fin</th>
                            <th class="py-3.5 px-6 text-center" width="180">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($maintenances as $maintenance)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6 font-bold text-gray-900">
                                    {{ $maintenance->nombre }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650 font-semibold">
                                    {{ $maintenance->fecha_inicio->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650 font-semibold">
                                    {{ $maintenance->fecha_fin->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.maintenance.schedule.index', $maintenance->id) }}" class="p-2 bg-blue-50 text-usat-blue hover:bg-blue-100 rounded-lg transition duration-150" title="Horarios">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.maintenance.edit', $maintenance->id) }}" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.maintenance.destroy', $maintenance->id) }}" method="POST" onsubmit="return confirm('Esta seguro de eliminar este mantenimiento?');" class="inline" data-ajax-form>
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
                                <td colspan="4" class="py-8 text-center text-gray-400">
                                    No se encontraron mantenimientos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($maintenances->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $maintenances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
