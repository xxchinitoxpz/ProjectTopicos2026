<x-app-layout>
    <x-slot:title>
        Detalles del Cambio
    </x-slot:title>

    @php
        $typeLabels = [
            'turno' => 'Turno',
            'vehiculo' => 'Vehiculo',
            'conductor' => 'Conductor',
            'helper' => 'Ayudante',
            'creacion' => 'Creacion',
            'actualizacion' => 'Actualizacion',
            'finalizacion' => 'Finalizacion',
        ];

        $typeLabel = $typeLabels[$change->change_type] ?? ucfirst(str_replace('_', ' ', $change->change_type));
        $planningDay = $change->planningDay;
        $shift = $planningDay?->shift;
        $oldValue = $change->old_value ?: '-';
        $newValue = $change->new_value ?: '-';

        $timeRows = [];
        if ($change->change_type === 'turno') {
            $oldShift = \App\Models\Shift::query()->where('name', $change->old_value)->first();
            $newShift = \App\Models\Shift::query()->where('name', $change->new_value)->first();

            $timeRows = [
                [
                    'label' => 'Hora de Entrada',
                    'old' => $oldShift?->check_in ? substr($oldShift->check_in, 0, 5) : '-',
                    'new' => $newShift?->check_in ? substr($newShift->check_in, 0, 5) : '-',
                ],
                [
                    'label' => 'Hora de Salida',
                    'old' => $oldShift?->check_out ? substr($oldShift->check_out, 0, 5) : '-',
                    'new' => $newShift?->check_out ? substr($newShift->check_out, 0, 5) : '-',
                ],
            ];
        }

        $programmingNumber = $planningDay?->id ? '#' . $planningDay->id : '-';
    @endphp

    <turbo-frame id="modal" data-modal-size="max-w-3xl">
        <div data-modal-size="max-w-3xl" class="mx-auto w-full max-w-3xl overflow-hidden rounded-sm bg-white shadow-2xl">
            <div class="bg-slate-100 px-6 py-5">
                <div class="rounded-sm bg-blue-900 px-5 py-4 text-white shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 text-2xl font-bold">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V8m0 0l-4 4m4-4l4 4m10-4H9m8 8H9" />
                                </svg>
                                Detalles del Cambio #{{ $change->id }}
                            </div>
                            <div class="mt-2 flex items-center gap-2 text-sm text-blue-100">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6 2a1 1 0 012 0v1h4V2a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h1V2Zm9 6H5v7h10V8Z" />
                                </svg>
                                {{ $change->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-amber-400 px-4 py-2 text-sm font-bold text-blue-950">
                                {{ $typeLabel }}
                            </span>
                            <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.change.index') }}'" class="text-white/80 transition hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6 px-6 pb-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-100">
                        <div class="bg-red-500 px-5 py-4 text-white">
                            <div class="text-lg font-bold">Valores Anteriores</div>
                            <div class="text-xs font-semibold text-red-50">Estado previo al cambio</div>
                        </div>
                        <div class="divide-y divide-gray-100 p-5">
                            <div class="py-3">
                                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $typeLabel }}</div>
                                <div class="mt-1 text-base font-bold text-gray-800">{{ $oldValue }}</div>
                            </div>
                            @foreach($timeRows as $row)
                                <div class="py-3">
                                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $row['label'] }}</div>
                                    <div class="mt-1 text-base font-bold text-gray-800">{{ $row['old'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-100">
                        <div class="bg-green-500 px-5 py-4 text-white">
                            <div class="text-lg font-bold">Valores Nuevos</div>
                            <div class="text-xs font-semibold text-green-50">Estado despues del cambio</div>
                        </div>
                        <div class="divide-y divide-gray-100 p-5">
                            <div class="py-3">
                                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $typeLabel }}</div>
                                <div class="mt-1 text-base font-bold text-green-600">{{ $newValue }}</div>
                            </div>
                            @foreach($timeRows as $row)
                                <div class="py-3">
                                    <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $row['label'] }}</div>
                                    <div class="mt-1 text-base font-bold text-green-600">{{ $row['new'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-100">
                    <div class="bg-cyan-600 px-5 py-4 text-white">
                        <div class="text-lg font-bold">Informacion del Cambio</div>
                        <div class="text-xs font-semibold text-cyan-50">Detalles adicionales del proceso</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 p-5 md:grid-cols-4">
                        <div class="rounded-lg bg-gray-50 px-4 py-5 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-sky-600 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V8m0 0l-4 4m4-4l4 4m10-4H9m8 8H9" />
                                </svg>
                            </div>
                            <div class="text-[11px] font-bold uppercase text-gray-500">Tipo de Cambio</div>
                            <div class="mt-2 inline-flex rounded-full bg-amber-400 px-3 py-1 text-xs font-bold text-blue-950">{{ $typeLabel }}</div>
                        </div>

                        <div class="rounded-lg bg-gray-50 px-4 py-5 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-900 text-white">
                                <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6Zm-7 8a7 7 0 1114 0H3Z" />
                                </svg>
                            </div>
                            <div class="text-[11px] font-bold uppercase text-gray-500">Realizado Por</div>
                            <div class="mt-2 text-sm font-bold text-gray-800">{{ $change->user?->name ?? 'Sistema' }}</div>
                        </div>

                        <div class="rounded-lg bg-gray-50 px-4 py-5 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-400 text-blue-950">
                                <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6 2a1 1 0 012 0v1h4V2a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h1V2Zm9 6H5v7h10V8Z" />
                                </svg>
                            </div>
                            <div class="text-[11px] font-bold uppercase text-gray-500">Fecha del Cambio</div>
                            <div class="mt-2 text-sm font-bold text-gray-800">{{ $change->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>

                        <div class="rounded-lg bg-gray-50 px-4 py-5 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-green-500 text-white">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h11M9 12h11M9 19h11M5 5h.01M5 12h.01M5 19h.01" />
                                </svg>
                            </div>
                            <div class="text-[11px] font-bold uppercase text-gray-500">Programacion</div>
                            <div class="mt-2 text-sm font-bold text-gray-800">{{ $programmingNumber }}</div>
                        </div>
                    </div>

                    <div class="mx-5 mb-5 rounded-lg bg-cyan-50 p-4">
                        <div class="text-sm font-bold text-cyan-800">Motivo del Cambio</div>
                        <div class="mt-2 text-sm font-semibold text-gray-800">{{ $change->reason_type ?: '-' }}</div>
                        <div class="mt-2 whitespace-pre-line text-sm text-gray-600">{{ $change->details ?: 'Sin detalle adicional' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </turbo-frame>
</x-app-layout>
