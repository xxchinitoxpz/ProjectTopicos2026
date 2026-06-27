<x-app-layout>
    <x-slot:title>
        Horarios de Mantenimiento
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">{{ $maintenance->nombre }}</h3>
                <p class="text-xs text-gray-400">{{ $maintenance->fecha_inicio->format('d/m/Y') }} - {{ $maintenance->fecha_fin->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.maintenance.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                    Volver
                </a>
                <a href="{{ route('admin.maintenance.schedule.create', $maintenance) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Horario
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Dia</th>
                            <th class="py-3.5 px-6">Vehiculo</th>
                            <th class="py-3.5 px-6">Responsable</th>
                            <th class="py-3.5 px-6">Tipo</th>
                            <th class="py-3.5 px-6">Inicio</th>
                            <th class="py-3.5 px-6">Fin</th>
                            <th class="py-3.5 px-6 min-w-[260px]">Fechas</th>
                            <th class="py-3.5 px-6 text-center" width="170">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6 font-bold text-gray-900 capitalize">
                                    {{ $schedule->dia_semana }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650 font-semibold">
                                    {{ $schedule->vehicle->name }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650 font-semibold">
                                    {{ $schedule->responsible->name }} {{ $schedule->responsible->last_name }}
                                </td>
                                <td class="py-3.5 px-6 text-xs text-gray-600 font-medium capitalize">
                                    {{ $schedule->tipo_mantenimiento }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650 font-semibold">
                                    {{ substr($schedule->hora_inicio, 0, 5) }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650 font-semibold">
                                    {{ substr($schedule->hora_fin, 0, 5) }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex gap-1.5 max-w-sm overflow-x-auto whitespace-nowrap pb-1">
                                        @foreach($schedule->days as $day)
                                            <span class="shrink-0 px-2 py-1 text-[10px] font-bold bg-blue-50 text-usat-blue rounded-full border border-blue-100">
                                                {{ $day->fecha->format('d/m/Y') }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.maintenance.schedule.days.index', [$maintenance, $schedule]) }}" class="p-2 bg-blue-50 text-usat-blue hover:bg-blue-100 rounded-lg transition duration-150" title="Ver">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.maintenance.schedule.edit', [$maintenance, $schedule]) }}" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.maintenance.schedule.destroy', [$maintenance, $schedule]) }}" method="POST" onsubmit="return confirm('Esta seguro de eliminar este horario y sus dias generados?');" class="inline" data-ajax-form>
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
                                    No se encontraron horarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
