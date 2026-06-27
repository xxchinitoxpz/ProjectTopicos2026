<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Turbo 8 Page Refreshes -->
        <meta name="turbo-refresh-method" content="morph">
        <meta name="turbo-refresh-scroll" content="preserve">
    </head>
    <body class="font-sans antialiased bg-usat-light text-gray-850" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-y-auto bg-usat-light">
                <!-- Navbar -->
                @include('layouts.navigation')

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl shadow-sm text-emerald-800 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl shadow-sm text-red-800 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-500 hover:text-red-700 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Modal Global -->
        <div x-data="{ 
                 open: false, 
                 modalSize: 'max-w-lg',
                 closeModal() { 
                     this.open = false; 
                     const frame = document.getElementById('modal');
                     if (frame) { frame.src = ''; frame.innerHTML = ''; }
                 } 
             }"
             @keydown.escape.window="closeModal()"
             @turbo:frame-render.window="if ($event.target.id === 'modal') { open = true; const size = $event.target.firstElementChild?.getAttribute('data-modal-size') || $event.target.getAttribute('data-modal-size'); modalSize = size ? size : 'max-w-lg'; }"
             @turbo:submit-end.window="if ($event.detail.success) closeModal()"
             x-show="open"
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog"
             aria-modal="true"
        >
            <!-- Backdrop -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                 @click="closeModal()"
            ></div>

            <!-- Modal Content -->
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     :class="modalSize"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full"
                >
                    <turbo-frame id="modal"></turbo-frame>
                </div>
            </div>
        </div>

        <script>
            (() => {
                const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
                const parseErrors = (errors) => Object.values(errors || {}).flat().filter(Boolean);

                const setFeedback = (form, messages) => {
                    const box = form.querySelector('[data-ajax-feedback]');
                    if (!box) return;

                    if (!messages.length) {
                        box.innerHTML = '';
                        box.classList.add('hidden');
                        return;
                    }

                    box.innerHTML = `
                        <ul class="space-y-1">
                            ${messages.map((message) => `<li>${message}</li>`).join('')}
                        </ul>
                    `;
                    box.classList.remove('hidden');
                };

                const setSubmitting = (form, submitting) => {
                    const button = form.querySelector('[type="submit"]');
                    if (!button) return;

                    button.disabled = submitting;
                    button.classList.toggle('opacity-60', submitting);
                    button.classList.toggle('cursor-not-allowed', submitting);
                };

                document.addEventListener('submit', async (event) => {
                    const form = event.target.closest('form[data-ajax-form]');
                    if (!form) return;

                    event.preventDefault();
                    setFeedback(form, []);
                    setSubmitting(form, true);

                    try {
                        const response = await fetch(form.action, {
                            method: form.method || 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json',
                            },
                            body: new FormData(form),
                        });

                        if (response.ok) {
                            const data = await response.json().catch(() => ({}));
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }
                            window.location.reload();
                            return;
                        }

                        if (response.status === 422) {
                            const data = await response.json();
                            setFeedback(form, parseErrors(data.errors));
                            return;
                        }

                        const data = await response.json().catch(() => ({}));
                        setFeedback(form, [data.message || 'No se pudo completar la operacion.']);
                    } catch (error) {
                        setFeedback(form, ['No se pudo conectar con el servidor.']);
                    } finally {
                        setSubmitting(form, false);
                    }
                });
            })();
        </script>
    </body>
</html>
