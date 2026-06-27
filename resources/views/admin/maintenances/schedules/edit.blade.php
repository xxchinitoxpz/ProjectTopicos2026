<x-app-layout>
    <x-slot:title>
        Modificar Horario
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Modificar Horario</h3>
                <p class="text-xs text-gray-400">{{ $maintenance->nombre }}</p>
            </div>
            <a href="{{ route('admin.maintenance.schedule.index', $maintenance) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                Volver
            </a>
        </div>

        <div class="flex flex-col xl:flex-row gap-6 items-start">
            <div class="w-full xl:w-1/2 min-w-0 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h4 class="text-sm font-bold text-gray-700">Calendario del Horario</h4>
                    <p class="text-xs text-gray-400 mt-1">Fechas generadas para {{ ucfirst($schedule->dia_semana) }}.</p>
                </div>

                <div class="p-5 space-y-4">
                    @foreach($calendarMonths as $month)
                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                            <div class="px-3 py-2 bg-usat-blue text-white text-xs font-bold">
                                {{ $month['label'] }}
                            </div>
                            <table class="w-full table-fixed border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100">
                                        @foreach(['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'] as $dayName)
                                            <th class="py-1.5 text-center">{{ $dayName }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($month['weeks'] as $week)
                                        <tr>
                                            @foreach($week as $day)
                                                @if($day)
                                                    <td class="h-9 text-center align-middle border-r border-gray-100 last:border-r-0 {{ $day['in_range'] ? 'text-gray-700' : 'text-gray-300 bg-gray-50/60' }}">
                                                        <span class="mx-auto w-7 h-7 flex items-center justify-center rounded-full text-[11px] font-bold {{ $day['marked'] ? 'bg-emerald-600 text-white shadow-sm' : '' }}">
                                                            {{ $day['day'] }}
                                                        </span>
                                                    </td>
                                                @else
                                                    <td class="h-9 border-r border-gray-100 last:border-r-0 bg-gray-50/60"></td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full xl:w-1/2 min-w-0 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('admin.maintenance.schedule.update', [$maintenance, $schedule]) }}" method="POST" class="p-6 space-y-6" data-turbo="false" data-ajax-form>
                    @csrf
                    @method('PUT')
                    <div data-ajax-feedback class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                    @include('admin.maintenances.schedules.form')

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.maintenance.schedule.index', $maintenance) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                            Actualizar Horario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
