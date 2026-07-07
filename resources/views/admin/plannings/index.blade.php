<x-app-layout>
    <x-slot:title>
        Programación de Grupos
    </x-slot:title>

    <div class="space-y-6" x-data="{ bulkModalOpen: {{ request()->boolean('open_bulk') ? 'true' : 'false' }} }">
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
                            <th class="py-3.5 px-6">Fecha Programada</th>
                            <th class="py-3.5 px-6">Día Programado</th>
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
                                        {{ $planning->date->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md border border-emerald-100 capitalize">
                                        {{ $planning->date->locale('es')->dayName }}
                                    </span>
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
        <div x-show="bulkModalOpen" x-data="bulkPlanningForm({{ $groups->toJson() }}, {{ $drivers->toJson() }}, {{ $helpers->toJson() }})" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <!-- Backdrop -->
                <div @click="bulkModalOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

                <!-- Modal Content -->
                <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 max-w-6xl w-full p-6 sm:p-8 overflow-hidden z-10 transition-all">
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-usat-blue">Programación Masiva</h3>
                            <p class="text-xs text-gray-400">Crea programaciones para múltiples grupos con asignaciones personalizadas y exclusión de feriados.</p>
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
                        
                        <!-- Hidden parameters representing Alpine state -->
                        <input type="hidden" name="group_assignments" :value="JSON.stringify(groupAssignments)">
                        <template x-for="groupId in selectedGroups">
                            <input type="hidden" name="group_ids[]" :value="groupId">
                        </template>
                        <template x-for="holDate in excludedHolidays">
                            <input type="hidden" name="excluded_holidays[]" :value="holDate">
                        </template>

                        <!-- Date Range and Validation Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end bg-gray-55 p-4 rounded-xl border border-gray-200">
                            <div>
                                <x-input-label for="bulk_date_start" :value="__('Fecha de Inicio *')" />
                                <x-text-input id="bulk_date_start" name="date_start" type="date" value="{{ date('Y-m-d') }}" x-model="dateStart" @change="onFieldChange()" class="block mt-1 w-full text-xs font-semibold" required />
                            </div>

                            <div>
                                <x-input-label for="bulk_date_end" :value="__('Fecha de Fin *')" />
                                <x-text-input id="bulk_date_end" name="date_end" type="date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" x-model="dateEnd" @change="onFieldChange()" class="block mt-1 w-full text-xs font-semibold" required />
                            </div>

                            <div>
                                <button type="button" @click="validateAvailability()" :disabled="isValidating"
                                        class="w-full flex items-center justify-center px-4 py-2.5 bg-blue-50 border border-blue-200 text-usat-blue hover:bg-blue-100 disabled:opacity-50 text-xs font-bold rounded-xl transition">
                                    <svg x-show="isValidating" class="animate-spin -ml-1 mr-3 h-4 w-4 text-usat-blue" fill="none" viewBox="0 0 24 24" style="display: none;">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Validar disponibilidad
                                </button>
                            </div>
                        </div>

                        <!-- Turn Filtering & Holidays Panel -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Left: Shift buttons -->
                            <div class="md:col-span-1 space-y-2">
                                <x-input-label :value="__('Filtrar por Turno')" />
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="t in ['all', 'Mañana', 'Tarde', 'Noche']">
                                        <button type="button" 
                                                @click="shiftFilter = t"
                                                :class="shiftFilter === t ? 'bg-usat-blue text-white border-usat-blue' : 'bg-gray-100 text-gray-700 hover:bg-gray-250 border-gray-200'"
                                                class="px-3 py-1.5 text-xs font-bold rounded-lg transition border"
                                                x-text="t === 'all' ? 'Todos los Turnos' : t">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Right: Holidays selection checklist -->
                            <div class="md:col-span-2 space-y-2">
                                <x-input-label :value="__('Días Feriados en el Rango Seleccionado')" />
                                <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/50 min-h-[50px] max-h-[85px] overflow-y-auto">
                                    <template x-if="holidays.length === 0">
                                        <p class="text-[10px] text-gray-400 italic">No se detectaron feriados en este rango de fechas.</p>
                                    </template>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <template x-for="hol in holidays" :key="hol.date">
                                            <label class="flex items-center text-[10px] font-semibold text-gray-700 cursor-pointer select-none">
                                                <input type="checkbox" :value="hol.date" x-model="excludedHolidays" @change="onFieldChange()"
                                                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 me-2 w-3.5 h-3.5">
                                                <span x-text="hol.formatted_date + ' (' + hol.description + ')'"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                                <p class="text-[9px] text-blue-600 font-medium">ℹ Los feriados seleccionados NO serán programados, incluso si el grupo trabaja ese día.</p>
                            </div>
                        </div>

                        <!-- Bulk Days Apply to All Selected Groups -->
                        <div class="space-y-2 bg-purple-50/50 p-4 rounded-xl border border-purple-100">
                            <x-input-label :value="__('Aplicar días de trabajo a todos los grupos seleccionados')" />
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="day in weekDays" :key="day">
                                        <label class="flex items-center text-[10px] font-semibold text-gray-700 capitalize cursor-pointer select-none px-2 py-1 bg-white rounded-lg border border-gray-200">
                                            <input type="checkbox" :value="day" x-model="globalDays"
                                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 me-1.5 w-3.5 h-3.5">
                                            <span x-text="day"></span>
                                        </label>
                                    </template>
                                </div>
                                <button type="button" @click="applyGlobalDays()"
                                        :disabled="globalDays.length === 0 || selectedGroups.length === 0"
                                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white text-[10px] font-bold rounded-lg transition whitespace-nowrap">
                                    Aplicar a seleccionados
                                </button>
                            </div>
                            <p class="text-[9px] text-purple-700 font-medium">Selecciona los días y aplícalos en bloque, o modifica los días de cada grupo individualmente abajo.</p>
                        </div>

                        <!-- Work Groups Customization Cards Grid -->
                        <div class="space-y-3">
                            <x-input-label :value="__('Grupos de Trabajo a Programar')" />
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-h-96 overflow-y-auto p-3 bg-gray-55 rounded-xl border border-gray-200">
                                <template x-for="group in filteredGroups()" :key="group.id">
                                    <div class="relative bg-white p-4 rounded-xl border border-gray-200 shadow-xs space-y-3 transition"
                                         :class="!selectedGroups.includes(group.id) ? 'opacity-40 border-dashed bg-gray-50' : 'border-solid bg-white'">
                                        
                                        <!-- Header card details -->
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="text-xs font-bold text-gray-900" x-text="group.name"></div>
                                                <div class="text-[9px] text-gray-400 font-semibold mt-0.5" x-text="'Zona: ' + (group.zone ? group.zone.name : 'N/A')"></div>
                                                <div class="text-[9px] text-gray-400 font-semibold" x-text="'Turno: ' + (group.shift ? group.shift.name : 'N/A')"></div>
                                            </div>
                                            <div class="flex items-center space-x-1.5">
                                                <!-- Include/Exclude Toggle -->
                                                <input type="checkbox" :value="group.id" x-model="selectedGroups" @change="onGroupSelectToggle(group.id)"
                                                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                                <button type="button" @click="removeGroup(group.id)" class="text-red-500 hover:text-red-700 transition" title="Remover de lista">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="text-[9px] flex flex-wrap gap-1">
                                            <span class="px-1.5 py-0.5 bg-blue-50 text-usat-blue font-bold rounded" x-text="'Placa: ' + (group.vehicle ? group.vehicle.plate : 'N/A')"></span>
                                        </div>

                                        <!-- Days checkboxes per group -->
                                        <div class="space-y-1">
                                            <span class="text-[9px] font-bold text-gray-500">Días de Trabajo:</span>
                                            <div class="grid grid-cols-2 gap-1">
                                                <template x-for="day in weekDays" :key="day">
                                                    <label class="flex items-center text-[9px] font-semibold text-gray-700 capitalize cursor-pointer select-none">
                                                        <input type="checkbox" :value="day"
                                                               x-model="groupAssignments[group.id].days"
                                                               @change="onFieldChange()"
                                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 me-1 w-3 h-3">
                                                        <span x-text="day"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Driver dropdown -->
                                        <div class="space-y-1">
                                            <span class="text-[9px] font-bold text-gray-500">Conductor Principal:</span>
                                            <select x-model="groupAssignments[group.id].driver_id" @change="onFieldChange()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-[10px] py-1">
                                                <option value="">Seleccione conductor...</option>
                                                <template x-for="driver in allDrivers" :key="driver.id">
                                                    <option :value="driver.id" x-text="driver.name + ' ' + driver.last_name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Helpers dropdowns -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-bold text-gray-500">Ayudante 1:</span>
                                                <select x-model="groupAssignments[group.id].helpers[0]" @change="onFieldChange()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-[10px] py-1">
                                                    <option value="">Ninguno...</option>
                                                    <template x-for="helper in allHelpers" :key="helper.id">
                                                        <option :value="helper.id" x-text="helper.name + ' ' + helper.last_name" :disabled="groupAssignments[group.id].helpers[1] == helper.id"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-bold text-gray-500">Ayudante 2:</span>
                                                <select x-model="groupAssignments[group.id].helpers[1]" @change="onFieldChange()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-[10px] py-1">
                                                    <option value="">Ninguno...</option>
                                                    <template x-for="helper in allHelpers" :key="helper.id">
                                                        <option :value="helper.id" x-text="helper.name + ' ' + helper.last_name" :disabled="groupAssignments[group.id].helpers[0] == helper.id"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- General Validation Alerts -->
                        <div x-show="isValidated" style="display: none;" class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3">
                            <h4 class="text-sm font-bold text-gray-900 flex items-center">
                                <svg class="w-4 h-4 me-1.5 text-usat-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Resultado de Validación General
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-60 overflow-y-auto">
                                <template x-for="groupId in selectedGroups" :key="groupId">
                                    <div x-show="validationResults[groupId]" class="p-3 bg-white border border-gray-200 rounded-lg shadow-xs space-y-2">
                                        <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                                            <span class="text-xs font-bold text-gray-900" x-text="validationResults[groupId].group_name"></span>
                                            <template x-if="validationResults[groupId].valid">
                                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[9px] border border-emerald-100">DISPONIBLE</span>
                                            </template>
                                            <template x-if="!validationResults[groupId].valid">
                                                <span class="px-1.5 py-0.5 bg-red-50 text-red-650 font-bold rounded text-[9px] border border-red-100">CON ERRORES</span>
                                            </template>
                                        </div>

                                        <!-- Errors -->
                                        <template x-if="validationResults[groupId].errors && validationResults[groupId].errors.length > 0">
                                            <div class="text-red-800 space-y-1">
                                                <div class="text-[9px] font-bold uppercase">Errores:</div>
                                                <ul class="list-disc list-inside text-[9px] space-y-0.5">
                                                    <template x-for="err in validationResults[groupId].errors">
                                                        <li x-text="err"></li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>

                                        <!-- Warnings -->
                                        <template x-if="validationResults[groupId].warnings && validationResults[groupId].warnings.length > 0">
                                            <div class="text-amber-800 space-y-1">
                                                <div class="text-[9px] font-bold uppercase">Advertencias / Info:</div>
                                                <ul class="list-disc list-inside text-[9px] space-y-0.5">
                                                    <template x-for="war in validationResults[groupId].warnings">
                                                        <li x-text="war"></li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>

                                        <!-- Suggestions -->
                                        <template x-if="validationResults[groupId].suggestions && validationResults[groupId].suggestions.length > 0">
                                            <div class="pt-2 border-t border-gray-100 space-y-1">
                                                <div class="text-[9px] text-gray-500 font-bold uppercase">Sugerencias de Reemplazo:</div>
                                                <div class="space-y-1">
                                                    <template x-for="sug in validationResults[groupId].suggestions" :key="sug.suggested_staff_id">
                                                        <div class="flex items-center justify-between bg-gray-50 p-1.5 rounded border border-gray-200 text-[9px]">
                                                            <span>Reemplazar a <strong x-text="sug.conflicting_staff_name"></strong> con <strong class="text-emerald-700" x-text="sug.suggested_staff_name"></strong></span>
                                                            <button type="button" @click="applySuggestion(groupId, sug)" class="px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 font-bold rounded transition text-[9px]">
                                                                Aplicar
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="bulkModalOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="!isValidated || !isValid" 
                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10 disabled:opacity-50 disabled:cursor-not-allowed">
                                Guardar Programaciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js global scheduling components definition -->
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
        };

        window.bulkPlanningForm = function(groupsJson, driversJson, helpersJson) {
            return {
                groups: groupsJson,
                allDrivers: driversJson,
                allHelpers: helpersJson,
                weekDays: ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'],

                dateStart: '{{ date("Y-m-d") }}',
                dateEnd: '{{ date("Y-m-d", strtotime("+14 days")) }}',
                
                holidays: [],
                excludedHolidays: [],
                shiftFilter: 'all',
                globalDays: [],
                
                selectedGroups: [],
                groupAssignments: {},

                isValidated: false,
                isValid: false,
                isValidating: false,
                validationResults: {},
                generalErrors: [],

                init() {
                    this.groups.forEach(g => {
                        this.groupAssignments[g.id] = {
                            driver_id: g.driver_id || '',
                            helpers: g.helpers ? g.helpers.map(h => h.id) : [],
                            days: g.days ? [...g.days] : []
                        };
                        this.validationResults[g.id] = {
                            group_name: g.name || 'Sin grupo',
                            valid: false,
                            errors: [],
                            warnings: [],
                            suggestions: []
                        };
                    });
                    this.selectedGroups = this.groups.map(g => g.id);

                    this.$watch('dateStart', () => this.fetchHolidays());
                    this.$watch('dateEnd', () => this.fetchHolidays());
                    this.fetchHolidays();
                },

                applyGlobalDays() {
                    if (this.globalDays.length === 0) return;
                    this.selectedGroups.forEach(groupId => {
                        if (this.groupAssignments[groupId]) {
                            this.groupAssignments[groupId].days = [...this.globalDays];
                        }
                    });
                    this.onFieldChange();
                },

                async fetchHolidays() {
                    if (!this.dateStart || !this.dateEnd) return;
                    try {
                        const url = `{{ route('admin.planning.get-holidays') }}?date_start=${this.dateStart}&date_end=${this.dateEnd}`;
                        const response = await fetch(url);
                        this.holidays = await response.json();
                        this.excludedHolidays = [];
                    } catch (err) {
                        console.error(err);
                    }
                },

                filteredGroups() {
                    if (this.shiftFilter === 'all') {
                        return this.groups;
                    }
                    return this.groups.filter(g => g.shift && g.shift.name.toLowerCase().includes(this.shiftFilter.toLowerCase()));
                },

                onGroupSelectToggle(groupId) {
                    this.isValidated = false;
                    this.isValid = false;
                },

                onFieldChange() {
                    this.isValidated = false;
                    this.isValid = false;
                },

                async validateAvailability() {
                    if (this.selectedGroups.length === 0) {
                        alert('Debe seleccionar al menos un grupo de trabajo.');
                        return;
                    }

                    this.isValidating = true;
                    this.generalErrors = [];
                    this.validationResults = {};

                    const groupsWithoutDays = this.selectedGroups.filter(groupId => {
                        const assign = this.groupAssignments[groupId];
                        return !assign || !assign.days || assign.days.length === 0;
                    });

                    if (groupsWithoutDays.length > 0) {
                        alert('Cada grupo seleccionado debe tener al menos un día de trabajo marcado.');
                        this.isValidating = false;
                        return;
                    }

                    const payloadGroups = this.selectedGroups.map(groupId => {
                        const assign = this.groupAssignments[groupId];
                        return {
                            staff_group_id: groupId,
                            driver_id: assign.driver_id,
                            helpers: assign.helpers,
                            days: assign.days
                        };
                    });

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
                                excluded_holidays: this.excludedHolidays,
                                groups: payloadGroups
                            })
                        });

                        const data = await response.json();
                        this.isValidated = true;
                        this.isValid = data.valid;
                        this.validationResults = data.results || {};
                    } catch (err) {
                        console.error(err);
                        alert('Ocurrió un error al validar la disponibilidad.');
                    } finally {
                        this.isValidating = false;
                    }
                },

                applySuggestion(groupId, sug) {
                    const assign = this.groupAssignments[groupId];
                    if (assign) {
                        if (sug.type === 'driver') {
                            assign.driver_id = sug.suggested_staff_id;
                        } else if (sug.type === 'helper') {
                            assign.helpers = assign.helpers.filter(id => id != sug.conflicting_staff_id);
                            if (!assign.helpers.includes(sug.suggested_staff_id)) {
                                assign.helpers.push(sug.suggested_staff_id);
                            }
                        }
                        this.validateAvailability();
                    }
                },

                removeGroup(groupId) {
                    this.selectedGroups = this.selectedGroups.filter(id => id != groupId);
                    this.isValidated = false;
                    this.isValid = false;
                }
            };
        };
    </script>
</x-app-layout>
