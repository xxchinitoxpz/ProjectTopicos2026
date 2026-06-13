<x-app-layout>
    <x-slot:title>
        Programación de Grupos
    </x-slot:title>

    <div class="space-y-6" x-data="{ bulkModalOpen: false }">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Programación de Grupos</h3>
                <p class="text-xs text-gray-400">Planifica y programa los ciclos de trabajo de los grupos de recolección.</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Bulk Scheduling Trigger -->
                <button @click="bulkModalOpen = true"
                        class="inline-flex items-center px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-usat-blue text-sm font-semibold rounded-xl transition border border-blue-100">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Programación Masiva
                </button>
                <!-- Individual Scheduling Trigger -->
                <a href="{{ route('admin.planning.create') }}" data-turbo-frame="modal" 
                   class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Programación
                </a>
            </div>
        </div>

        <!-- Filters & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search & Pagination Filter -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                <form action="{{ route('admin.planning.index') }}" method="GET" class="flex flex-1 flex-col sm:flex-row gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por grupo, conductor o ayudante..." class="flex-1 min-w-[200px] text-sm" />
                    
                    <select name="state" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ $stateFilter === 'active' ? 'selected' : '' }}>Activa</option>
                        <option value="finished" {{ $stateFilter === 'finished' ? 'selected' : '' }}>Finalizada</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Desde:</span>
                        <x-text-input type="date" name="date_start" value="{{ $dateStart }}" class="text-sm py-1" />
                        <span class="text-xs text-gray-400">Hasta:</span>
                        <x-text-input type="date" name="date_end" value="{{ $dateEnd }}" class="text-sm py-1" />
                    </div>
                    
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Filtrar
                    </button>
                    @if($search || $stateFilter || $dateStart || $dateEnd)
                        <a href="{{ route('admin.planning.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center justify-center">
                            Limpiar
                        </a>
                    @endif
                </form>
                <form action="{{ route('admin.planning.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500 xl:justify-end">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="state" value="{{ $stateFilter }}">
                    <input type="hidden" name="date_start" value="{{ $dateStart }}">
                    <input type="hidden" name="date_end" value="{{ $dateEnd }}">
                    <span>Mostrar</span>
                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Grupo de Personal</th>
                            <th class="py-3.5 px-6">Conductor</th>
                            <th class="py-3.5 px-6">Ayudantes</th>
                            <th class="py-3.5 px-6">Período de Programación</th>
                            <th class="py-3.5 px-6">Días Programados</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center" width="160">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($plannings as $planning)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-gray-900 leading-tight">
                                        {{ $planning->staffGroup?->name ?? 'Grupo Eliminado' }}
                                    </div>
                                    <div class="text-[10px] text-gray-450 mt-1 flex items-center space-x-1.5">
                                        <span class="px-1.5 py-0.5 bg-blue-50 text-usat-blue font-bold rounded">
                                            ZONA: {{ $planning->staffGroup?->zone?->name ?? 'N/A' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded">
                                            PLACA: {{ $planning->staffGroup?->vehicle?->plate ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-gray-800">
                                    @if($planning->driver)
                                        <div class="flex items-center space-x-2">
                                            <img src="{{ $planning->driver->photo_url }}" class="w-7 h-7 object-cover rounded-full border border-gray-200" alt="">
                                            <span>{{ $planning->driver->name }} {{ $planning->driver->last_name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">No asignado</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-xs">
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @forelse($planning->helpers as $helper)
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-750 font-medium rounded-md border border-gray-200" title="{{ $helper->name }} {{ $helper->last_name }}">
                                                {{ $helper->name }} {{ substr($helper->last_name, 0, 1) }}.
                                            </span>
                                        @empty
                                            <span class="text-gray-400 italic">Sin ayudantes</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs font-semibold text-gray-750 flex items-center">
                                        <svg class="w-3.5 h-3.5 text-gray-400 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $planning->date_start->format('d/m/Y') }} - {{ $planning->date_end->format('d/m/Y') }}
                                    </div>
                                    <div class="text-[9px] text-gray-400 mt-0.5">
                                        Duración: {{ $planning->date_start->diffInDays($planning->date_end) + 1 }} días
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex flex-wrap gap-1 max-w-[180px]">
                                        @foreach($planning->days as $day)
                                            <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded capitalize">
                                                {{ substr($day, 0, 2) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($planning->state === 'active')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">ACTIVA</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-full border border-gray-200">FINALIZADA</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <!-- Finalize Planning -->
                                        @if($planning->state === 'active')
                                            <form action="{{ route('admin.planning.finish', $planning->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de finalizar esta programación?');" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg transition" title="Finalizar Programación">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.planning.edit', $planning->id) }}" data-turbo-frame="modal" class="p-1.5 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- History Modal Trigger -->
                                        <a href="{{ route('admin.planning.history', $planning->id) }}" data-turbo-frame="modal" class="p-1.5 bg-blue-50 text-usat-blue hover:bg-blue-100 rounded-lg transition" title="Historial de Cambios">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.planning.destroy', $planning->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta programación? Esta acción es irreversible.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    No se encontraron programaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($plannings->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $plannings->links() }}
                </div>
            @endif
        </div>

        <!-- Bulk Scheduling Modal (Alpine.js controlled) -->
        <div x-show="bulkModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <!-- Backdrop -->
                <div @click="bulkModalOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

                <!-- Modal Content -->
                <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 max-w-2xl w-full p-6 sm:p-8 overflow-hidden z-10 transition-all">
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-usat-blue">Programación Masiva</h3>
                            <p class="text-xs text-gray-400">Crea programaciones para múltiples grupos utilizando sus datos predeterminados.</p>
                        </div>
                        <button type="button" @click="bulkModalOpen = false" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('admin.planning.bulk') }}" method="POST" class="mt-6 space-y-6">
                        @csrf

                        <!-- Dates Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="bulk_date_start" :value="__('Fecha de Inicio')" />
                                <x-text-input id="bulk_date_start" name="date_start" type="date" value="{{ date('Y-m-d') }}" class="block mt-1 w-full text-sm" required />
                            </div>

                            <div>
                                <x-input-label for="bulk_date_end" :value="__('Fecha de Fin')" />
                                <x-text-input id="bulk_date_end" name="date_end" type="date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="block mt-1 w-full text-sm" required />
                            </div>
                        </div>

                        <!-- Groups Selection List -->
                        <div class="space-y-2" x-data="{
                            selected: [],
                            allIds: [{{ $groups->pluck('id')->join(', ') }}],
                            toggleAll() {
                                if (this.selected.length === this.allIds.length) {
                                    this.selected = [];
                                } else {
                                    this.selected = [...this.allIds];
                                }
                            }
                        }">
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('Seleccionar Grupos de Personal')" />
                                <button type="button" @click="toggleAll()" class="text-xs font-bold text-usat-blue hover:underline">
                                    Seleccionar / Deseleccionar Todo
                                </button>
                            </div>
                            
                            <div class="border border-gray-200 rounded-xl max-h-56 overflow-y-auto divide-y divide-gray-100 p-2 bg-gray-50/50">
                                @forelse($groups as $group)
                                    <label class="flex items-center py-2 px-3 hover:bg-white rounded-lg transition cursor-pointer select-none">
                                        <input type="checkbox" name="group_ids[]" value="{{ $group->id }}" x-model="selected"
                                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 me-3">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ $group->name }}</div>
                                            <div class="text-[10px] text-gray-400">
                                                Conductor: {{ $group->driver?->name }} | Días: {{ implode(', ', $group->days) }}
                                            </div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-6 text-gray-400 text-sm">
                                        No hay grupos de personal activos disponibles.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="bulkModalOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                                Generar Programaciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js global scheduling component definition -->
    <script>
        window.planningForm = function(groupsJson, planningJson = null, helpersArray = []) {
            return {
                groups: groupsJson,
                planningId: planningJson ? planningJson.id : null,
                staffGroupId: planningJson ? planningJson.staff_group_id : '',
                dateStart: planningJson ? planningJson.date_start.substring(0, 10) : '{{ date("Y-m-d") }}',
                dateEnd: planningJson ? planningJson.date_end.substring(0, 10) : '{{ date("Y-m-d", strtotime("+14 days")) }}',
                driverId: planningJson ? planningJson.driver_id : '',
                helpers: helpersArray,
                days: planningJson ? [...planningJson.days] : [],
                
                isValidated: false,
                isValid: false,
                isValidating: false,
                errors: [],
                suggestions: [],
                successMessage: '',

                onGroupChange() {
                    this.isValidated = false;
                    this.isValid = false;
                    const group = this.groups.find(g => g.id == this.staffGroupId);
                    if (group) {
                        this.driverId = group.driver_id || '';
                        this.days = group.days ? [...group.days] : [];
                        this.helpers = group.helpers ? group.helpers.map(h => h.id) : [];
                    } else {
                        this.driverId = '';
                        this.days = [];
                        this.helpers = [];
                    }
                },

                onFieldChange() {
                    this.isValidated = false;
                    this.isValid = false;
                },

                async validateAvailability() {
                    if (!this.dateStart || !this.dateEnd || !this.staffGroupId || !this.driverId || this.days.length === 0) {
                        alert('Por favor complete todos los campos requeridos (Fechas, Conductor y al menos un Día).');
                        return;
                    }

                    this.isValidating = true;
                    this.errors = [];
                    this.suggestions = [];

                    try {
                        const response = await fetch('{{ route("admin.planning.validate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                date_start: this.dateStart,
                                date_end: this.dateEnd,
                                staff_group_id: this.staffGroupId,
                                driver_id: this.driverId,
                                helpers: this.helpers,
                                days: this.days,
                                planning_id: this.planningId
                            })
                        });

                        const data = await response.json();
                        this.isValidated = true;
                        this.isValid = data.valid;
                        this.errors = data.errors || [];
                        this.suggestions = data.suggestions || [];
                        if (this.isValid) {
                            this.successMessage = '¡Disponibilidad validada con éxito! Todos los miembros y el vehículo están disponibles.';
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Ocurrió un error al validar la disponibilidad.');
                    } finally {
                        this.isValidating = false;
                    }
                },

                applySuggestion(sug) {
                    if (sug.type === 'driver') {
                        this.driverId = sug.suggested_staff_id;
                    } else if (sug.type === 'helper') {
                        this.helpers = this.helpers.filter(id => id != sug.conflicting_staff_id);
                        if (!this.helpers.includes(sug.suggested_staff_id)) {
                            this.helpers.push(sug.suggested_staff_id);
                        }
                    }
                    this.validateAvailability();
                }
            };
        }
    </script>
</x-app-layout>
