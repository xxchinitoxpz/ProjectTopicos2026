<x-app-layout>
    <x-slot:title>
        Listado de Personal
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Listado de Personal</h3>
                <p class="text-xs text-gray-400">Gestiona los miembros del personal y sus roles en la plataforma.</p>
            </div>
            <div>
                <a href="{{ route('admin.staff.create') }}" data-turbo-frame="modal" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-600/10">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Personal
                </a>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search & Pagination Filter -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.staff.index') }}" method="GET" class="flex flex-1 gap-2">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <x-text-input type="text" name="search" value="{{ $search }}" placeholder="Buscar por DNI, nombre, email o cargo..." class="flex-1 text-sm" />
                    <button type="submit" class="px-4 py-2 bg-usat-blue hover:bg-blue-800 text-white text-sm font-bold rounded-xl transition">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('admin.staff.index', ['per_page' => $perPage]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-xl transition flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>
                <form action="{{ route('admin.staff.index') }}" method="GET" class="flex items-center gap-2 text-sm text-gray-500">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <span>Mostrar</span>
                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm py-1.5 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span>registros</span>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-150 text-gray-400 font-bold text-xs uppercase bg-gray-50/70">
                            <th class="py-3.5 px-6" width="90">Foto</th>
                            <th class="py-3.5 px-6">Personal</th>
                            <th class="py-3.5 px-6">DNI</th>
                            <th class="py-3.5 px-6">Correo Electrónico</th>
                            <th class="py-3.5 px-6">Tipo / Cargo</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($staffMembers as $member)
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="py-3.5 px-6">
                                    <img src="{{ $member->photo_url }}" class="w-12 h-12 object-cover rounded-xl border border-gray-200 shadow-sm" alt="{{ $member->name }}">
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $member->name }} {{ $member->last_name }}</div>
                                    @if($member->birthdate)
                                        <div class="text-[10px] text-gray-405 mt-0.5">F. Nac: {{ $member->birthdate->format('d/m/Y') }} ({{ $member->birthdate->age }} años)</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-xs text-usat-blue font-semibold">{{ $member->dni }}</code>
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="text-xs font-semibold text-gray-600">{{ $member->email }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5 space-y-0.5">
                                        @if($member->phone)
                                            <div>Telf: {{ $member->phone }}</div>
                                        @endif
                                        @if($member->address)
                                            <div class="truncate max-w-[200px]" title="{{ $member->address }}">Dir: {{ $member->address }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-usat-blue rounded-lg border border-blue-100">{{ $member->staffType?->name }}</span>
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($member->status === 'active')
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">Activo</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-full border border-gray-200">Inactivo</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.staff.edit', $member->id) }}" data-turbo-frame="modal" class="p-2 bg-amber-50 text-usat-gold hover:bg-amber-100 rounded-lg transition duration-150" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar a este miembro del personal?');" class="inline">
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
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    No se encontraron miembros del personal registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($staffMembers->hasPages())
                <div class="p-6 border-t border-gray-100">
                    {{ $staffMembers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
