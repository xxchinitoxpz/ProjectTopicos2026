<x-app-layout>
    <x-slot:title>
        Mantenimiento de Vehiculos
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Mantenimiento de Vehiculos</h3>
                <p class="text-xs text-gray-400">Programa y consulta las actividades de mantenimiento de la flota.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h4 class="text-sm font-bold text-gray-700">Programacion de mantenimientos</h4>
            </div>

            <div class="p-8 text-center text-sm text-gray-500">
                No hay mantenimientos registrados todavia.
            </div>
        </div>
    </div>
</x-app-layout>
