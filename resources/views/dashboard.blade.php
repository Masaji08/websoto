@php
    $title = 'Dashboard';

    // Stat cards
    $stats = [
        ['title' => 'Pendapatan Hari Ini', 'value' => 'Rp ' . number_format($revenueToday, 0, ',', '.'), 'change' => $orderTodayCount > 0 ? "Dari $orderTodayCount pesanan" : 'Belum ada pesanan', 'trend' => $revenueToday > 0 ? 'up' : 'neutral', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'primary'],
        ['title' => 'Total Pesanan Hari Ini', 'value' => "$orderTodayCount pesanan", 'change' => $orderTodayCount > 0 ? ($recentOrders->first()?->created_at->diffForHumans() ?? 'Baru saja') : 'Belum ada', 'trend' => $orderTodayCount > 0 ? 'up' : 'neutral', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'color' => 'green'],
        ['title' => 'Pesanan Aktif', 'value' => "$activeOrders pesanan", 'change' => 'Sedang diproses', 'trend' => $activeOrders > 0 ? 'up' : 'neutral', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'accent'],
        ['title' => 'Menu Tersedia', 'value' => "$availableMenu dari $totalMenu", 'change' => ($totalMenu - $availableMenu) . ' tidak tersedia', 'trend' => $availableMenu === $totalMenu ? 'up' : 'down', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'color' => 'blue'],
    ];

    // Payment method colors/icons
    $paymentMeta = [
        'qris' => ['color' => '#FF8C42'],
        'bank_transfer' => ['color' => '#3B82F6'],
        'cash' => ['color' => '#F59E0B'],
    ];
    $paymentLabels = [
        'qris' => 'QRIS', 'bank_transfer' => 'Transfer', 'cash' => 'Tunai',
    ];
@endphp

<x-layouts.admin>
    <div class="space-y-6 animate-fade-in">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($stats as $s)
                <div class="animate-slide-up animation-delay-{{ ($loop->index + 1) * 100 }}">
                    <x-admin.stat-card :title="$s['title']" :value="$s['value']" :change="$s['change']" :trend="$s['trend']" :icon="$s['icon']" :color="$s['color']" />
                </div>
            @endforeach
        </div>

        {{-- Main grid: Orders table + Payments --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Orders --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm hover-card">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Pesanan Terbaru</h3>
                    <a href="{{ route('kasir.orders') }}" class="text-xs text-[#FF8C42] font-medium hover:underline">Lihat Semua Pesanan →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                                <th class="text-left px-5 py-3 font-medium">No. Order</th>
                                <th class="text-left px-5 py-3 font-medium">Meja</th>
                                <th class="text-left px-5 py-3 font-medium">Items</th>
                                <th class="text-left px-5 py-3 font-medium">Total</th>
                                <th class="text-left px-5 py-3 font-medium">Bayar</th>
                                <th class="text-left px-5 py-3 font-medium">Status</th>
                                <th class="text-right px-5 py-3 font-medium">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($recentOrders as $order)
                                @php
                                    $statusColors = ['pending' => 'yellow', 'confirmed' => 'blue', 'processing' => 'blue', 'ready' => 'green', 'completed' => 'emerald', 'cancelled' => 'red'];
                                    $statusIcons = ['pending' => 'pending', 'confirmed' => 'processing', 'processing' => 'processing', 'ready' => 'ready', 'completed' => 'completed', 'cancelled' => 'cancelled'];
                                    $statusLabels = ['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                                    $sc = $statusColors[$order->status] ?? 'gray';
                                    $badge = match($sc) {
                                        'emerald' => 'bg-emerald-50 text-emerald-700',
                                        'blue' => 'bg-blue-50 text-blue-700',
                                        'yellow' => 'bg-yellow-50 text-yellow-700',
                                        'green' => 'bg-green-50 text-green-700',
                                        'red' => 'bg-red-50 text-red-700',
                                        default => 'bg-gray-50 text-gray-700',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 font-semibold text-gray-900">#{{ $order->order_number }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $order->table?->name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $order->items->count() }} item</td>
                                    <td class="px-5 py-3 font-medium text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'pending' ? 'bg-yellow-500' : ($order->status === 'cancelled' ? 'bg-red-500' : 'bg-green-500') }}"></span>
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada pesanan hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Payment Breakdown --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover-card">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Pembayaran Hari Ini</h3>
                </div>
                <div class="p-5 space-y-4">
                            @forelse ($paymentBreakdown as $pb)
                        @php
                            $meta = $paymentMeta[$pb->payment_method] ?? ['color' => '#6B7280'];
                            $totalPembayaran = $paymentBreakdown->sum('total');
                            $pct = $totalPembayaran > 0 ? round(($pb->total / $totalPembayaran) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" style="background:{{ $meta['color'] }}"></span>
                                    <span class="text-sm font-medium text-gray-700">{{ $paymentLabels[$pb->payment_method] ?? ucfirst($pb->payment_method) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($pb->total, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-400 ml-1">({{ $pb->count }}x)</span>
                                </div>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background: {{ $meta['color'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada pembayaran hari ini.</p>
                    @endforelse
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total {{ $paymentTransactionCount }} transaksi</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($paymentBreakdown->sum('total'), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom grid: Top items + Table status --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Menu Items --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover-card">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Menu Terlaris</h3>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($topMenuItems as $i)
                        @php $rank = $loop->iteration; @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $rank === 1 ? 'bg-yellow-100 text-yellow-700' : ($rank === 2 ? 'bg-gray-100 text-gray-600' : ($rank === 3 ? 'bg-orange-100 text-orange-700' : 'bg-[#FFFFFF] text-gray-400')) }}">
                                {{ $rank }}
                            </span>
                            <span class="flex-1 text-sm text-gray-700">{{ $i->menuItem?->name ?? 'Unknown' }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $i->total_qty }} porsi</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-4">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Table Status --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover-card">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Status Meja</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-5 gap-3">
                        @foreach ($tables as $t)
                            @php
                                $hasOrder = in_array($t->id, $activeOrderTableIds);
                                $statusCode = !$t->is_active ? 0 : ($hasOrder ? 2 : 1);
                                $bgClass = match($statusCode) { 0 => 'bg-gray-100', 1 => 'bg-emerald-50 border-emerald-200', 2 => 'bg-orange-50 border-orange-200' };
                                $dotClass = match($statusCode) { 0 => 'bg-gray-400', 1 => 'bg-emerald-500', 2 => 'bg-orange-500' };
                                $label = match($statusCode) { 0 => 'Tidak Aktif', 1 => 'Kosong', 2 => 'Ada Pesanan' };
                            @endphp
                            <div class="rounded-lg border-2 p-2.5 text-center transition-all {{ $bgClass }}" title="{{ $t->name }}: {{ $label }}">
                                <div class="flex justify-center mb-1">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $dotClass }}"></span>
                                </div>
                                <p class="text-xs font-semibold text-gray-700">{{ $t->name }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-4 mt-4 text-xs text-gray-500 justify-center">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Kosong</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Ada Pesanan</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Tidak Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
