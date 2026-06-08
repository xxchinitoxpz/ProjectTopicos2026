<x-app-layout>
    <x-slot:title>
        Listado de Contratos
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Contratos</h3>
                <p class="text-xs text-gray-400">Gestiona los contratos, salarios y períodos de prueba del personal.</p>
            </div>
            <div>
                <a href="{{ route('admin.contract.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Contrato
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search & Filter Area -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <form action="{{ route('admin.contract.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-1 w-full gap-2">
                        <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por DNI o nombre del personal..." class="w-full text-sm max-w-md" />
                        
                        <select name="contract_type" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="permanente" {{ $typeFilter === 'permanente' ? 'selected' : '' }}>Permanente</option>
                            <option value="nombrado" {{ $typeFilter === 'nombrado' ? 'selected' : '' }}>Nombrado</option>
                            <option value="temporal" {{ $typeFilter === 'temporal' ? 'selected' : '' }}>Temporal</option>
                        </select>
                        
                        <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                            Filtrar
                        </button>
                        @if($search || $typeFilter)
                            <a href="{{ route('admin.contract.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
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
                            <th class="py-3.5 px-6">Tipo Contrato</th>
                            <th class="py-3.5 px-6">Fecha Inicio</th>
                            <th class="py-3.5 px-6">Fecha Fin</th>
                            <th class="py-3.5 px-6">Salario</th>
                            <th class="py-3.5 px-6 text-center">P. Prueba</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($contracts as $contract)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $contract->staff?->photo_url }}" class="w-10 h-10 object-cover rounded-lg border border-gray-200 shadow-sm" alt="{{ $contract->staff?->name }}">
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $contract->staff?->name }} {{ $contract->staff?->last_name }}</div>
                                            <div class="text-[10px] text-gray-400 mt-0.5">DNI: {{ $contract->staff?->dni }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    @if($contract->contract_type === 'permanente')
                                        <span class="px-2.5 py-1 text-xs font-bold bg-purple-50 text-purple-700 rounded-lg border border-purple-100">Permanente</span>
                                    @elseif($contract->contract_type === 'nombrado')
                                        <span class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-700 rounded-lg border border-blue-100">Nombrado</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-lg border border-amber-100">Temporal</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-xs font-semibold text-gray-650">
                                    {{ $contract->date_start->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 text-xs font-semibold text-gray-650">
                                    @if($contract->date_end)
                                        {{ $contract->date_end->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400 italic">Indefinido</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 font-bold text-gray-900">
                                    S/. {{ number_format($contract->salary, 2) }}
                                </td>
                                <td class="py-3.5 px-6 text-center text-xs font-semibold">
                                    @if($contract->probation)
                                        <span class="text-gray-700">{{ $contract->probation }} {{ $contract->probation == 1 ? 'mes' : 'meses' }}</span>
                                    @else
                                        <span class="text-gray-400 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($contract->state === 'active')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">Activo</span>
                                    @elseif($contract->state === 'inactive')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-150 text-gray-505 rounded-full border border-gray-200">Inactivo</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-red-50 text-red-600 rounded-full border border-red-100">Vencido</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.contract.edit', $contract->id) }}" data-turbo-frame="modal" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.contract.destroy', $contract->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este contrato?');" class="inline">
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
                                    No se encontraron contratos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($contracts->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $contracts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
