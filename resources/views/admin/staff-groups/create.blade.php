<x-app-layout>
    <x-slot:title>Nuevo Grupo de Personal</x-slot:title>

    <div class="space-y-4">
        <div class="flex items-center justify-between bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Registrar Nuevo Grupo</h3>
                <p class="text-xs text-gray-400">Asigna un equipo de recolección asociando vehículo, zona, turno y personal.</p>
            </div>
            <a href="{{ route('admin.staff-group.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">← Volver</a>
        </div>

        <form action="{{ route('admin.staff-group.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Panel izquierdo: Datos Generales --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <h4 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-3">Información General</h4>

                    <div>
                        <x-input-label for="name" value="Nombre del Grupo *"/>
                        <x-text-input id="name" name="name" type="text" value="{{ old('name') }}"
                            placeholder="Ej: Grupo Recolector Norte A" class="block mt-1 w-full text-sm" required autofocus/>
                        <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="shift_id" value="Turno *"/>
                        <select id="shift_id" name="shift_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="">— Seleccionar Turno —</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }} ({{ substr($shift->check_in, 0, 5) }} - {{ substr($shift->check_out, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('shift_id')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="zone_id" value="Zona de Recolección *"/>
                        <select id="zone_id" name="zone_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="">— Seleccionar Zona —</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }} ({{ $zone->district?->name }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('zone_id')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="vehicle_id" value="Vehículo Asignado *"/>
                        <select id="vehicle_id" name="vehicle_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="">— Seleccionar Vehículo —</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate }} - {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="status" value="Estado *"/>
                        <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1"/>
                    </div>

                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.staff-group.index') }}"
                            class="flex-1 text-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                            Guardar Grupo
                        </button>
                    </div>
                </div>

                {{-- Panel derecho: Días, Conductor y Ayudantes --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <h4 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-3">Programación de Operación & Personal</h4>

                    <!-- Días de la semana -->
                    <div>
                        <x-input-label value="Días de Operación *"/>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-2">
                            @foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $day)
                                <label class="flex items-center gap-2 p-2.5 border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/20 rounded-xl transition cursor-pointer text-xs font-semibold capitalize text-gray-700">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" 
                                        {{ is_array(old('days')) && in_array($day, old('days')) ? 'checked' : '' }}
                                        class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                    <span>{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('days')" class="mt-1"/>
                    </div>

                    <!-- Conductor principal -->
                    <div>
                        <x-input-label for="driver_id" value="Conductor Principal *"/>
                        <select id="driver_id" name="driver_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="">— Seleccionar Conductor —</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }} {{ $driver->last_name }} (DNI: {{ $driver->dni }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-1"/>
                    </div>

                    <!-- Ayudantes del Grupo (Selección Múltiple) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <x-input-label value="Ayudantes (Operarios)"/>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Seleccionar varios</span>
                        </div>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto p-3 border border-gray-150 rounded-xl bg-gray-50/50">
                            @forelse($helpers as $helper)
                                <label class="flex items-center justify-between p-2 hover:bg-white rounded-lg transition border border-transparent hover:border-gray-100 cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="helpers[]" value="{{ $helper->id }}"
                                            {{ is_array(old('helpers')) && in_array($helper->id, old('helpers')) ? 'checked' : '' }}
                                            class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                        <div>
                                            <span class="text-xs font-bold text-gray-800">{{ $helper->name }} {{ $helper->last_name }}</span>
                                            <span class="block text-[10px] text-gray-450">{{ $helper->dni }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 text-[9px] font-bold bg-gray-100 text-gray-500 rounded border border-gray-150 uppercase">
                                        {{ $helper->staffType?->name ?? 'Operario' }}
                                    </span>
                                </label>
                            @empty
                                <div class="p-4 text-center text-xs text-gray-400 italic">No hay personal de apoyo disponible</div>
                            @endforelse
                        </div>
                        <x-input-error :messages="$errors->get('helpers')" class="mt-1"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
