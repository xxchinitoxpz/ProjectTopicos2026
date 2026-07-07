<x-app-layout>
    <x-slot:title>
        Modificar Motivo
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-lg">
        <div data-modal-size="max-w-lg" class="w-full max-w-lg mx-auto overflow-hidden rounded-sm bg-white shadow-2xl">
            <div class="flex items-center justify-between bg-blue-900 px-4 py-3 text-white">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 8h10M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                    </svg>
                    <h3 class="text-base font-semibold">Modificar Motivo</h3>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.motive.index') }}'" class="text-white/70 transition hover:text-white focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.motive.update', $motive->id) }}" method="POST" class="p-5" data-turbo="false" data-turbo-frame="_top">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('Nombre')" />
                        <x-text-input id="name" name="name" type="text" value="{{ old('name', $motive->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200 focus:ring text-sm" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Descripción')" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200 focus:ring text-sm" placeholder="Agregue una descripción">{{ old('description', $motive->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.motive.index') }}'" class="rounded-md bg-red-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-600">
                        Cancelar
                    </button>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
