<x-app-layout>
    <x-slot:title>
        Nuevo Vehículo
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-4xl">
        <div data-modal-size="max-w-4xl" class="max-w-4xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Registrar Vehículo</h3>
                    <p class="text-xs text-gray-400">Agrega un nuevo transporte a la flota del campus.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.vehicle.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.vehicle.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Grid Section 1: General Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="name" :value="__('Nombre del Vehículo')" />
                        <x-text-input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Ej. Camión Recolector 1" class="block mt-1 w-full text-sm" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="plate" :value="__('Placa / Matrícula')" />
                        <x-text-input id="plate" name="plate" type="text" value="{{ old('plate') }}" placeholder="Ej. ABC-123" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('plate')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="code" :value="__('Código Interno')" />
                        <x-text-input id="code" name="code" type="text" value="{{ old('code') }}" placeholder="Ej. V-01" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('code')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid Section 2: Relationships (Brand, Model, Type, Color) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <x-input-label for="brand_id" :value="__('Marca')" />
                        <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('brand_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="model_id" :value="__('Modelo')" />
                        <select id="model_id" name="model_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required disabled>
                            <option value="">-- Seleccionar Marca Primero --</option>
                        </select>
                        <x-input-error :messages="$errors->get('model_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="type_id" :value="__('Tipo de Vehículo')" />
                        <select id="type_id" name="type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="color_id" :value="__('Color')" />
                        <select id="color_id" name="color_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>{{ $color->name }} ({{ $color->code }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('color_id')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid Section 3: Technical Spec & Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="year" :value="__('Año de Fabricación')" />
                        <x-text-input id="year" name="year" type="number" min="1900" max="{{ date('Y')+1 }}" value="{{ old('year') }}" placeholder="Ej. 2020" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('year')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="occupant_capacity" :value="__('Capacidad Ocupantes')" />
                        <x-text-input id="occupant_capacity" name="occupant_capacity" type="number" min="1" value="{{ old('occupant_capacity') }}" placeholder="Ej. 3" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('occupant_capacity')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Estado')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid Section 3.5: Capacities in TN / Gl. -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="load_capacity" :value="__('Capacidad Carga (TN)')" />
                        <x-text-input id="load_capacity" name="load_capacity" type="number" step="0.01" min="0" value="{{ old('load_capacity') }}" placeholder="Ej. 3.50" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('load_capacity')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="compaction_capacity" :value="__('Capacidad Compactación (TN)')" />
                        <x-text-input id="compaction_capacity" name="compaction_capacity" type="number" step="0.01" min="0" value="{{ old('compaction_capacity') }}" placeholder="Ej. 5.00" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('compaction_capacity')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="combustible_capacity" :value="__('Capacidad Combustible (Gl.)')" />
                        <x-text-input id="combustible_capacity" name="combustible_capacity" type="number" step="0.01" min="0" value="{{ old('combustible_capacity') }}" placeholder="Ej. 15.00" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('combustible_capacity')" class="mt-1" />
                    </div>
                </div>

                <!-- Description Field -->
                <div>
                    <x-input-label for="description" :value="__('Descripción o Notas Adicionales')" />
                    <textarea id="description" name="description" rows="3" placeholder="Detalles de equipamiento, estado mecánico..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.vehicle.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Guardar Vehículo
                    </button>
                </div>
            </form>
        </div>

        <!-- Script for dynamic brand models loading -->
        <script>
            (function() {
                // Dynamic Models Loading
                const brandSelect = document.getElementById('brand_id');
                const modelSelect = document.getElementById('model_id');

                function loadModels(brandId, selectedModelId = null) {
                    if (!modelSelect) return;
                    if (!brandId) {
                        modelSelect.innerHTML = '<option value="">-- Seleccionar Marca Primero --</option>';
                        modelSelect.disabled = true;
                        return;
                    }

                    modelSelect.disabled = true;
                    modelSelect.innerHTML = '<option value="">Cargando modelos...</option>';

                    fetch(`/vehicles/brand-models-by-brand/${brandId}`)
                        .then(response => response.json())
                        .then(data => {
                            let options = '<option value="">-- Seleccionar Modelo --</option>';
                            data.forEach(model => {
                                const isSelected = selectedModelId == model.id ? 'selected' : '';
                                options += `<option value="${model.id}" ${isSelected}>${model.name}</option>`;
                            });
                            modelSelect.innerHTML = options;
                            modelSelect.disabled = false;
                        })
                        .catch(err => {
                            console.error('Error cargando modelos:', err);
                            modelSelect.innerHTML = '<option value="">Error al cargar modelos</option>';
                        });
                }

                if (brandSelect && modelSelect) {
                    brandSelect.addEventListener('change', function(e) {
                        loadModels(e.target.value);
                    });

                    // Reload model if brand was pre-selected (old values)
                    if (brandSelect.value) {
                        const oldModelId = "{{ old('model_id') }}";
                        loadModels(brandSelect.value, oldModelId);
                    }
                }
            })();
        </script>
    </turbo-frame>
</x-app-layout>
