@php
    $currentShift = $planningDay?->shift ?? $group->shift;
    $currentVehicle = $planningDay?->vehicle ?? $group->vehicle;
    $currentDriver = $planningDay?->driver ?? $group->driver;
    $currentHelpers = ($planningDay?->helpers ?? $group->helpers) ?? collect();
@endphp

<turbo-frame id="modal" data-modal-size="max-w-6xl">
    <div data-modal-size="max-w-6xl" class="w-full max-w-6xl mx-auto overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between bg-slate-900 px-5 py-3 text-white">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.5-9.5a2.1 2.1 0 1 1 3 3L12 16l-4 1 1-4 8.5-8.5Z" />
                </svg>
                <h3 class="text-base font-semibold">Editor de Programacion</h3>
            </div>
            <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.history.back()"
                    class="text-white/70 transition hover:text-white focus:outline-none">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST"
              action="{{ route('dashboard.card-details.update', ['group' => $group->id]) }}"
              class="space-y-4 p-4 sm:p-5"
              data-turbo-frame="_top">
            @csrf
            @method('PATCH')
            <input type="hidden" name="planning_day_id" value="{{ $planningDay?->id }}">
            <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">

            <div class="grid gap-4 lg:grid-cols-2">
                <section class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-2 bg-sky-600 px-4 py-2.5 text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 5H11v6l5 3 .75-1.23-4.75-2.77Z" />
                        </svg>
                        <h4 class="text-sm font-semibold">Cambio de Turno</h4>
                    </div>
                    <div class="space-y-3 p-4">
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-slate-700">Turno Actual</span>
                            <div class="flex items-center rounded-md border border-slate-300 bg-slate-100">
                                <span class="px-3 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m4-3a9 9 0 11-18 0 9 9 0 0118 0Z" />
                                    </svg>
                                </span>
                                <input type="text" value="{{ $planningDay?->shift?->name ?? $group->shift?->name ?? 'Sin turno' }}" disabled class="w-full border-0 bg-transparent py-2 text-sm text-slate-600">
                            </div>
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-sky-700">Nuevo Turno</span>
                            <div class="flex items-center rounded-md border border-slate-300 bg-white">
                                <span class="px-3 text-sky-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16" />
                                    </svg>
                                </span>
                                <select name="shift_id" class="w-full border-0 bg-transparent py-2 text-sm text-slate-700 focus:ring-0">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}" @selected(($planningDay?->shift?->id ?? $group->shift?->id) === $shift->id)>{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-2 bg-emerald-500 px-4 py-2.5 text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 8h-3V6a2 2 0 0 0-2-2H9A2 2 0 0 0 7 6v2H4a2 2 0 0 0-2 2v5h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h2v-5a2 2 0 0 0-2-2Zm-5 0H9V6h6Zm-9 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm14 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                        </svg>
                        <h4 class="text-sm font-semibold">Cambio de Vehiculo</h4>
                    </div>
                    <div class="space-y-3 p-4">
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-emerald-700">Vehiculo Actual</span>
                            <div class="flex items-center rounded-md border border-slate-300 bg-slate-100">
                                <span class="px-3 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 8h-3V6a2 2 0 0 0-2-2H9A2 2 0 0 0 7 6v2H4a2 2 0 0 0-2 2v5h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h2v-5a2 2 0 0 0-2-2Zm-5 0H9V6h6Zm-9 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm14 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                    </svg>
                                </span>
                                <input type="text" value="{{ $planningDay?->vehicle?->code ?? $group->vehicle?->code ?? 'Sin vehiculo' }}{{ ($planningDay?->vehicle?->name ?? $group->vehicle?->name) ? ' - '.($planningDay?->vehicle?->name ?? $group->vehicle?->name) : '' }}" disabled class="w-full border-0 bg-transparent py-2 text-sm text-slate-600">
                            </div>
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-emerald-700">Nuevo Vehiculo</span>
                            <div class="flex items-center rounded-md border border-slate-300 bg-white">
                                <span class="px-3 text-emerald-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16" />
                                    </svg>
                                </span>
                                <select name="vehicle_id" class="w-full border-0 bg-transparent py-2 text-sm text-slate-700 focus:ring-0">
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected(($planningDay?->vehicle?->id ?? $group->vehicle?->id) === $vehicle->id)>{{ $vehicle->code }} - {{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </label>
                    </div>
                </section>
            </div>

            <section class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-2 bg-cyan-500 px-4 py-2.5 text-white">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V20h8v-3.5c0-.42.09-.82.24-1.2A8.5 8.5 0 0 0 8 13Zm8 0c-.29 0-.6.03-.92.08A8.4 8.4 0 0 0 16 16.5V20h8v-3.5c0-2.33-4.67-3.5-8-3.5Z" />
                    </svg>
                    <h4 class="text-sm font-semibold">Cambio de Personal</h4>
                </div>
                <div class="space-y-3 p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <div class="mb-1 text-xs font-bold text-sky-700">Personal Actual</div>
                            <div class="flex items-center rounded-md border border-slate-300 bg-slate-100">
                                <span class="px-3 text-sky-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z" />
                                    </svg>
                                </span>
                                <input type="text" value="{{ $planningDay?->driver?->name ?? $group->driver?->name ?? 'Sin conductor' }} {{ $planningDay?->driver?->last_name ?? $group->driver?->last_name ?? '' }} (conductor)" disabled class="w-full border-0 bg-transparent py-2 text-sm text-slate-600">
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 text-xs font-bold text-sky-700">Nuevo Personal</div>
                            <div class="flex items-center rounded-md border border-slate-300 bg-white">
                                <span class="px-3 text-sky-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z" />
                                    </svg>
                                </span>
                                <select name="driver_id" class="w-full border-0 bg-transparent py-2 text-sm text-slate-700 focus:ring-0">
                                    <option value="">Seleccione un nuevo personal</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}" @selected(($planningDay?->driver?->id ?? $group->driver?->id) === $driver->id)>{{ $driver->name }} {{ $driver->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    @forelse ($currentHelpers as $helper)
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <div class="mb-1 text-xs font-bold text-sky-700">Personal Actual</div>
                                <div class="flex items-center rounded-md border border-slate-300 bg-slate-100">
                                    <span class="px-3 text-sky-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z" />
                                        </svg>
                                    </span>
                                    <input type="text" value="{{ $helper->name }} {{ $helper->last_name }} (ayudante)" disabled class="w-full border-0 bg-transparent py-2 text-sm text-slate-600">
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-bold text-sky-700">Nuevo Personal</div>
                                <div class="flex items-center rounded-md border border-slate-300 bg-white">
                                    <span class="px-3 text-sky-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 5v1h16v-1c0-2.83-3.67-5-8-5Z" />
                                        </svg>
                                    </span>
                                    <select name="helper_ids[]" class="w-full border-0 bg-transparent py-2 text-sm text-slate-700 focus:ring-0">
                                        <option value="">Seleccione un nuevo personal</option>
                                        @foreach ($helpersPool as $helperOption)
                                            <option value="{{ $helperOption->id }}" @selected($helper->id === $helperOption->id)>{{ $helperOption->name }} {{ $helperOption->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-md border border-dashed border-slate-300 p-4 text-sm text-slate-500">
                            Sin ayudantes asignados para esta programacion.
                        </div>
                    @endforelse
                </div>
            </section>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.history.back()"
                        class="inline-flex items-center rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">
                    Cerrar
                </button>
                <button type="submit"
                        class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</turbo-frame>
