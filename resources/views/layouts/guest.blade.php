<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="theme()" :class="{ 'dark': isDark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ setting('nama_warung', 'Soto Seger Boyolali Pak Antok') }} — Masuk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .bg-pattern { background-color: #FFF3E0; background-image: radial-gradient(circle at 1px 1px, #FF8C42 1px, transparent 0); background-size: 24px 24px; }
        .dark .bg-pattern { background-color: #1a1a2e; background-image: radial-gradient(circle at 1px 1px, #FF8C42 1px, transparent 0); background-size: 24px 24px; }
    </style>
</head>
<body class="font-['Plus_Jakarta_Sans'] antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-pattern dark:bg-gray-900 px-4">
        <div class="w-full sm:max-w-md">
            {{-- Brand --}}
            @php $logoPath = setting('logo'); @endphp
            <div class="text-center mb-8">
                @if ($logoPath && (\App\Services\CloudinaryService::isCloudinaryUrl($logoPath) || \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)))
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-lg mb-3 overflow-hidden p-1">
                        <img src="{{ \App\Services\CloudinaryService::getImageUrl($logoPath) }}" alt="{{ setting('nama_warung') }}" class="w-full h-full object-contain">
                    </div>
                @else
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#FF8C42] to-[#6D4C41] shadow-lg mb-3">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                @endif
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ setting('nama_warung', 'Soto Seger Boyolali Pak Antok') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ setting('deskripsi', 'Warung Soto UMKM') }}</p>
            </div>

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-[0_8px_30px_-5px_rgba(0,0,0,0.08)] border border-[#E5E0D8] dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5">
                    {{ $slot }}
                </div>
            </div>

            {{-- Dark mode toggle --}}
            <div class="text-center mt-6">
                <button @@click="toggleDark()" class="inline-flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg x-show="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-text="isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('theme', () => ({
                isDark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
                init() {
                    if (this.isDark) document.documentElement.classList.add('dark');
                },
                toggleDark() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    document.documentElement.classList.toggle('dark', this.isDark);
                },
            }));
        });
    </script>
</body>
</html>
