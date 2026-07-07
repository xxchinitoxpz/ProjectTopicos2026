<x-app-layout>
    <x-slot:title>
        Nueva Programación
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-4xl">
        <div data-modal-size="max-w-4xl" class="max-w-4xl mx-auto space-y-6 p-6 sm:p-8" x-data="planningForm({{ $groups->toJson() }})">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Registrar Programación</h3>
                    <p class="text-xs text-gray-400">Planifica un nuevo período de trabajo para un grupo de personal.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.planning.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.planning.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Two Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Column Left: Dates, Group and Driver selection -->
                    <div class="space-y-5">
                        <!-- Dates Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="date_start" :value="__('Fecha de inicio *')" />
                                <x-text-input id="date_start" name="date_start" type="date" value="{{ date('Y-m-d') }}" x-model="dateStart" @change="onFieldChange()" class="block mt-1 w-full text-sm" required />
                                <x-input-error :messages="$errors->get('date_start')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="date_end" :value="__('Fecha de fin *')" />
                                <x-text-input id="date_end" name="date_end" type="date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" x-model="dateEnd" @change="onFieldChange()" class="block mt-1 w-full text-sm" required />
                                <x-input-error :messages="$errors->get('date_end')" class="mt-1" />
                            </div>
                        </div>

                        <!-- Validate Button Row -->
                        <div>
                            <button type="button" @click="validateAvailability()" :disabled="isValidating"
                                    class="w-full flex items-center justify-center px-4 py-2.5 bg-blue-50 border border-blue-200 text-usat-blue hover:bg-blue-100 disabled:opacity-50 text-sm font-bold rounded-xl transition">
                                <svg x-show="isValidating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-usat-blue" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg x-show="!isValidating" class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Validar disponibilidad
                            </button>
                        </div>

                        <!-- Staff Group Select -->
                        <div>
                            <x-input-label for="staff_group_id" :value="__('Grupo de Personal *')" />
                            <select id="staff_group_id" name="staff_group_id" x-model="staffGroupId" @change="onGroupChange()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                                <option value="">Seleccione un grupo...</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('staff_group_id')" class="mt-1" />
                            <p class="text-[10px] text-gray-400 mt-1">Busque por nombre, zona o turno</p>
                        </div>

                        <!-- Driver Select (Conductor) -->
                        <div>
                            <x-input-label for="driver_id" :value="__('Conductor Principal *')" />
                            <select id="driver_id" name="driver_id" x-model="driverId" @change="onFieldChange()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                                <option value="">Seleccione conductor...</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} {{ $driver->last_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('driver_id')" class="mt-1" />
                        </div>

                        <!-- Days Checklist -->
                        <div>
                            <x-input-label :value="__('Días de Trabajo *')" />
                            <div class="grid grid-cols-2 gap-2 mt-1 bg-gray-55 p-3 rounded-xl border border-gray-200">
                                @foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day)
                                    <label class="flex items-center text-xs font-semibold text-gray-700 capitalize cursor-pointer select-none">
                                        <input type="checkbox" name="days[]" value="{{ $day }}" x-model="days" @change="onFieldChange()"
                                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 me-2">
                                        {{ $day }}
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('days')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Column Right: Helpers Checklist -->
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('Ayudantes del Grupo')" />
                                <span class="text-[10px] text-gray-450 font-bold" x-text="helpers.length + ' seleccionado(s)'"></span>
                            </div>
                            <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/50 max-h-96 overflow-y-auto space-y-2 mt-1">
                                @forelse($helpers as $helper)
                                    <label class="flex items-center p-2 hover:bg-white rounded-lg transition cursor-pointer select-none">
                                        <input type="checkbox" name="helpers[]" value="{{ $helper->id }}" x-model="helpers" @change="onFieldChange()"
                                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 me-3">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ $helper->name }} {{ $helper->last_name }}</div>
                                            <div class="text-[10px] text-gray-400">Cargo: {{ $helper->staffType?->name }} | DNI: {{ $helper->dni }}</div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-6 text-gray-400 text-sm">
                                        No hay ayudantes activos registrados.
                                    </div>
                                @endforelse
                            </div>
                            <x-input-error :messages="$errors->get('helpers')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Alert Banners based on Availability Check -->
                <div class="space-y-3">
                    <!-- Failure alert: Inconsistencias -->
                    <div x-show="isValidated && !isValid" style="display: none;" 
                         class="p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm text-red-800 space-y-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 me-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-bold text-red-900">Hay errores que corregir</h4>
                                <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                                    <template x-for="err in errors" :key="err">
                                        <li x-text="err"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <!-- Suggestions -->
                        <div x-show="suggestions.length > 0" class="pt-3 border-t border-red-200">
                            <h5 class="text-xs font-bold text-red-900 flex items-center mb-2">
                                <svg class="w-4 h-4 me-1.5 text-red-650" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Sugerencias de Reemplazo:
                            </h5>
                            <div class="space-y-1.5 max-h-36 overflow-y-auto">
                                <template x-for="sug in suggestions" :key="sug.suggested_staff_id">
                                    <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-red-100 text-xs shadow-xs">
                                        <div>
                                            Reemplazar a <strong x-text="sug.conflicting_staff_name"></strong> con <strong class="text-emerald-700" x-text="sug.suggested_staff_name"></strong>
                                        </div>
                                        <button type="button" @click="applySuggestion(sug)"
                                                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-bold rounded-lg transition duration-150">
                                            Reemplazar
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Success alert: Listo para guardar -->
                    <div x-show="isValidated && isValid" style="display: none;" 
                         class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm text-emerald-800 flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-emerald-500 me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-semibold" x-text="successMessage"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.planning.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="!isValidated || !isValid" 
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10 disabled:opacity-50 disabled:cursor-not-allowed">
                        Guardar Programación
                    </button>
                </div>
            </form>

            <!-- Alpine.js dynamic script inside turbo-frame -->
            <script>
                window.planningForm = function(groupsJson) {
                    return {
                        groups: groupsJson,
                        staffGroupId: '',
                        dateStart: '{{ date("Y-m-d") }}',
                        dateEnd: '{{ date("Y-m-d", strtotime("+14 days")) }}',
                        driverId: '',
                        helpers: [],
                        days: [],
                        
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
                                alert('Por favor complete todos los campos requeridos (Fechas, Grupo, Conductor y al menos un Día).');
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
                                        days: this.days
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
        </div>
    </turbo-frame>
</x-app-layout>
