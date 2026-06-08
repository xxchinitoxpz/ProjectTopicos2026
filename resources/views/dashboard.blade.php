<x-app-layout>
    <x-slot:title>
        Dashboard de Gestión RSU
    </x-slot:title>

    <div class="space-y-8">
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-usat-blue to-blue-950 rounded-2xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl md:text-3xl font-extrabold tracking-tight">¡Bienvenido de vuelta, {{ Auth::user()->name }}!</h3>
                <p class="text-blue-100 mt-2 text-sm md:text-base max-w-xl">
                    Este es el panel central de Responsabilidad Social Universitaria (RSU). Aquí puedes gestionar el reciclaje, vehículos y rutas ecológicas de la universidad.
                </p>
            </div>
            <!-- Decorative SVG circles -->
            <div class="absolute right-0 bottom-0 translate-y-1/4 translate-x-1/4 opacity-10 pointer-events-none">
                <svg class="w-80 h-80 fill-current" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" />
                </svg>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card: Kilos Reciclados -->
            <div class="bg-white p-6 rounded-2xl border-t-4 border-emerald-500 shadow-md border border-gray-100 flex flex-col justify-between hover:shadow-lg transition duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kilos Reciclados</p>
                        <h4 class="text-3xl font-extrabold text-gray-800 mt-1">1,248 kg</h4>
                    </div>
                    <span class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </span>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-500 font-bold flex items-center me-2">
                        <svg class="w-3.5 h-3.5 me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                        +12%
                    </span>
                    <span class="text-gray-400 font-medium">vs la semana pasada</span>
                </div>
            </div>

            <!-- Card: Puntos Acumulados -->
            <div class="bg-white p-6 rounded-2xl border-t-4 border-usat-gold shadow-md border border-gray-100 flex flex-col justify-between hover:shadow-lg transition duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Puntos Acumulados</p>
                        <h4 class="text-3xl font-extrabold text-gray-800 mt-1">4,850 Pts</h4>
                    </div>
                    <span class="p-3 bg-amber-50 text-usat-gold rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </span>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-500 font-bold flex items-center me-2">
                        <svg class="w-3.5 h-3.5 me-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                        +8.2%
                    </span>
                    <span class="text-gray-400 font-medium">Incremento de canje</span>
                </div>
            </div>

            <!-- Card: Campañas Activas -->
            <div class="bg-white p-6 rounded-2xl border-t-4 border-usat-blue shadow-md border border-gray-100 flex flex-col justify-between hover:shadow-lg transition duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Campañas Activas</p>
                        <h4 class="text-3xl font-extrabold text-gray-800 mt-1">3 Campañas</h4>
                    </div>
                    <span class="p-3 bg-blue-50 text-usat-blue rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </span>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-usat-blue font-bold me-2">USAT Ecológica</span>
                    <span class="text-gray-400 font-medium">En campus principal</span>
                </div>
            </div>

            <!-- Card: Rutas Activas -->
            <div class="bg-white p-6 rounded-2xl border-t-4 border-emerald-600 shadow-md border border-gray-100 flex flex-col justify-between hover:shadow-lg transition duration-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rutas de Recojo</p>
                        <h4 class="text-3xl font-extrabold text-gray-800 mt-1">8 Activas</h4>
                    </div>
                    <span class="p-3 bg-green-50 text-emerald-700 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </span>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-600 font-bold me-2">3 Distritos</span>
                    <span class="text-gray-400 font-medium">Cobertura de transporte</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Activities Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Recent Activity Table -->
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-lg font-bold text-usat-blue">Últimos Registros de Reciclaje</h4>
                    <a href="#" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">Ver todo &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 font-semibold text-xs uppercase">
                                <th class="pb-3">Usuario / DNI</th>
                                <th class="pb-3">Tipo Residuo</th>
                                <th class="pb-3 text-right">Cantidad</th>
                                <th class="pb-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            <tr>
                                <td class="py-4">
                                    <div class="font-bold">Juan Pérez</div>
                                    <div class="text-xs text-gray-400">DNI: 72834921</div>
                                </td>
                                <td class="py-4 font-medium text-gray-900">Plásticos (PET)</td>
                                <td class="py-4 text-right font-bold text-emerald-600">15.4 kg</td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 rounded-full">Recibido</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4">
                                    <div class="font-bold">María Ramos</div>
                                    <div class="text-xs text-gray-400">DNI: 49830211</div>
                                </td>
                                <td class="py-4 font-medium text-gray-900">Papel y Cartón</td>
                                <td class="py-4 text-right font-bold text-emerald-600">22.0 kg</td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 rounded-full">Recibido</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4">
                                    <div class="font-bold">Carlos Mendoza</div>
                                    <div class="text-xs text-gray-400">DNI: 08342210</div>
                                </td>
                                <td class="py-4 font-medium text-gray-900">Vidrio</td>
                                <td class="py-4 text-right font-bold text-emerald-600">8.5 kg</td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-amber-50 text-amber-700 rounded-full">Pendiente</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right 1 Col: Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 space-y-6 flex flex-col justify-between">
                <div>
                    <h4 class="text-lg font-bold text-usat-blue mb-2">Acciones Rápidas</h4>
                    <p class="text-xs text-gray-400">Registra actividades y alertas en tiempo real.</p>
                </div>

                <div class="space-y-4">
                    <!-- Action: Registrar Reciclaje -->
                    <button class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl flex items-center justify-center space-x-2 transition shadow-lg shadow-emerald-600/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Registrar Reciclaje</span>
                    </button>

                    <!-- Action: Crear Alerta -->
                    <button class="w-full bg-usat-gold hover:bg-amber-600 active:bg-amber-700 text-white font-bold py-3.5 px-4 rounded-xl flex items-center justify-center space-x-2 transition shadow-lg shadow-amber-600/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Crear Alerta</span>
                    </button>

                    <!-- Action: Ver Rutas -->
                    <button class="w-full bg-usat-blue hover:bg-blue-800 active:bg-blue-900 text-white font-bold py-3.5 px-4 rounded-xl flex items-center justify-center space-x-2 transition shadow-lg shadow-blue-900/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        <span>Ver Mapa de Rutas</span>
                    </button>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>Estado del Servidor</span>
                    <span class="flex items-center text-emerald-500 font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 me-1.5 animate-pulse"></span>
                        En línea
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
