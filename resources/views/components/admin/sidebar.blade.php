@php
    $currentRoute = request()->route()?->getName() ?? '';
    $role = Auth::user()->role;
    $allNavItems = [
        ['label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'route' => 'dashboard', 'badge' => null, 'role' => 'all'],
        ['label' => 'Pesanan Masuk', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'route' => 'kasir.orders', 'badge' => null, 'role' => 'all'],
        ['label' => 'Kelola Menu', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'route' => 'admin.menu-items.index', 'badge' => null, 'role' => 'all'],
        ['label' => 'Paket Promo', 'icon' => 'M11.5 3l2.5 5 5 .5-3.5 3.5 1 5.5-5-2.5-5 2.5 1-5.5L4 8.5l5-.5L11.5 3z', 'route' => 'admin.packages.index', 'badge' => null, 'role' => 'all'],
        ['label' => 'Meja & QR', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'route' => 'admin.tables.index', 'badge' => null, 'role' => 'admin'],
        ['label' => 'Riwayat Bayar', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'route' => 'admin.payments', 'badge' => null, 'role' => 'admin'],
        ['label' => 'Laporan', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'route' => 'admin.reports', 'badge' => null, 'role' => 'admin'],
        ['label' => 'Pengaturan', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'route' => 'admin.settings', 'badge' => null, 'role' => 'all'],
    ];
    $navItems = array_filter($allNavItems, fn($item) => $item['role'] === 'all' || $item['role'] === $role);
@endphp

<div class="flex flex-col h-full">
    @php $sdLogoPath = setting('logo'); @endphp
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-[#6D4C41]/30">
        @if ($sdLogoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($sdLogoPath))
            <img src="{{ Storage::url($sdLogoPath) }}" alt="Logo" class="w-12 h-12 rounded-xl object-contain bg-white/10">
        @else
        <svg class="w-8 h-8 text-[#6D4C41]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
        </svg>
        @endif
        <div>
            <h1 class="text-white font-bold text-lg leading-tight">{{ setting('nama_warung', 'Soto Seger Boyolali Pak Antok') }}</h1>
            <p class="text-[#6D4C41] text-xs">{{ setting('deskripsi', 'Warung Soto UMKM') }}</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">
        @foreach ($navItems as $item)
            @php
                $segments = explode('.', $item['route']);
                $last = end($segments);
                $prefix = in_array($last, ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
                    ? implode('.', array_slice($segments, 0, -1))
                    : $item['route'];
                $isActive = $currentRoute === $item['route'] || str_starts_with($currentRoute, $prefix . '.');
                $href = $item['route'] === '#' ? '#' : route($item['route'], [], false);
            @endphp
            @if ($item['route'] === '#')
                <span class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-[#9CA3AF] rounded-lg cursor-not-allowed opacity-60">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if ($item['badge'])
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                    @endif
                </span>
            @else
                <a href="{{ $href }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-150
                   {{ $isActive ? 'bg-[#6D4C41]/15 text-[#6D4C41] sidebar-link-active' : 'text-[#9CA3AF] hover:bg-[#6D4C41]/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if ($item['badge'])
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- User footer --}}
    <div class="border-t border-[#6D4C41]/30 p-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#6D4C41] flex items-center justify-center text-[#FFF3E0] font-bold text-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-[#9CA3AF] text-xs">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-[#9CA3AF] hover:text-white transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
