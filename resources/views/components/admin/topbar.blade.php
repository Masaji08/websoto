<header x-data="{ clock: '' }" x-init="clock = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); setInterval(() => { clock = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }, 30000)" class="bg-white dark:bg-gray-800 border-b border-[#FF8C42]/10 px-4 md:px-6 py-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-[#FF8C42]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div>
                <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-sm md:text-base">{{ $title ?? 'Dashboard' }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @stack('actions')
            <button @@click="toggleDark()" class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle dark mode">
                <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="clock"></span>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <div class="w-7 h-7 rounded-full bg-[#FF8C42] flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <span class="hidden sm:block text-gray-700 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </div>
</header>
