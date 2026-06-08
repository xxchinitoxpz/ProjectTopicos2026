<x-app-layout>
    <x-slot:title>
        Modificar Marca de Vehículo
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-2xl">
        <div class="max-w-2xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Modificar Marca</h3>
                    <p class="text-xs text-gray-400">Edita los detalles de la marca: {{ $brand->name }}.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.brand.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div>
                    <x-input-label for="name" :value="__('Nombre de la Marca')" />
                    <x-text-input id="name" name="name" type="text" value="{{ old('name', $brand->name) }}" placeholder="Ej. Toyota, Volvo, etc." class="block mt-1 w-full text-sm" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Logo Upload & Preview Field -->
                <div>
                    <x-input-label for="logo" :value="__('Logotipo')" />
                    <div class="flex items-center gap-6 mt-1.5">
                        <div class="w-16 h-16 rounded-xl border border-gray-200 shadow-sm overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img id="logo_preview" src="{{ $brand->logo_url }}" class="w-full h-full object-cover" alt="Vista previa">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="logo" name="logo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer" />
                            <p class="text-[10px] text-gray-400 mt-1">Dejar vacío si no deseas cambiar el logotipo actual. JPG, PNG, GIF, WEBP. Máx 2MB.</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                </div>

                <!-- Description Field -->
                <div>
                    <x-input-label for="description" :value="__('Descripción (Opcional)')" />
                    <textarea id="description" name="description" rows="3" placeholder="Detalle adicional sobre el fabricante..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('description', $brand->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.brand.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Actualizar Marca
                    </button>
                </div>
            </form>
        </div>

        <!-- Image preview script -->
        <script>
            (function() {
                const logoInput = document.getElementById('logo');
                const preview = document.getElementById('logo_preview');

                if (logoInput) {
                    logoInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                if (preview) preview.src = event.target.result;
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }
            })();
        </script>
    </turbo-frame>
</x-app-layout>
