<x-app-layout>
    <x-slot:title>
        Modificar Feriado
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-2xl">
        <div data-modal-size="max-w-2xl" class="max-w-2xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Modificar Feriado</h3>
                    <p class="text-xs text-gray-400">Edita los detalles del feriado registrado.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.holiday.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.holiday.update', $holiday->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Date & State Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date" :value="__('Fecha')" />
                        <x-text-input id="date" name="date" type="date" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" class="block mt-1 w-full text-sm" required autofocus />
                        <x-input-error :messages="$errors->get('date')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="state" :value="__('Estado')" />
                        <select id="state" name="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="activo" {{ old('state', $holiday->state) === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('state', $holiday->state) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('state')" class="mt-1" />
                    </div>
                </div>

                <!-- Description Field -->
                <div>
                    <x-input-label for="description" :value="__('Descripción o Motivo')" />
                    <x-text-input id="description" name="description" type="text" value="{{ old('description', $holiday->description) }}" placeholder="Ej. Año Nuevo, Día del Trabajador, etc." class="block mt-1 w-full text-sm" required />
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.holiday.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Actualizar Feriado
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
