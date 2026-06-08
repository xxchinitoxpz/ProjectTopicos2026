<x-app-layout>
    <x-slot:title>
        Listado de Vacaciones
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Vacaciones</h3>
                <p class="text-xs text-gray-400">Administra las solicitudes de vacaciones, aprobaciones y días acumulados del personal.</p>
            </div>
            <div>
                <a href="{{ route('admin.vacation.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Solicitud
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search & Filter Area -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <form action="{{ route('admin.vacation.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-1 w-full gap-2">
                        <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por DNI o nombre del personal..." class="w-full text-sm max-w-md" />
                        
                        <select name="state" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">
                            <option value="">Todos los estados</option>
                            <option value="pending" {{ $stateFilter === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="approved" {{ $stateFilter === 'approved' ? 'selected' : '' }}>Aprobado</option>
                            <option value="rejected" {{ $stateFilter === 'rejected' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                        
                        <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                            Filtrar
                        </button>
                        @if($search || $stateFilter)
                            <a href="{{ route('admin.vacation.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Personal</th>
                            <th class="py-3.5 px-6 text-center">Días Solicitados</th>
                            <th class="py-3.5 px-6">Fecha Inicio</th>
                            <th class="py-3.5 px-6">Fecha Fin</th>
                            <th class="py-3.5 px-6">Fecha Solicitud</th>
                            <th class="py-3.5 px-6">Observaciones</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center" width="220">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($vacations as $vacation)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $vacation->staff?->photo_url }}" class="w-10 h-10 object-cover rounded-lg border border-gray-200 shadow-sm" alt="{{ $vacation->staff?->name }}">
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $vacation->staff?->name }} {{ $vacation->staff?->last_name }}</div>
                                            <div class="text-[10px] text-gray-400 mt-0.5">DNI: {{ $vacation->staff?->dni }} | Disp: <strong>{{ $vacation->staff?->vacation_days }} días</strong></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-center font-bold text-gray-900">
                                    {{ $vacation->days_requested }} {{ $vacation->days_requested == 1 ? 'día' : 'días' }}
                                </td>
                                <td class="py-3.5 px-6 text-xs font-semibold text-gray-650">
                                    {{ $vacation->date_start->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 text-xs font-semibold text-gray-650">
                                    {{ $vacation->date_end->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 text-xs text-gray-400">
                                    {{ $vacation->date_request->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 text-xs text-gray-500">
                                    {{ $vacation->notes ?? '-' }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($vacation->state === 'pending')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-100">Pendiente</span>
                                    @elseif($vacation->state === 'approved')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">Aprobado</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-red-50 text-red-650 rounded-full border border-red-100">Rechazado</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        @if($vacation->state === 'pending')
                                            <!-- Approve Button Action -->
                                            <form action="{{ route('admin.vacation.approve', $vacation->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de APROBAR esta solicitud de vacaciones? Se restarán los días correspondientes.');" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition" title="Aprobar Solicitud">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Reject Button Action -->
                                            <form action="{{ route('admin.vacation.reject', $vacation->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de RECHAZAR esta solicitud de vacaciones?');" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Rechazar Solicitud">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Edit button -->
                                            <a href="{{ route('admin.vacation.edit', $vacation->id) }}" data-turbo-frame="modal" class="p-1.5 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </a>

                                            <!-- Delete button -->
                                            <form action="{{ route('admin.vacation.destroy', $vacation->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta solicitud de vacaciones?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No modificable</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">
                                    No se encontraron solicitudes de vacaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vacations->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $vacations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
