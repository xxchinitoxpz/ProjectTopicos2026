<x-app-layout>
    <x-slot:title>
        Tipos de Personal
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Tipos de Personal</h3>
                <p class="text-xs text-gray-400">Gestiona los diferentes cargos o roles del personal (ej. Chofer, Supervisor, Recolector).</p>
            </div>
            <div>
                <a href="{{ route('admin.staff-type.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Tipo
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search Filter -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <form action="{{ route('admin.staff-type.index') }}" method="GET" class="flex max-w-md gap-2">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o descripción..." class="w-full text-sm" />
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('admin.staff-type.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Nombre</th>
                            <th class="py-3.5 px-6">Descripción</th>
                            <th class="py-3.5 px-6 text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($types as $type)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $type->name }}</td>
                                <td class="py-4 px-6 text-gray-500 text-xs">{{ $type->description ?? 'Sin descripción' }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.staff-type.edit', $type->id) }}" data-turbo-frame="modal" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.staff-type.destroy', $type->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este tipo de personal?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition duration-150" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-400">
                                    No se encontraron tipos de personal registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($types->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $types->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
