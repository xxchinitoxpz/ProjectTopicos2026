<x-app-layout>
    <x-slot:title>
        Nuevo Mantenimiento
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Registrar Mantenimiento</h3>
                <p class="text-xs text-gray-400">Define el periodo de mantenimiento de vehiculos.</p>
            </div>
            <a href="{{ route('admin.maintenance.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                Volver
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.maintenance.store') }}" method="POST" class="p-6 space-y-6" data-turbo="false" data-ajax-form>
                @csrf
                <div data-ajax-feedback class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                <div>
                    <x-input-label for="nombre" :value="__('Nombre')" />
                    <x-text-input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" class="block mt-1 w-full text-sm" required autofocus />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="fecha_inicio" :value="__('Fecha de Inicio')" />
                        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio', date('Y-m-d')) }}" class="block mt-1 w-full text-sm" required onchange="document.getElementById('fecha_fin').min = this.value; if (document.getElementById('fecha_fin').value < this.value) document.getElementById('fecha_fin').value = this.value;" />
                        <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin" :value="__('Fecha de Fin')" />
                        <x-text-input id="fecha_fin" name="fecha_fin" type="date" value="{{ old('fecha_fin', date('Y-m-d')) }}" min="{{ old('fecha_inicio', date('Y-m-d')) }}" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('fecha_fin')" class="mt-1" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.maintenance.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Guardar Mantenimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
