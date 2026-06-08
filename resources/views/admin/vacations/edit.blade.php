<x-app-layout>
    <x-slot:title>
        Modificar Solicitud de Vacaciones
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-2xl">
        <div data-modal-size="max-w-2xl" 
             x-data="{
                 dateStart: '{{ old('date_start', $vacation->date_start?->format('Y-m-d')) }}',
                 dateEnd: '{{ old('date_end', $vacation->date_end?->format('Y-m-d')) }}',
                 daysRequested: '{{ old('days_requested', $vacation->days_requested) }}',
                 
                 init() {
                     this.$watch('dateStart', (function() { this.calculateDays(); }).bind(this));
                     this.$watch('dateEnd', (function() { this.calculateDays(); }).bind(this));
                     this.calculateDays();
                 },
                 
                 calculateDays() {
                     if (!this.dateStart || !this.dateEnd) {
                         this.daysRequested = '';
                         return;
                     }
                     
                     var start = new Date(this.dateStart + 'T00:00:00');
                     var end = new Date(this.dateEnd + 'T00:00:00');
                     
                     if (end < start) {
                         this.daysRequested = '';
                         return;
                     }
                     
                     var diffTime = end.getTime() - start.getTime();
                     var diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
                     
                     this.daysRequested = diffDays;
                 }
             }"
             class="max-w-2xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Modificar Solicitud</h3>
                    <p class="text-xs text-gray-400">Edita los detalles de la solicitud de vacaciones de: <strong>{{ $vacation->staff?->name }} {{ $vacation->staff?->last_name }}</strong>.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.vacation.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.vacation.update', $vacation->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Staff Select Field -->
                <div>
                    <x-input-label for="staff_id" :value="__('Miembro del Personal')" />
                    <select id="staff_id" name="staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm bg-gray-50" required>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ old('staff_id', $vacation->staff_id) == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} {{ $staff->last_name }} (DNI: {{ $staff->dni }} | Disp: {{ $staff->vacation_days }} días)
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('staff_id')" class="mt-1" />
                </div>

                <!-- Dates Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date_start" :value="__('Fecha de Inicio')" />
                        <x-text-input id="date_start" name="date_start" type="date" x-model="dateStart" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('date_start')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="date_end" :value="__('Fecha de Finalización')" />
                        <x-text-input id="date_end" name="date_end" type="date" x-model="dateEnd" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('date_end')" class="mt-1" />
                    </div>
                </div>

                <!-- Days Requested Field -->
                <div>
                    <x-input-label for="days_requested" :value="__('Cantidad de Días Solicitados')" />
                    <x-text-input id="days_requested" name="days_requested" type="number" min="1" x-model="daysRequested" readonly class="block mt-1 w-full text-sm bg-gray-50 text-gray-500 font-semibold border-gray-200 cursor-not-allowed shadow-none" required />
                    <x-input-error :messages="$errors->get('days_requested')" class="mt-1" />
                </div>

                <!-- Notes Field -->
                <div>
                    <x-input-label for="notes" :value="__('Observaciones o Notas Adicionales')" />
                    <textarea id="notes" name="notes" rows="3" placeholder="Ej. Por motivos de salud, viaje programado, etc..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('notes', $vacation->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.vacation.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Actualizar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
