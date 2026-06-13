<x-app-layout>
    <x-slot:title>
        Listado de Asistencias
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Asistencias</h3>
                <p class="text-xs text-gray-400">Registra y administra las asistencias del personal.</p>
            </div>
            <div>
                <a href="{{ route('admin.assistance.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Registrar Asistencia
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search & Pagination Filter -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                <form action="{{ route('admin.assistance.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por DNI o nombre..." class="flex-1 min-w-[200px] text-sm" />
                    
                    <select name="state" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">Todos los estados</option>
                        <option value="presente" {{ $stateFilter === 'presente' ? 'selected' : '' }}>Presente</option>
                        <option value="ausente" {{ $stateFilter === 'ausente' ? 'selected' : '' }}>Ausente</option>
                    </select>
                    
                    <x-text-input type="date" name="date" value="{{ $dateFilter }}" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200" />
                    
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Filtrar
                    </button>
                    @if($search || $stateFilter || $dateFilter)
                        <a href="{{ route('admin.assistance.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center justify-center">
                            Limpiar
                        </a>
                    @endif
                </form>
                <form action="{{ route('admin.assistance.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500 xl:justify-end">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="state" value="{{ $stateFilter }}">
                    <input type="hidden" name="date" value="{{ $dateFilter }}">
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
                            <th class="py-3.5 px-6">Personal</th>
                            <th class="py-3.5 px-6">Fecha</th>
                            <th class="py-3.5 px-6">Hora</th>
                            <th class="py-3.5 px-6">Tipo</th>
                            <th class="py-3.5 px-6">Turno</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6">Notas</th>
                            <th class="py-3.5 px-6 text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($assistances as $assistance)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center space-x-3">
                                        @if($assistance->staff?->photo)
                                            <img src="{{ $assistance->staff->photo_url }}" class="w-9 h-9 object-cover rounded-lg border border-gray-200 shadow-sm" alt="{{ $assistance->staff->name }}">
                                        @else
                                            <div class="w-9 h-9 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center font-bold text-gray-500 uppercase">
                                                {{ substr($assistance->staff?->name, 0, 1) }}{{ substr($assistance->staff?->last_name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $assistance->staff?->name }} {{ $assistance->staff?->last_name }}</div>
                                            <div class="text-[10px] text-gray-400 mt-0.5">DNI: {{ $assistance->staff?->dni }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-gray-750">
                                    {{ $assistance->date_time->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-gray-750">
                                    {{ $assistance->date_time->format('H:i') }}
                                </td>
                                <td class="py-3.5 px-6">
                                    @if($assistance->type === 'ingreso')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            Ingreso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-750 border border-amber-100">
                                            Salida
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-xs text-gray-600 font-medium">
                                    {{ $assistance->shift?->name ?? 'Sin turno' }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($assistance->state === 'presente')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">Presente</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-red-50 text-red-650 rounded-full border border-red-100">Ausente</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-xs text-gray-500 max-w-xs truncate" title="{{ $assistance->notes }}">
                                    {{ $assistance->notes ?? '-' }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.assistance.edit', $assistance->id) }}" data-turbo-frame="modal" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.assistance.destroy', $assistance->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta asistencia?');" class="inline">
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
                                    No se encontraron asistencias registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assistances->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $assistances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
