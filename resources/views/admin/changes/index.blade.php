<x-app-layout>
    <x-slot:title>
        Cambios de Programaciones
    </x-slot:title>

    @php
        $listUrl = route('admin.change.index');
    @endphp

    <div class="space-y-4">
        <div class="flex justify-end rounded-sm bg-white px-3 py-4 shadow-sm">
            <a href="{{ route('admin.change.create') }}" data-turbo-frame="modal" class="inline-flex items-center justify-center rounded-sm bg-sky-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-sky-600">
                <svg class="me-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V8m0 0l-4 4m4-4l4 4m6-4v8m0 0l-4-4m4 4l4-4" />
                </svg>
                Nuevo Cambio Masivo
            </a>
        </div>

        <div class="rounded-sm border border-gray-200 bg-white p-3 shadow-sm">
            <form action="{{ $listUrl }}" method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                <div class="md:col-span-2">
                    <label for="date_start" class="mb-1 block text-[11px] font-semibold text-gray-700">Fecha de inicio</label>
                    <input id="date_start" type="date" name="date_start" value="{{ $dateStart }}" class="block h-10 w-full rounded-sm border-gray-300 text-xs text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                </div>

                <div class="md:col-span-2">
                    <label for="date_end" class="mb-1 block text-[11px] font-semibold text-gray-700">Fecha de fin</label>
                    <input id="date_end" type="date" name="date_end" value="{{ $dateEnd }}" class="block h-10 w-full rounded-sm border-gray-300 text-xs text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                </div>

                <div class="md:col-span-4">
                    <label for="change_type" class="mb-1 block text-[11px] font-semibold text-gray-700">Tipo de cambio</label>
                    <select id="change_type" name="change_type" class="block h-10 w-full rounded-sm border-gray-300 text-xs text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                        <option value="">Todos los tipos</option>
                        <option value="creacion" @selected($changeType === 'creacion')>Creacion</option>
                        <option value="actualizacion" @selected($changeType === 'actualizacion')>Actualizacion</option>
                        <option value="finalizacion" @selected($changeType === 'finalizacion')>Finalizacion</option>
                        <option value="turno" @selected($changeType === 'turno')>Turno</option>
                        <option value="vehiculo" @selected($changeType === 'vehiculo')>Vehiculo</option>
                        <option value="conductor" @selected($changeType === 'conductor')>Conductor</option>
                        <option value="helper" @selected($changeType === 'helper')>Ayudante</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-sm bg-sky-600 px-8 text-xs font-semibold text-white transition hover:bg-sky-700">
                    <svg class="me-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 8h12M9 12h6M11 16h2" />
                    </svg>
                    Filtrar
                    </button>
                </div>

                <div class="md:col-span-2">
                    <a href="{{ $listUrl }}" class="inline-flex h-10 w-full items-center justify-center rounded-sm border border-gray-300 bg-white px-6 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                        ✕ Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-sm border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 text-[11px] text-gray-600 sm:flex-row sm:items-center sm:justify-between">
                <form action="{{ $listUrl }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="date_start" value="{{ $dateStart }}">
                    <input type="hidden" name="date_end" value="{{ $dateEnd }}">
                    <input type="hidden" name="change_type" value="{{ $changeType }}">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <span>Mostrar</span>
                    <select name="per_page" onchange="this.form.submit()" class="h-7 rounded-sm border-gray-300 py-0 text-[11px]">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>

                <form action="{{ $listUrl }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="date_start" value="{{ $dateStart }}">
                    <input type="hidden" name="date_end" value="{{ $dateEnd }}">
                    <input type="hidden" name="change_type" value="{{ $changeType }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <label for="search_table">Buscar:</label>
                    <input id="search_table" type="text" name="search" value="{{ $search }}" class="h-7 w-36 rounded-sm border border-gray-300 text-[11px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200 sm:w-44">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left">
                    <thead class="bg-gray-50">
                        <tr class="border-b border-gray-200 text-[10px] font-bold uppercase tracking-wide text-gray-500">
                            <th class="px-5 py-3">Tipo de cambio</th>
                            <th class="px-5 py-3">Fecha Cambio</th>
                            <th class="px-5 py-3">Antes</th>
                            <th class="px-5 py-3">Despues</th>
                            <th class="px-5 py-3">Realizado por</th>
                            <th class="px-5 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($changes as $change)
                            @php
                                $typeColors = match ($change->change_type) {
                                    'turno' => 'bg-amber-300 text-gray-900',
                                    'vehiculo' => 'bg-emerald-500 text-white',
                                    'conductor', 'helper' => 'bg-sky-500 text-white',
                                    'actualizacion' => 'bg-indigo-500 text-white',
                                    default => 'bg-gray-400 text-white',
                                };

                                $beforeDot = in_array($change->change_type, ['turno', 'actualizacion']) ? 'bg-amber-400' : 'bg-gray-400';
                                $afterDot = in_array($change->change_type, ['turno', 'vehiculo', 'conductor', 'helper', 'actualizacion']) ? 'bg-emerald-500' : 'bg-gray-400';
                            @endphp
                            <tr class="text-xs text-gray-700 transition hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-sm px-2 py-0.5 text-[10px] font-bold {{ $typeColors }}">
                                        {{ ucfirst(str_replace('_', ' ', $change->change_type)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1 text-[11px] font-medium text-gray-700">
                                        <svg class="h-3 w-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6 2a1 1 0 012 0v1h4V2a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h1V2Zm9 6H5v7h10V8Z" />
                                        </svg>
                                        {{ $change->created_at?->format('d/m/Y') ?? '-' }}
                                    </div>
                                    <div class="mt-1 flex items-center gap-1 text-[10px] text-gray-400">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm1-11a1 1 0 10-2 0v3a1 1 0 00.293.707l2 2a1 1 0 101.414-1.414L11 9.586V7Z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $change->created_at?->format('H:i') ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-2.5 w-2.5 rounded-full {{ $beforeDot }}"></span>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $change->old_value }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $change->reason_type }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-2.5 w-2.5 rounded-full {{ $afterDot }}"></span>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $change->new_value }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $change->planningDay?->planning?->staffGroup?->name ?? 'Sin grupo' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="mt-0.5 h-3.5 w-3.5 text-blue-900" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6Zm-7 8a7 7 0 1114 0H3Z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $change->user?->name ?? 'Sistema' }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $change->user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.change.show', $change->id) }}" data-turbo-frame="modal" class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-sky-600 text-white transition hover:bg-sky-700" title="Ver detalle">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Zm6 0c-1.5 4-5.5 7-9 7s-7.5-3-9-7c1.5-4 5.5-7 9-7s7.5 3 9 7Z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.change.destroy', $change->id) }}" method="POST" onsubmit="return confirm('Esta seguro de eliminar este cambio?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-red-500 text-white transition hover:bg-red-600" title="Eliminar">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0v12m4-12v12M7 7l1 14h8l1-14" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                                    No se encontraron cambios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($changes->hasPages())
                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $changes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
