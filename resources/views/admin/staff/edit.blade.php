<x-app-layout>
    <x-slot:title>
        Modificar Personal
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-4xl">
        <div data-modal-size="max-w-4xl" class="max-w-4xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Modificar Personal</h3>
                    <p class="text-xs text-gray-400">Edita los detalles de: {{ $member->name }} {{ $member->last_name }}.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.staff.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.staff.update', $member->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Grid: Basic Info (DNI, Names, Last Name) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="dni" :value="__('DNI / Identificación')" />
                        <x-text-input id="dni" name="dni" type="text" value="{{ old('dni', $member->dni) }}" placeholder="Ej. 72834921" class="block mt-1 w-full text-sm" required autofocus />
                        <x-input-error :messages="$errors->get('dni')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Nombres')" />
                        <x-text-input id="name" name="name" type="text" value="{{ old('name', $member->name) }}" placeholder="Ej. Juan" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="last_name" :value="__('Apellidos')" />
                        <x-text-input id="last_name" name="last_name" type="text" value="{{ old('last_name', $member->last_name) }}" placeholder="Ej. Pérez Ramos" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid: Email, Role, Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="email" :value="__('Correo Electrónico')" />
                        <x-text-input id="email" name="email" type="email" value="{{ old('email', $member->email) }}" placeholder="Ej. juan.perez@usat.edu.pe" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="staff_type_id" :value="__('Cargo / Tipo')" />
                        <select id="staff_type_id" name="staff_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="">-- Seleccionar Cargo --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('staff_type_id', $member->staff_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('staff_type_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Estado')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid: Contact & Additional Info (Birthdate, Phone, Address) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="birthdate" :value="__('Fecha de Nacimiento')" />
                        <x-text-input id="birthdate" name="birthdate" type="date" value="{{ old('birthdate', $member->birthdate?->format('Y-m-d')) }}" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('birthdate')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('Teléfono / Celular')" />
                        <x-text-input id="phone" name="phone" type="text" value="{{ old('phone', $member->phone) }}" placeholder="Ej. 987654321" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Dirección de Domicilio')" />
                        <x-text-input id="address" name="address" type="text" value="{{ old('address', $member->address) }}" placeholder="Ej. Av. Balta 123" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('address')" class="mt-1" />
                    </div>
                </div>

                <!-- Section: Photo Upload -->
                <div>
                    <x-input-label for="photo" :value="__('Fotografía de Perfil')" />
                    <div class="flex items-center gap-6 mt-1.5">
                        <div class="w-20 h-20 rounded-2xl border border-gray-200 shadow-sm overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img id="photo_preview" src="{{ $imageUrl }}" class="w-full h-full object-cover" alt="Vista previa">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="photo" name="photo" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer" />
                            <p class="text-[10px] text-gray-400 mt-1">Dejar vacío si no deseas cambiar la fotografía actual. JPG, PNG, GIF, WEBP. Máx 2MB.</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.staff.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Actualizar Personal
                    </button>
                </div>
            </form>
        </div>

        <!-- Photo preview script -->
        <script>
            (function() {
                const photoInput = document.getElementById('photo');
                const preview = document.getElementById('photo_preview');

                if (photoInput) {
                    photoInput.addEventListener('change', function(e) {
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
