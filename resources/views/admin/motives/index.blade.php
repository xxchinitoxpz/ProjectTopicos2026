<x-app-layout>
    <x-slot:title>
        Motivos
    </x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Motivos</h3>
                <p class="text-xs text-gray-400">Administra los motivos usados en las modificaciones de programación.</p>
            </div>
            <div>
                <a href="{{ route('admin.motive.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Motivo
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.motive.index') }}" method="GET" class="flex flex-1 gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o descripción..." class="flex-1 text-sm" />
                    <select name="state" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ $stateFilter === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ $stateFilter === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Buscar
                    </button>
                    @if($search || $stateFilter)
                        <a href="{{ route('admin.motive.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>
                <form action="{{ route('admin.motive.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="state" value="{{ $stateFilter }}">
                    <span>Mostrar</span>
                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6">Nombre</th>
                            <th class="py-3.5 px-6">Descripción</th>
                            <th class="py-3.5 px-6 text-center">Fecha Creación</th>
                            <th class="py-3.5 px-6 text-center">Fecha Actualización</th>
                            <th class="py-3.5 px-6 text-center" width="180">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($motives as $motive)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $motive->name }}</div>
                                </td>
                                <td class="py-3.5 px-6 text-xs text-gray-500">
                                    {{ $motive->description ?? 'Sin descripción' }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    {{ $motive->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    {{ $motive->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.motive.edit', $motive->id) }}" data-turbo-frame="modal" class="inline-flex items-center rounded-md bg-amber-50 px-3 py-1.5 text-xs font-semibold text-usat-gold hover:bg-amber-100 transition" title="Editar">
                                            Editar
                                        </a>
                                        <form action="{{ route('admin.motive.destroy', $motive->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este motivo?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-md bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition" title="Eliminar">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    No se encontraron motivos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($motives->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $motives->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
