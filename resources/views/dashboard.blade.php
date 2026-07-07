<x-app-layout>
    <x-slot:title>
        Dashboard General
    </x-slot:title>

    <div class="space-y-6">
        <section class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Dashboard General</h1>
                <p class="mt-1 text-sm text-slate-500">Monitoreo y gestion de programaciones en tiempo real</p>
            </div>

            <a href="{{ route('admin.planning.index') }}"
               class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 5h8m-8 5h5M7 3h10a2 2 0 012 2v14l-4-3-4 3-4-3-4 3V5a2 2 0 012-2z" />
                </svg>
                Ir al Modulo de Programacion
            </a>
        </section>

        <section class="grid gap-3 xl:grid-cols-4">
            <article class="rounded-sm bg-sky-600 text-white shadow-sm">
                <div class="flex min-h-[96px] items-center justify-between px-4 py-4">
                    <div>
                        <p class="text-3xl font-bold leading-none">{{ $totalPlannings }}</p>
                        <p class="mt-2 text-sm font-medium text-white/90">Total Programaciones</p>
                    </div>
                    <svg class="h-12 w-12 text-black/10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1.5A2.5 2.5 0 0 1 22 6.5v13A2.5 2.5 0 0 1 19.5 22h-15A2.5 2.5 0 0 1 2 19.5v-13A2.5 2.5 0 0 1 4.5 4H6V3a1 1 0 0 1 1-1Zm-3 8v9.5c0 .28.22.5.5.5h15a.5.5 0 0 0 .5-.5V10H4Zm3-4H4.5a.5.5 0 0 0-.5.5V8h16V6.5a.5.5 0 0 0-.5-.5H18v1a1 1 0 1 1-2 0V5H8v1a1 1 0 1 1-2 0V5Z" />
                    </svg>
                </div>
            </article>

            <article class="rounded-sm bg-emerald-500 text-white shadow-sm">
                <div class="flex min-h-[96px] items-center justify-between px-4 py-4">
                    <div>
                        <p class="text-3xl font-bold leading-none">{{ $completePlannings }}</p>
                        <p class="mt-2 text-sm font-medium text-white/90">Programaciones Completas</p>
                    </div>
                    <svg class="h-12 w-12 text-black/10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 16.2 6.8 13l-1.4 1.4L10 19.2 19.6 9.6l-1.4-1.4zM12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8Z" />
                    </svg>
                </div>
            </article>

            <article class="rounded-sm bg-rose-500 text-white shadow-sm">
                <div class="flex min-h-[96px] items-center justify-between px-4 py-4">
                    <div>
                        <p class="text-3xl font-bold leading-none">{{ $incompletePlannings }}</p>
                        <p class="mt-2 text-sm font-medium text-white/90">Programaciones Incompletas</p>
                    </div>
                    <svg class="h-12 w-12 text-black/10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
                    </svg>
                </div>
            </article>

            <article class="rounded-sm bg-amber-400 text-slate-900 shadow-sm">
                <div class="flex min-h-[96px] items-center justify-between px-4 py-4">
                    <div>
                        <p class="text-3xl font-bold leading-none">{{ $missingStaffTotal }}</p>
                        <p class="mt-2 text-sm font-medium text-slate-800">Personal Faltante</p>
                    </div>
                    <svg class="h-12 w-12 text-black/10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z" />
                    </svg>
                </div>
            </article>
        </section>

        <section class="rounded-sm border border-slate-200 bg-white px-4 py-4 shadow-sm">
            <form method="GET" action="{{ route('dashboard') }}" class="grid gap-4 md:grid-cols-3 md:items-end">
                <label class="block min-w-0">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Fecha:</span>
                    <div class="flex items-center rounded border border-slate-300 bg-slate-50">
                        <span class="px-3 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2v4M16 2v4M3 10h18M5 6h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                            </svg>
                        </span>
                        <input type="date"
                               name="date"
                               value="{{ $selectedDate->format('Y-m-d') }}"
                               class="w-full border-0 bg-transparent py-2.5 text-sm text-slate-700 focus:ring-0">
                    </div>
                </label>

                <label class="block min-w-0">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Turno:</span>
                    <div class="flex items-center rounded border border-slate-300 bg-white">
                        <span class="px-3 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                            </svg>
                        </span>
                        <select name="shift_id" class="w-full border-0 bg-transparent py-2.5 text-sm text-slate-700 focus:ring-0">
                            <option value="">Todos los turnos</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}" @selected((string) $selectedShiftId === (string) $shift->id)>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                <div class="flex items-end">
                    <button type="submit"
                            class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-sky-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                        </svg>
                        Buscar
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-sm border border-slate-200 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-1 text-sm sm:flex-row sm:items-center sm:gap-2">
                <p class="font-semibold text-slate-900">Filtros activos</p>
                <p class="text-slate-600">Fecha: {{ $selectedDateLabel }} <span class="mx-1 text-slate-300">|</span> Turno: {{ $selectedShiftName }}</p>
            </div>
        </section>

        @if ($cards->isEmpty())
            <section class="rounded-sm border border-dashed border-slate-300 bg-white px-8 py-10 text-center shadow-sm">
                <p class="text-lg font-semibold text-slate-800">No hay programaciones para esta fecha</p>
                <p class="mt-2 text-sm text-slate-500">Prueba otra fecha o cambia el turno para ver tarjetas de zonas.</p>
            </section>
        @else
            <section class="grid gap-4 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach ($cards as $card)
                    <article class="overflow-hidden rounded-sm border border-slate-200 bg-white shadow-sm">
                        <header class="flex items-center justify-between px-4 py-2 text-white {{ $card['is_complete'] ? 'bg-emerald-500' : 'bg-rose-500' }}">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2c3.87 0 7 3.13 7 7 0 5.25-7 13-7 13S5 14.25 5 9c0-3.87 3.13-7 7-7Zm0 4a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                                </svg>
                                <span class="text-sm font-semibold">{{ $card['zone_name'] }}</span>
                            </div>

                            @if ($card['show_edit_button'])
                                <a href="{{ $card['edit_url'] ?? $card['detail_url'] }}"
                                   data-turbo-frame="modal"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-amber-400 text-white shadow-sm transition hover:bg-amber-500"
                                   title="Editar">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.5-9.5a2.1 2.1 0 1 1 3 3L12 16l-4 1 1-4 8.5-8.5Z" />
                                    </svg>
                                </a>
                            @else
                                <span class="rounded-full bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-900">
                                    OK
                                </span>
                            @endif
                        </header>

                        <div class="px-4 py-4">
                            <div class="grid grid-cols-2 gap-4 text-center text-sm">
                                <div>
                                    <div class="mb-2 flex justify-center text-sky-500">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2a5 5 0 0 0-5 5c0 1.4.58 2.64 1.5 3.54V12H9v2H8v2h8v-2h-1v-2h.5V10.54A5.01 5.01 0 0 0 17 7a5 5 0 0 0-5-5Z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-500">Turno</p>
                                    <p class="mt-1 font-semibold text-slate-800">{{ $card['shift_name'] }}</p>
                                </div>
                                <div>
                                    <div class="mb-2 flex justify-center text-amber-500">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20 8h-3V6a2 2 0 0 0-2-2H9A2 2 0 0 0 7 6v2H4a2 2 0 0 0-2 2v5h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h2v-5a2 2 0 0 0-2-2Zm-5 0H9V6h6Zm-9 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm14 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-500">Vehiculo</p>
                                    <p class="mt-1 font-semibold text-slate-800">{{ $card['vehicle_name'] }}</p>
                                </div>
                            </div>

                            <div class="my-4 text-center">
                                <p class="text-xs text-slate-500">Grupo</p>
                                <p class="mt-1 text-base font-semibold text-slate-900">{{ $card['group_name'] }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-sm bg-emerald-500 px-3 py-3 text-center text-white">
                                    <div class="flex items-center justify-center gap-1 text-sm font-semibold">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z" />
                                        </svg>
                                        <span>{{ $card['present_count'] }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-medium">Presentes</p>
                                </div>
                                <div class="rounded-sm bg-rose-500 px-3 py-3 text-center text-white">
                                    <div class="flex items-center justify-center gap-1 text-sm font-semibold">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
                                        </svg>
                                        <span>{{ $card['missing_count'] }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-medium">Faltantes</p>
                                </div>
                            </div>

                            <a href="{{ $card['detail_url'] }}"
                               data-turbo-frame="modal"
                               class="mt-4 inline-flex w-full items-center justify-center rounded-sm bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Ver Detalles
                            </a>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
</x-app-layout>
