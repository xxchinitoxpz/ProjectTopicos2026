<x-app-layout>
    <x-slot:title>
        Modificar Programación
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-4xl">
        <div data-modal-size="max-w-4xl" class="max-w-4xl mx-auto space-y-6 p-6 sm:p-8" 
             x-data="{
                activeTab: 'turno',
                pendingChanges: [],
                
                // Form inputs
                newShiftId: '',
                newVehicleId: '',
                replacePerson: '', 
                newPersonId: '',
                motiveType: 'Imprevistos',
                motiveDetails: '',

                // Current values
                currentShiftId: '{{ $planning->shift_id }}',
                currentShiftName: '{{ $planning->shift?->name }}',
                currentVehicleId: '{{ $planning->vehicle_id }}',
                currentVehiclePlate: '{{ $planning->vehicle?->plate }}',
                currentDriverId: '{{ $planning->driver_id }}',
                currentDriverName: '{{ $planning->driver?->name }} {{ $planning->driver?->last_name }}',
                currentHelpers: [
                    @foreach($planning->helpers as $helper)
                        { id: '{{ $helper->id }}', name: '{{ $helper->name }} {{ $helper->last_name }}' },
                    @endforeach
                ],

                addChange() {
                    let type = '';
                    let newId = '';
                    let oldId = '';
                    let newName = '';
                    let oldName = '';

                    if (this.activeTab === 'turno') {
                        if (!this.newShiftId) {
                            alert('Seleccione un nuevo turno.');
                            return;
                        }
                        if (this.newShiftId == this.currentShiftId) {
                            alert('El turno seleccionado es el mismo que el actual.');
                            return;
                        }
                        type = 'turno';
                        newId = this.newShiftId;
                        oldId = this.currentShiftId;
                        oldName = this.currentShiftName;
                        
                        const selectEl = document.getElementById('new_shift_id');
                        newName = selectEl.options[selectEl.selectedIndex].text;

                    } else if (this.activeTab === 'vehiculo') {
                        if (!this.newVehicleId) {
                            alert('Seleccione un nuevo vehículo.');
                            return;
                        }
                        if (this.newVehicleId == this.currentVehicleId) {
                            alert('El vehículo seleccionado es el mismo que el actual.');
                            return;
                        }
                        type = 'vehiculo';
                        newId = this.newVehicleId;
                        oldId = this.currentVehicleId;
                        oldName = 'Vehículo ' + this.currentVehiclePlate;

                        const selectEl = document.getElementById('new_vehicle_id');
                        newName = selectEl.options[selectEl.selectedIndex].text;

                    } else if (this.activeTab === 'personal') {
                        if (!this.replacePerson) {
                            alert('Seleccione el personal actual a reemplazar.');
                            return;
                        }
                        if (!this.newPersonId) {
                            alert('Seleccione el nuevo empleado disponible.');
                            return;
                        }

                        if (this.replacePerson === 'conductor') {
                            if (this.newPersonId == this.currentDriverId) {
                                alert('El conductor seleccionado es el mismo que el actual.');
                                return;
                            }
                            type = 'conductor';
                            newId = this.newPersonId;
                            oldId = this.currentDriverId;
                            oldName = this.currentDriverName;
                        } else {
                            if (this.newPersonId == this.replacePerson) {
                                alert('El ayudante seleccionado es el mismo que el actual.');
                                return;
                            }
                            type = 'helper';
                            newId = this.newPersonId;
                            oldId = this.replacePerson;
                            const h = this.currentHelpers.find(ch => ch.id == this.replacePerson);
                            oldName = h ? h.name : 'Ayudante';
                        }

                        const selectEl = document.getElementById('new_person_id');
                        newName = selectEl.options[selectEl.selectedIndex].text;
                    }

                    if (!this.motiveDetails) {
                        alert('Debe describir el motivo del cambio.');
                        return;
                    }

                    // Check if duplicate change exists
                    this.pendingChanges = this.pendingChanges.filter(c => !(c.type === type && c.old_id == oldId));

                    this.pendingChanges.push({
                        type: type,
                        new_id: newId,
                        old_id: oldId,
                        new_name: newName,
                        old_name: oldName,
                        reason_type: this.motiveType,
                        details: this.motiveDetails
                    });

                    // Clear inputs
                    this.newShiftId = '';
                    this.newVehicleId = '';
                    this.replacePerson = '';
                    this.newPersonId = '';
                    this.motiveDetails = '';
                },

                removeChange(index) {
                    this.pendingChanges.splice(index, 1);
                }
             }">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Modificar Programación</h3>
                    <p class="text-xs text-gray-400">Actualiza la programación de un día específico. Se puede cambiar el turno, el vehículo o el personal.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.planning.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Tab Selectors -->
            <div class="flex border-b border-gray-200">
                <button type="button" 
                        @click="activeTab = 'turno'"
                        :class="activeTab === 'turno' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 text-center border-b-2 font-medium text-xs sm:text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Cambio de Turno
                </button>
                <button type="button" 
                        @click="activeTab = 'vehiculo'"
                        :class="activeTab === 'vehiculo' ? 'border-amber-500 text-amber-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 text-center border-b-2 font-medium text-xs sm:text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M21 16V10a2 2 0 00-2-2h-3V6a1 1 0 00-1-1H13v11"></path>
                    </svg>
                    Cambio de Vehículo
                </button>
                <button type="button" 
                        @click="activeTab = 'personal'"
                        :class="activeTab === 'personal' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 text-center border-b-2 font-medium text-xs sm:text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Cambio de Personal
                </button>
            </div>

            <!-- Main Tab Fields Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-4 sm:p-6 rounded-2xl border border-gray-150">
                <!-- Left: Form inputs for active tab -->
                <div class="space-y-4">
                    <!-- Cambio de Turno Fields -->
                    <div x-show="activeTab === 'turno'" class="space-y-4">
                        <div>
                            <x-input-label :value="__('Turno Actual')" />
                            <input type="text" readonly :value="currentShiftName" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:outline-none text-xs font-semibold py-2">
                        </div>
                        <div>
                            <x-input-label for="new_shift_id" :value="__('Nuevo Turno *')" />
                            <select id="new_shift_id" x-model="newShiftId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs">
                                <option value="">Seleccione un nuevo turno...</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cambio de Vehiculo Fields -->
                    <div x-show="activeTab === 'vehiculo'" class="space-y-4">
                        <div>
                            <x-input-label :value="__('Vehículo Actual')" />
                            <input type="text" readonly :value="'Vehículo ' + currentVehiclePlate" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:outline-none text-xs font-semibold py-2">
                        </div>
                        <div>
                            <x-input-label for="new_vehicle_id" :value="__('Nuevo Vehículo *')" />
                            <select id="new_vehicle_id" x-model="newVehicleId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 text-xs">
                                <option value="">Seleccione un nuevo vehículo...</option>
                                @foreach($vehicles as $veh)
                                    <option value="{{ $veh->id }}">Vehículo {{ $veh->plate }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cambio de Personal Fields -->
                    <div x-show="activeTab === 'personal'" class="space-y-4">
                        <div>
                            <x-input-label for="replace_person" :value="__('Personal Actual *')" />
                            <select id="replace_person" x-model="replacePerson" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-xs">
                                <option value="">Seleccione el personal a reemplazar...</option>
                                <option value="conductor" x-text="'Conductor: ' + currentDriverName"></option>
                                <template x-for="h in currentHelpers" :key="h.id">
                                    <option :value="h.id" x-text="'Ayudante: ' + h.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="new_person_id" :value="__('Nuevo Personal *')" />
                            <select id="new_person_id" x-model="newPersonId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-xs">
                                <option value="">Buscar empleado disponible...</option>
                                @foreach($allStaff as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} {{ $st->last_name }} ({{ $st->staffType?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Right: Motive fields -->
                <div class="space-y-4">
                    <div>
                        <x-input-label for="motive_type" :value="__('Motivo Predefinido')" />
                        <select id="motive_type" x-model="motiveType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 text-xs">
                            <option value="Imprevistos">Imprevistos</option>
                            <option value="Falla Mecánica">Falla Mecánica</option>
                            <option value="Inasistencia">Inasistencia</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                            <option value="Obras en la vía">Obras en la vía</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="motive_details" :value="__('Motivo del Cambio *')" />
                        <textarea id="motive_details" x-model="motiveDetails" placeholder="Ingrese el motivo detallado de esta modificación..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 text-xs h-20 py-2 resize-none"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="addChange()" 
                                :class="{
                                    'bg-blue-600 hover:bg-blue-700': activeTab === 'turno',
                                    'bg-amber-500 hover:bg-amber-600': activeTab === 'vehiculo',
                                    'bg-emerald-600 hover:bg-emerald-700': activeTab === 'personal'
                                }"
                                class="w-full text-white text-xs font-bold py-2.5 px-4 rounded-xl transition flex items-center justify-center">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar cambio
                        </button>
                    </div>
                </div>
            </div>

            <!-- registered changes table section -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold text-gray-800 flex items-center">
                    <svg class="w-4 h-4 me-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Cambios Registrados
                </h4>
                <div class="border border-gray-150 rounded-xl overflow-hidden bg-white shadow-xs">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-150 text-gray-400 font-bold uppercase bg-gray-50/70 text-[10px]">
                                <th class="py-2.5 px-4">Tipo de Cambio</th>
                                <th class="py-2.5 px-4">Valor Anterior</th>
                                <th class="py-2.5 px-4">Valor Nuevo</th>
                                <th class="py-2.5 px-4">Motivo</th>
                                <th class="py-2.5 px-4">Descripción</th>
                                <th class="py-2.5 px-4 text-center" width="80">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-[11px]">
                            <!-- No changes placeholder -->
                            <template x-if="pendingChanges.length === 0">
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-400 italic">
                                        No hay cambios registrados. Agregue cambios usando los botones superiores.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="(c, idx) in pendingChanges" :key="idx">
                                <tr class="hover:bg-gray-50/30 transition">
                                    <td class="py-2.5 px-4">
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded capitalize"
                                              :class="{
                                                  'bg-blue-50 text-blue-700 border border-blue-100': c.type === 'turno',
                                                  'bg-amber-50 text-amber-700 border border-amber-100': c.type === 'vehiculo',
                                                  'bg-emerald-50 text-emerald-700 border border-emerald-100': c.type === 'conductor' || c.type === 'helper'
                                              }"
                                              x-text="c.type">
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 font-semibold text-gray-800" x-text="c.old_name"></td>
                                    <td class="py-2.5 px-4 font-semibold text-gray-900" x-text="c.new_name"></td>
                                    <td class="py-2.5 px-4 text-gray-650" x-text="c.reason_type"></td>
                                    <td class="py-2.5 px-4 text-gray-650" x-text="c.details"></td>
                                    <td class="py-2.5 px-4 text-center">
                                        <button type="button" @click="removeChange(idx)" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 hover:bg-red-50 rounded-lg transition" title="Deshacer cambio">
                                            Deshacer
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Save Trigger -->
            <form action="{{ route('admin.planning.update', $planning->id) }}" method="POST" class="pt-4 border-t border-gray-100">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="changes" :value="JSON.stringify(pendingChanges)">

                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.planning.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="pendingChanges.length === 0" 
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10 disabled:opacity-50 disabled:cursor-not-allowed">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
