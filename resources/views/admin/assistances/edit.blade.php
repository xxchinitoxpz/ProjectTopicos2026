<x-app-layout>
    <x-slot:title>
        Editar Asistencia
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-2xl">
        <div data-modal-size="max-w-2xl" 
             x-data="{
                 shifts: {{ json_encode($shifts) }},
                 time: '{{ old('time', $time) }}',
                 selectedShiftId: '',
                 selectedShiftName: 'Ninguno',
                 selectedType: 'ingreso',
                 selectedTypeName: 'Ingreso',
                 
                 init() {
                     this.$watch('time', (function() { this.calculateShiftAndType(); }).bind(this));
                     this.calculateShiftAndType();
                 },
                 
                 calculateShiftAndType() {
                     if (!this.time || this.shifts.length === 0) {
                         this.selectedShiftId = '';
                         this.selectedShiftName = 'Ninguno';
                         this.selectedType = 'ingreso';
                         this.selectedTypeName = 'Ingreso';
                         return;
                     }
                     
                     var [h, m] = this.time.split(':').map(Number);
                     var timeInMinutes = h * 60 + m;
                     
                     var minDiff = Infinity;
                     var bestShift = null;
                     var bestType = 'ingreso';
                     
                     for (var i = 0; i < this.shifts.length; i++) {
                         var shift = this.shifts[i];
                         var [inH, inM] = shift.check_in.split(':').map(Number);
                         var checkInMinutes = inH * 60 + inM;
                         
                         var [outH, outM] = shift.check_out.split(':').map(Number);
                         var checkOutMinutes = outH * 60 + outM;
                         
                         var diffIn = Math.abs(timeInMinutes - checkInMinutes);
                         if (diffIn < minDiff) {
                             minDiff = diffIn;
                             bestShift = shift;
                             bestType = 'ingreso';
                         }
                         
                         var diffOut = Math.abs(timeInMinutes - checkOutMinutes);
                         if (diffOut < minDiff) {
                             minDiff = diffOut;
                             bestShift = shift;
                             bestType = 'salida';
                         }
                     }
                     
                     if (bestShift) {
                         this.selectedShiftId = bestShift.id;
                         this.selectedShiftName = bestShift.name;
                         this.selectedType = bestType;
                         this.selectedTypeName = (bestType === 'ingreso' ? 'Ingreso' : 'Salida');
                     }
                 }
             }"
             class="max-w-2xl mx-auto space-y-6 p-6 sm:p-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Editar Asistencia</h3>
                    <p class="text-xs text-gray-400">Modifica los detalles de la asistencia seleccionada.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.assistance.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.assistance.update', $assistance->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Staff Select Field -->
                <div>
                    <x-input-label for="staff_id" :value="__('Miembro del Personal')" />
                    <select id="staff_id" name="staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                        <option value="">-- Seleccionar Personal --</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ old('staff_id', $assistance->staff_id) == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} {{ $staff->last_name }} (DNI: {{ $staff->dni }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('staff_id')" class="mt-1" />
                </div>

                <!-- Dates & Time Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date" :value="__('Fecha')" />
                        <x-text-input id="date" name="date" type="date" value="{{ old('date', $date) }}" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="time" :value="__('Hora')" />
                        <x-text-input id="time" name="time" type="time" x-model="time" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('time')" class="mt-1" />
                    </div>
                </div>

                <!-- Automatic Determination Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/70 p-4 rounded-xl border border-gray-150">
                    <div>
                        <x-input-label :value="__('Turno (Asignado Automáticamente)')" />
                        <x-text-input type="text" x-model="selectedShiftName" readonly class="block mt-1 w-full text-sm bg-white text-gray-500 font-semibold border-gray-200 cursor-not-allowed shadow-none" />
                    </div>

                    <div>
                        <x-input-label :value="__('Tipo de Asistencia')" />
                        <div class="mt-1 flex h-10 items-center">
                            <template x-if="selectedType === 'ingreso'">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-bold bg-indigo-50 text-indigo-750 border border-indigo-100">
                                    <svg class="w-4 h-4 me-1.5 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Ingreso
                                </span>
                            </template>
                            <template x-if="selectedType === 'salida'">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-bold bg-amber-50 text-amber-750 border border-amber-100">
                                    <svg class="w-4 h-4 me-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1"></path>
                                    </svg>
                                    Salida
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- State Select Field -->
                <div>
                    <x-input-label for="state" :value="__('Estado')" />
                    <select id="state" name="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                        <option value="presente" {{ old('state', $assistance->state) == 'presente' ? 'selected' : '' }}>Presente</option>
                        <option value="ausente" {{ old('state', $assistance->state) == 'ausente' ? 'selected' : '' }}>Ausente</option>
                    </select>
                    <x-input-error :messages="$errors->get('state')" class="mt-1" />
                </div>

                <!-- Notes Field -->
                <div>
                    <x-input-label for="notes" :value="__('Notas o Detalles Adicionales')" />
                    <textarea id="notes" name="notes" rows="3" placeholder="Ej. Retraso de tráfico, permiso excepcional, etc..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('notes', $assistance->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.assistance.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Actualizar Asistencia
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
