<x-app-layout>
    <x-slot:title>
        Dias Generados
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">{{ $maintenance->nombre }}</h3>
                <p class="text-xs text-gray-400">
                    {{ ucfirst($schedule->dia_semana) }} - {{ $schedule->vehicle->name }} - {{ $schedule->responsible->name }} {{ $schedule->responsible->last_name }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.maintenance.schedule.index', $maintenance) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                    Volver
                </a>
                <a href="{{ route('admin.maintenance.schedule.edit', [$maintenance, $schedule]) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    Editar Horario
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Fecha</th>
                            <th class="py-3.5 px-6">Observacion</th>
                            <th class="py-3.5 px-6">Imagen</th>
                            <th class="py-3.5 px-6 text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($schedule->days as $day)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6 font-bold text-gray-900">
                                    {{ $day->fecha->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-6 text-gray-650">
                                    {{ $day->observacion ?: '-' }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <img
                                        src="{{ $day->image_url }}"
                                        onerror="this.onerror=null;this.src='{{ \App\Support\PublicImageStorage::placeholder() }}';"
                                        alt="Imagen del dia"
                                        class="w-16 h-16 object-cover rounded-lg border border-gray-200 {{ $day->imagen ? '' : 'opacity-60' }}"
                                    >
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <span class="p-2 rounded-lg {{ $day->realizado ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-650' }}" title="{{ $day->realizado ? 'Realizado' : 'No realizado' }}">
                                            @if($day->realizado)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 6l12 12M6 18L18 6"></path>
                                                </svg>
                                            @endif
                                        </span>
                                        <a href="{{ route('admin.maintenance.schedule.days.edit', [$maintenance, $schedule, $day]) }}" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400">
                                    No se encontraron dias generados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
