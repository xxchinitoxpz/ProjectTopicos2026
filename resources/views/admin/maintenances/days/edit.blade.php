<x-app-layout>
    <x-slot:title>
        Editar Dia
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Editar Dia</h3>
                <p class="text-xs text-gray-400">{{ $day->fecha->format('d/m/Y') }} - {{ $maintenance->nombre }}</p>
            </div>
            <a href="{{ route('admin.maintenance.schedule.days.index', [$maintenance, $schedule]) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                Volver
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.maintenance.schedule.days.update', [$maintenance, $schedule, $day]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" data-turbo="false" data-ajax-form>
                @csrf
                @method('PUT')
                <div data-ajax-feedback class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="fecha" :value="__('Fecha')" />
                        <x-text-input id="fecha" name="fecha" type="text" value="{{ $day->fecha->format('d/m/Y') }}" class="block mt-1 w-full text-sm bg-gray-50" disabled />
                    </div>

                    <div>
                        <x-input-label for="realizado" :value="__('Estado')" />
                        <select id="realizado" name="realizado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="1" {{ old('realizado', $day->realizado) ? 'selected' : '' }}>Realizado</option>
                            <option value="0" {{ old('realizado', $day->realizado) ? '' : 'selected' }}>No realizado</option>
                        </select>
                        <x-input-error :messages="$errors->get('realizado')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="observacion" :value="__('Observacion')" />
                    <textarea id="observacion" name="observacion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm">{{ old('observacion', $day->observacion) }}</textarea>
                    <x-input-error :messages="$errors->get('observacion')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="imagen" :value="__('Imagen')" />
                        <input id="imagen" name="imagen" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer" />
                        <x-input-error :messages="$errors->get('imagen')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-center">
                        <img id="day_image_preview" src="{{ $day->image_url }}" onerror="this.onerror=null;this.src='{{ \App\Support\PublicImageStorage::placeholder() }}';" alt="Imagen actual" class="w-full max-w-xs h-40 object-cover rounded-xl border border-gray-200">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.maintenance.schedule.days.index', [$maintenance, $schedule]) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const input = document.getElementById('imagen');
            const preview = document.getElementById('day_image_preview');
            if (!input || !preview) return;

            input.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (event) => {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
</x-app-layout>
