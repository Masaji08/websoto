<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="theme()" :class="{ 'dark': isDark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ setting('nama_warung', 'Soto Seger Boyolali Pak Antok') }} — {{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .dark body { background-color: #1a1a2e; }
        .dark main { background-color: #1a1a2e; }
    </style>
</head>
<body class="font-['Plus_Jakarta_Sans'] antialiased bg-[#FFFFFF] dark:bg-gray-900">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" @@click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-cloak></div>

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-60 bg-[#4E342E] dark:bg-gray-800 flex-shrink-0 transform -translate-x-full transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto"
               :class="{ 'translate-x-0': sidebarOpen }">
            @include('components.admin.sidebar')
        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            @include('components.admin.topbar')

            {{-- Error Alert --}}
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition-all duration-300 ease-out"
                    x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition-all duration-200 ease-in" x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-0"
                    class="fixed top-4 right-4 z-50 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl shadow-lg text-sm flex items-center gap-3" x-cloak>
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                    <button @@click="show = false" class="ml-2 text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('theme', () => ({
                isDark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),

                init() {
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                    }
                },

                toggleDark() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    document.documentElement.classList.toggle('dark', this.isDark);
                },
            }));

            Alpine.data('dashboard', () => ({}));
        });
    </script>
</body>
</html>
