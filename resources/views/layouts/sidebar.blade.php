<!-- Sidebar Container -->
<aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
       x-data="{ 
           openVehicles: {{ request()->routeIs('admin.vehicle*') || request()->routeIs('admin.brand*') ? 'true' : 'false' }},
           openStaff: {{ request()->routeIs('admin.staff*') || request()->routeIs('admin.staff-type*') || request()->routeIs('admin.contract*') || request()->routeIs('admin.shift*') || request()->routeIs('admin.vacation*') || request()->routeIs('admin.assistance*') || request()->routeIs('admin.holiday*') ? 'true' : 'false' }},
           openSchedule: {{ request()->routeIs('admin.zone*') || request()->routeIs('admin.staff-group*') || request()->routeIs('admin.planning*') ? 'true' : 'false' }},
           openChanges: false,
           openUsers: false
       }"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-usat-blue text-white transition-transform duration-300 ease-in-out transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-2xl border-r border-blue-950">
    
    <!-- Sidebar Header / Logo -->
    <div class="h-16 flex items-center px-6 border-b border-blue-800/50 bg-blue-950/40">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <x-application-logo class="w-9 h-9 text-emerald-400 group-hover:scale-105 transition-transform duration-200" />
            <div>
                <h1 class="font-extrabold text-lg tracking-tight leading-none text-white">RSU <span class="text-emerald-400">Recicla</span></h1>
                <span class="text-[10px] text-blue-300 font-semibold tracking-wider uppercase">USAT - Sistemas</span>
            </div>
        </a>
    </div>

    <!-- Navigation Items -->
    <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
        <!-- Item: Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-emerald-500/10 text-emerald-400 border-l-4 border-emerald-500' : 'text-blue-100 hover:bg-blue-850 hover:text-white' }}">
            <svg class="w-5 h-5 me-3 {{ request()->routeIs('dashboard') ? 'text-emerald-400' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
            </svg>
            Dashboard
        </a>

        <!-- Section: Gestión de Vehículos (Collapsible) -->
        <div class="space-y-1">
            <button @click="openVehicles = !openVehicles" 
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-xl text-blue-100 hover:bg-blue-850 hover:text-white transition duration-150 focus:outline-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Gestión de Vehículos</span>
                </div>
                <svg :class="{'rotate-180': openVehicles}" class="w-4 h-4 text-blue-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openVehicles" x-transition.opacity class="ps-8 pe-2 py-1 space-y-1">
                <a href="{{ route('admin.vehicle-color.index') }}" 
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.vehicle-color.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Colores
                </a>
                <a href="{{ route('admin.brand.index') }}" 
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.brand.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Marcas
                </a>
                <a href="{{ route('admin.brand-model.index') }}" 
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.brand-model.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Modelos
                </a>
                <a href="{{ route('admin.vehicle-type.index') }}" 
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.vehicle-type.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Tipos de Vehículos
                </a>
                <a href="{{ route('admin.vehicle.index') }}" 
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.vehicle.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Vehículos
                </a>
                <a href="#" class="block py-2 px-3 text-xs font-bold rounded-lg text-blue-300 hover:text-white hover:bg-blue-850">
                    • Mantenimientos
                </a>
            </div>
        </div>

        <!-- Section: Gestión de Personal (Collapsible) -->
        <div class="space-y-1">
            <button @click="openStaff = !openStaff" 
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-xl text-blue-100 hover:bg-blue-850 hover:text-white transition duration-150 focus:outline-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Gestión de Personal</span>
                </div>
                <svg :class="{'rotate-180': openStaff}" class="w-4 h-4 text-blue-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="openStaff" class="ps-8 pe-2 py-1 space-y-1">
                <a href="{{ route('admin.staff-type.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.staff-type.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Tipos de Personal
                </a>
                <a href="{{ route('admin.staff.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.staff.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Personal
                </a>
                <a href="{{ route('admin.contract.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.contract.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Contratos
                </a>
                <a href="{{ route('admin.shift.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.shift.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Turnos
                </a>
                <a href="{{ route('admin.vacation.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.vacation.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Vacaciones
                </a>
                <a href="{{ route('admin.assistance.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.assistance.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Asistencias
                </a>
                <a href="{{ route('admin.holiday.index') }}" class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.holiday.*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Feriados
                </a>
            </div>
        </div>

        <!-- Section: Programación (Collapsible) -->
        <div class="space-y-1">
            <button @click="openSchedule = !openSchedule" 
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-xl text-blue-100 hover:bg-blue-850 hover:text-white transition duration-150 focus:outline-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Programación</span>
                </div>
                <svg :class="{'rotate-180': openSchedule}" class="w-4 h-4 text-blue-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="openSchedule" class="ps-8 pe-2 py-1 space-y-1">
                <a href="{{ route('admin.zone.index') }}"
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.zone*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Zonas
                </a>
                <a href="{{ route('admin.staff-group.index') }}"
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.staff-group*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Grupos de Personal
                </a>
                <a href="{{ route('admin.planning.index') }}"
                   class="block py-2 px-3 text-xs font-bold rounded-lg transition {{ request()->routeIs('admin.planning*') ? 'text-emerald-400 bg-blue-900/40' : 'text-blue-200 hover:text-white hover:bg-blue-850' }}">
                    • Programación de Grupos
                </a>
                <a href="#" class="block py-2 px-3 text-xs font-bold rounded-lg text-blue-300 hover:text-white hover:bg-blue-850">
                    • Rutas y Horarios
                </a>
            </div>
        </div>

        <!-- Section: Gestión de Cambios (Collapsible) -->
        <div class="space-y-1">
            <button @click="openChanges = !openChanges" 
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-xl text-blue-100 hover:bg-blue-850 hover:text-white transition duration-150 focus:outline-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Gestión de Cambios</span>
                </div>
                <svg :class="{'rotate-180': openChanges}" class="w-4 h-4 text-blue-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="openChanges" class="ps-8 pe-2 py-1 space-y-1">
                <a href="#" class="block py-2 px-3 text-xs font-bold rounded-lg text-blue-200 hover:text-white hover:bg-blue-850">
                    • Solicitudes
                </a>
            </div>
        </div>

        <!-- Section: Gestión de Usuarios (Collapsible) -->
        <div class="space-y-1">
            <button @click="openUsers = !openUsers" 
                    class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-xl text-blue-100 hover:bg-blue-850 hover:text-white transition duration-150 focus:outline-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Gestión de Usuarios</span>
                </div>
                <svg :class="{'rotate-180': openUsers}" class="w-4 h-4 text-blue-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="openUsers" class="ps-8 pe-2 py-1 space-y-1">
                <a href="#" class="block py-2 px-3 text-xs font-bold rounded-lg text-blue-200 hover:text-white hover:bg-blue-850">
                    • Usuarios
                </a>
                <a href="#" class="block py-2 px-3 text-xs font-bold rounded-lg text-blue-200 hover:text-white hover:bg-blue-850">
                    • Tipos de Usuario
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-blue-800/50 bg-blue-950/20 text-xs text-blue-300 text-center">
        &copy; 2026 Project Topicos
    </div>
</aside>

<!-- Background Overlay for Mobile Sidebar -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
