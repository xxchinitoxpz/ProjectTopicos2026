<x-app-layout>
    <x-slot:title>
        Modificar Color de Vehículo
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-2xl">
        <div class="max-w-2xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Modificar Color</h3>
                    <p class="text-xs text-gray-400">Edita los detalles del color: {{ $color->name }}.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.vehicle-color.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.vehicle-color.update', $color->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div>
                    <x-input-label for="name" :value="__('Nombre del Color')" />
                    <x-text-input id="name" name="name" type="text" value="{{ old('name', $color->name) }}" placeholder="Ej. Verde Ecológico" class="block mt-1 w-full text-sm" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Code Picker Field -->
                <div>
                    <x-input-label for="code" :value="__('Seleccionar Color')" />
                    <div class="flex items-center gap-4 mt-1.5">
                        <input type="color" id="color_picker" value="{{ old('color_picker', strtolower($color->code)) }}" class="w-16 h-11 rounded-lg border border-gray-200 cursor-pointer p-0 overflow-hidden bg-transparent" />
                        <x-text-input id="code" name="code" type="text" value="{{ old('code', $color->code) }}" placeholder="#10B981" class="w-36 text-sm font-semibold tracking-wider text-center" required readonly />
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">Usa la paleta para seleccionar el color. El código hexadecimal se genera automáticamente.</p>
                    <x-input-error :messages="$errors->get('code')" class="mt-1" />
                </div>

                <!-- Color Preview Area -->
                <div>
                    <x-input-label :value="__('Vista Previa')" />
                    <div id="color_preview" class="mt-1.5 w-full h-20 rounded-xl flex items-center justify-center font-bold text-sm border border-gray-200 shadow-inner transition-colors duration-150">
                        Texto de Muestra
                    </div>
                </div>

                <!-- Description Field -->
                <div>
                    <x-input-label for="description" :value="__('Descripción (Opcional)')" />
                    <textarea id="description" name="description" rows="3" placeholder="Detalle adicional sobre el uso del color..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('description', $color->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.vehicle-color.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Actualizar Color
                    </button>
                </div>
            </form>
        </div>

        <!-- Color picker logic -->
        <script>
            (function() {
                const picker = document.getElementById('color_picker');
                const codeInput = document.getElementById('code');
                const preview = document.getElementById('color_preview');

                function textColorForBg(hex) {
                    hex = hex.replace('#', '');
                    const r = parseInt(hex.substring(0, 2), 16);
                    const g = parseInt(hex.substring(2, 4), 16);
                    const b = parseInt(hex.substring(4, 6), 16);
                    const luminance = (r * 299 + g * 587 + b * 114) / 1000;
                    return luminance > 128 ? '#1e293b' : '#ffffff';
                }

                function updatePreview(hex) {
                    if (!codeInput || !preview) return;
                    hex = hex.toUpperCase();
                    codeInput.value = hex;
                    preview.style.backgroundColor = hex;
                    preview.style.color = textColorForBg(hex);
                    preview.innerText = hex + ' - Vista Previa';
                }

                if (picker) {
                    updatePreview(picker.value);
                    picker.addEventListener('input', function(e) {
                        updatePreview(e.target.value);
                    });
                    picker.addEventListener('change', function(e) {
                        updatePreview(e.target.value);
                    });
                }
            })();
        </script>
    </turbo-frame>
</x-app-layout>
