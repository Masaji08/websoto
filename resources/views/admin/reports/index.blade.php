@php $title = 'Laporan'; @endphp

@push('actions')
    <div class="flex gap-1 flex-wrap items-center">
        <a href="{{ route('admin.reports', ['period' => 'today']) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'today' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Hari Ini</a>
        <a href="{{ route('admin.reports', ['period' => 'week']) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'week' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Minggu Ini</a>
        <a href="{{ route('admin.reports', ['period' => 'month']) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'month' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Bulan Ini</a>
        <div class="w-px h-6 bg-gray-200 mx-1"></div>
        <a href="{{ route('admin.reports.export-pdf', ['period' => $period]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('admin.reports.export-excel', ['period' => $period]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
        <a href="{{ route('admin.reports.export', ['period' => $period]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
    </div>
@endpush

<x-layouts.admin>
    <div class="space-y-6">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-[#FF8C42] mt-1">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $orderCount }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Rata-rata per Pesanan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($avgPerOrder, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Daily Orders Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold mb-4 text-gray-900">Pendapatan 30 Hari Terakhir</h3>
            <div class="h-64 relative">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

        {{-- Top Selling Menu Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Menu Terlaris</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="text-left px-6 py-3 font-medium">Nama Menu</th>
                            <th class="text-left px-6 py-3 font-medium">Kategori</th>
                            <th class="text-center px-6 py-3 font-medium">Jumlah Terjual</th>
                            <th class="text-right px-6 py-3 font-medium">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($topItems as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3.5 font-medium text-gray-900">{{ $item->menuItem?->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3.5 text-gray-500">{{ $item->menuItem?->category?->name ?? '-' }}</td>
                                <td class="px-6 py-3.5 text-center font-semibold text-gray-900">{{ $item->total_qty }}</td>
                                <td class="px-6 py-3.5 text-right font-semibold text-gray-900">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada data penjualan untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Breakdown Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Breakdown Pembayaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="text-left px-6 py-3 font-medium">Metode</th>
                            <th class="text-center px-6 py-3 font-medium">Jumlah Transaksi</th>
                            <th class="text-right px-6 py-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($paymentBreakdown as $pb)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $pb->payment_method === 'qris' ? 'bg-[#FF8C42]' : ($pb->payment_method === 'bank_transfer' ? 'bg-blue-500' : 'bg-yellow-500') }}"></span>
                                        {{ $paymentLabels[$pb->payment_method] ?? ucfirst($pb->payment_method) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-gray-900">{{ $pb->count }}x</td>
                                <td class="px-6 py-3.5 text-right font-semibold text-gray-900">Rp {{ number_format($pb->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada pembayaran untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartLabels = @json($chartData['labels']);
        const chartTotals = @json($chartData['totals']);
        const maxVal = Math.max(...chartTotals, 1);

        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: chartTotals,
                    backgroundColor: '#FF8C42',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.ceil(maxVal * 1.1) || 1,
                        ticks: {
                            callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 15 },
                    }
                }
            }
        });
    </script>
    @endpush
</x-layouts.admin>
