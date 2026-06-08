<x-app-layout>
    <x-slot:title>
        Nuevo Turno
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-2xl">
        <div data-modal-size="max-w-2xl" class="max-w-2xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Registrar Turno</h3>
                    <p class="text-xs text-gray-400">Agrega un nuevo horario de trabajo a la plataforma.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.shift.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.shift.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name Field -->
                <div>
                    <x-input-label for="name" :value="__('Nombre del Turno')" />
                    <x-text-input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Ej. Turno Mañana, Turno Noche, etc." class="block mt-1 w-full text-sm" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Hours Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="check_in" :value="__('Hora de Entrada')" />
                        <x-text-input id="check_in" name="check_in" type="time" value="{{ old('check_in', '08:00') }}" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('check_in')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="check_out" :value="__('Hora de Salida')" />
                        <x-text-input id="check_out" name="check_out" type="time" value="{{ old('check_out', '17:00') }}" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('check_out')" class="mt-1" />
                    </div>
                </div>

                <!-- Description Field -->
                <div>
                    <x-input-label for="description" :value="__('Descripción o Detalles del Horario')" />
                    <textarea id="description" name="description" rows="3" placeholder="Ej. Incluye refrigerio de 13:00 a 14:00, lunes a viernes..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.shift.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Guardar Turno
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
