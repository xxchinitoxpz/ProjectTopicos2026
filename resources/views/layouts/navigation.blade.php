<nav class="bg-white border-b border-gray-200/80 sticky top-0 z-30 shadow-sm h-16 flex items-center justify-between px-6">
    <!-- Left side: Hamburger button (mobile) and title -->
    <div class="flex items-center space-x-4">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none lg:hidden transition duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <!-- Page Title -->
        <h2 class="text-base sm:text-lg font-bold text-usat-blue tracking-tight">
            {{ $title ?? 'Panel de Control' }}
        </h2>
    </div>

    <!-- Right side: User Dropdown -->
    <div class="flex items-center space-x-4">
        <!-- User Welcome Message (desktop) -->
        <span class="text-xs text-gray-500 font-medium hidden md:inline-block">
            Bienvenido, <span class="font-semibold text-gray-700">{{ Auth::user()->name }}</span>
        </span>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-semibold rounded-xl text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                    <div class="flex items-center space-x-2">
                        <!-- User icon placeholder -->
                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="hidden sm:inline-block text-sm">{{ Auth::user()->name }}</span>
                    </div>

                    <div class="ms-1">
                        <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2 border-b border-gray-100 lg:hidden">
                    <div class="font-semibold text-sm text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>
