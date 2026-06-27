<x-app-layout>
    <x-slot:title>
        Nuevo Horario
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Registrar Horario</h3>
                <p class="text-xs text-gray-400">{{ $maintenance->nombre }}</p>
            </div>
            <a href="{{ route('admin.maintenance.schedule.index', $maintenance) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                Volver
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.maintenance.schedule.store', $maintenance) }}" method="POST" class="p-6 space-y-6" data-turbo="false" data-ajax-form>
                @csrf
                <div data-ajax-feedback class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                @include('admin.maintenances.schedules.form')

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.maintenance.schedule.index', $maintenance) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Guardar Horario
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
