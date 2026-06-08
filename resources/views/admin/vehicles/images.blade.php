<x-app-layout>
    <x-slot:title>
        Gestionar Imágenes - {{ $vehicle->name }}
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-4xl">
        <div data-modal-size="max-w-4xl" class="max-w-4xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Gestionar Imágenes</h3>
                    <p class="text-xs text-gray-400">Administra las fotos del vehículo <strong>{{ $vehicle->name }}</strong> (Placa: {{ $vehicle->plate }}).</p>
                </div>
                <a href="{{ route('admin.vehicle.index') }}" data-turbo-frame="_top" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            <!-- Upload Multiple Form -->
            <div class="bg-gray-50 p-4 sm:p-6 rounded-2xl border border-gray-200">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Subir nuevas imágenes</h4>
                <form action="{{ route('admin.vehicle.images.store', $vehicle->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div x-data="{ filesCount: 0 }" class="space-y-2">
                        <div class="relative flex flex-col items-center justify-center border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl py-6 px-4 transition bg-white cursor-pointer group">
                            <input type="file" name="images[]" multiple accept="image/*" 
                                   @change="filesCount = $event.target.files.length"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required />
                            
                            <svg class="w-8 h-8 text-gray-400 group-hover:text-emerald-500 transition mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            
                            <span class="text-xs text-gray-500 font-semibold group-hover:text-emerald-600 transition" x-show="filesCount === 0">
                                Haz clic o arrastra imágenes aquí
                            </span>
                            <span class="text-xs text-emerald-600 font-bold" x-show="filesCount > 0" x-text="filesCount === 1 ? '1 imagen seleccionada' : filesCount + ' imágenes seleccionadas'">
                            </span>
                            <span class="text-[10px] text-gray-400 mt-1">Formatos: JPG, PNG, GIF, WEBP. Máx. 2MB por imagen.</span>
                        </div>
                        
                        <x-input-error :messages="$errors->get('images')" class="mt-1" />
                        <x-input-error :messages="$errors->get('images.*')" class="mt-1" />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Subir Imágenes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Images Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-gray-700">Imágenes Guardadas ({{ $images->count() }})</h4>
                    <span class="text-[10px] text-gray-400">La imagen principal se muestra en el listado general</span>
                </div>

                @if($images->isEmpty())
                    <div class="text-center py-12 border-2 border-dashed border-gray-150 rounded-2xl bg-gray-50/50">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-xs text-gray-450 font-semibold">El vehículo no tiene imágenes asociadas.</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Sube algunas fotos arriba para comenzar.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($images as $image)
                            <div class="group relative aspect-square rounded-2xl border border-gray-200 overflow-hidden bg-gray-50 hover:shadow-md transition">
                                <!-- Image Thumbnail -->
                                <img src="{{ $image->url }}" class="w-full h-full object-cover" alt="Imagen de vehículo">

                                <!-- Star/Profile Overlay Indicator -->
                                @if($image->profile)
                                    <div class="absolute top-2 left-2 px-2 py-1 bg-amber-500/90 text-white rounded-lg flex items-center space-x-1 shadow-md">
                                        <svg class="w-3.5 h-3.5 fill-current text-white" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span class="text-[9px] font-extrabold uppercase tracking-wider">Principal</span>
                                    </div>
                                @else
                                    <!-- Hover overlay with options -->
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-2">
                                        <form action="{{ route('admin.vehicle.images.set-profile', $image->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-lg transition duration-150" title="Marcar como principal">
                                                <svg class="w-4 h-4 fill-none stroke-current" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                </svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.vehicle.images.destroy', $image->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta imagen?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-lg transition duration-150" title="Eliminar imagen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer / Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.vehicle.index') }}" data-turbo-frame="_top" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition inline-flex items-center">
                    Cerrar y Volver
                </a>
            </div>
        </div>
    </turbo-frame>
</x-app-layout>
