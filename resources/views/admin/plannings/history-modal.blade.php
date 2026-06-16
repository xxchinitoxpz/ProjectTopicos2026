<x-app-layout>
    <x-slot:title>
        Historial de Cambios
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-3xl">
        <div data-modal-size="max-w-3xl" class="max-w-3xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Historial de Cambios</h3>
                    <p class="text-xs text-gray-400">Auditoría de modificaciones para la programación del grupo: <strong>{{ $planning->staffGroup?->name ?? 'Grupo Eliminado' }}</strong></p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.planning.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Personal Asignado Section -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-gray-800 flex items-center uppercase tracking-wide">
                    <svg class="w-4 h-4 me-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Personal Asignado
                </h4>
                <div class="border border-gray-150 rounded-xl overflow-hidden bg-white shadow-xs divide-y divide-gray-100">
                    <!-- Conductor row -->
                    <div class="flex items-center justify-between p-3 text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 text-[9px] font-bold bg-blue-600 text-white rounded-lg">Conductor</span>
                            <span class="font-semibold text-gray-850">{{ $planning->driver?->name }} {{ $planning->driver?->last_name }}</span>
                        </div>
                    </div>
                    <!-- Helpers rows -->
                    @forelse($planning->helpers as $helper)
                        <div class="flex items-center justify-between p-3 text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-600 text-white rounded-lg">Ayudante</span>
                                <span class="font-medium text-gray-750">{{ $helper->name }} {{ $helper->last_name }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-xs text-gray-400 italic">
                            Sin ayudantes asignados para este día.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Audit Changes log -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-gray-800 flex items-center uppercase tracking-wide">
                    <svg class="w-4 h-4 me-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Historial de Cambios
                </h4>
                <div class="border border-gray-150 rounded-xl overflow-hidden shadow-xs bg-white">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-gray-150 text-gray-450 font-bold uppercase bg-gray-50/70 text-[10px]">
                                    <th class="py-3 px-4" width="130">Fecha</th>
                                    <th class="py-3 px-4" width="100">Tipo</th>
                                    <th class="py-3 px-4" width="130">Anterior</th>
                                    <th class="py-3 px-4" width="130">Nuevo</th>
                                    <th class="py-3 px-4">Motivo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                                @forelse($history as $log)
                                    <tr class="hover:bg-gray-50/30 transition">
                                        <td class="py-3 px-4 font-medium text-gray-550">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($log->change_type === 'turno')
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-amber-50 text-amber-700 rounded-md border border-amber-100">Turno</span>
                                            @elseif($log->change_type === 'vehiculo')
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-blue-50 text-blue-700 rounded-md border border-blue-100">Vehículo</span>
                                            @elseif($log->change_type === 'conductor' || $log->change_type === 'helper')
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-700 rounded-md border border-emerald-100">Personal</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[9px] font-bold bg-gray-55 text-gray-600 rounded-md border border-gray-200 capitalize">{{ $log->change_type }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-semibold text-gray-800">
                                            {{ $log->old_value }}
                                        </td>
                                        <td class="py-3 px-4 font-semibold text-gray-900">
                                            {{ $log->new_value }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-650 leading-relaxed">
                                            <div class="font-bold text-gray-800 text-[10px]">{{ $log->reason_type }}</div>
                                            <div class="text-gray-500 whitespace-pre-line">{{ $log->details }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-400 italic">
                                            No se registran modificaciones para esta programación.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.planning.index') }}'" 
                        class="px-5 py-2.5 bg-gray-150 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                    Cerrar
                </button>
            </div>
        </div>
    </turbo-frame>
</x-app-layout>
