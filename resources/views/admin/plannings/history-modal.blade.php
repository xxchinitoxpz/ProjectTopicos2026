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

            <!-- Table log -->
            <div class="border border-gray-150 rounded-xl overflow-hidden shadow-xs bg-white">
                <div class="max-h-96 overflow-y-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-150 text-gray-450 font-bold uppercase bg-gray-50/70">
                                <th class="py-3 px-4" width="140">Fecha y Hora</th>
                                <th class="py-3 px-4" width="130">Usuario</th>
                                <th class="py-3 px-4" width="100">Acción</th>
                                <th class="py-3 px-4">Detalles de Cambios</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($history as $log)
                                <tr class="hover:bg-gray-50/30 transition">
                                    <td class="py-3 px-4 font-medium text-gray-550">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-gray-900 leading-tight">{{ $log->user?->name }}</div>
                                        <div class="text-[9px] text-gray-400">{{ $log->user?->email }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($log->action === 'created')
                                            <span class="px-2 py-0.5 text-[9px] font-bold bg-blue-50 text-usat-blue rounded-md border border-blue-100">CREADO</span>
                                        @elseif($log->action === 'updated')
                                            <span class="px-2 py-0.5 text-[9px] font-bold bg-amber-50 text-usat-gold rounded-md border border-amber-100">MODIFICADO</span>
                                        @elseif($log->action === 'finished')
                                            <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-700 rounded-md border border-emerald-100">FINALIZADO</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[9px] font-bold bg-red-50 text-red-650 rounded-md border border-red-100">{{ strtoupper($log->action) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-gray-650 font-medium whitespace-pre-line text-xs leading-relaxed">
                                        {{ $log->details }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-400 italic">
                                        No se registran modificaciones para esta programación.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
