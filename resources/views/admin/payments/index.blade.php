@php $title = 'Riwayat Bayar'; @endphp

@push('actions')
    <div class="flex gap-1 flex-wrap">
        <a href="{{ route('admin.payments', ['period' => 'today', 'method' => $paymentMethod]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'today' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Hari Ini</a>
        <a href="{{ route('admin.payments', ['period' => 'week', 'method' => $paymentMethod]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'week' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Minggu Ini</a>
        <a href="{{ route('admin.payments', ['period' => 'month', 'method' => $paymentMethod]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'month' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Bulan Ini</a>
        <a href="{{ route('admin.payments', ['period' => 'all', 'method' => $paymentMethod]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $period === 'all' ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Semua</a>
    </div>
@endpush

<x-layouts.admin>
    <div class="space-y-5">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary->total_transactions ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-[#FF8C42] mt-1">Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Metode Pembayaran</p>
                <div class="mt-2 space-y-1.5">
                    @php
                        $paymentLabels = ['qris' => 'QRIS', 'cash' => 'Tunai'];
                        $paymentColors = ['qris' => '#FF8C42', 'cash' => '#F59E0B'];
                    @endphp
                    @foreach ($methodTotals as $mt)
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" style="background:{{ $paymentColors[$mt->payment_method] ?? '#6B7280' }}"></span>
                                {{ $paymentLabels[$mt->payment_method] ?? ucfirst($mt->payment_method) }}
                            </span>
                            <span class="font-medium text-gray-700">Rp {{ number_format($mt->total, 0, ',', '.') }} ({{ $mt->count }})</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filter by payment method --}}
        <div class="flex gap-1 flex-wrap">
            <a href="{{ route('admin.payments', ['period' => $period, 'method' => '']) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ !$paymentMethod ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Semua Metode</a>
            @foreach (['qris', 'cash'] as $m)
                <a href="{{ route('admin.payments', ['period' => $period, 'method' => $m]) }}" class="px-3 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium transition-colors {{ $paymentMethod === $m ? 'bg-[#FF8C42] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">{{ $paymentLabels[$m] ?? ucfirst($m) }}</a>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="text-left px-5 py-3 font-medium">No. Order</th>
                            <th class="text-left px-5 py-3 font-medium">Meja</th>
                            <th class="text-left px-5 py-3 font-medium">Metode</th>
                            <th class="text-left px-5 py-3 font-medium">Total</th>
                            <th class="text-left px-5 py-3 font-medium">Status</th>
                            <th class="text-right px-5 py-3 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($payments as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 font-semibold text-gray-900">#{{ $order->order_number }}</td>
                                <td class="px-5 py-4 text-gray-600">{{ $order->table?->name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium">
                                        <span class="w-2 h-2 rounded-full" style="background:{{ $paymentColors[$order->payment_method] ?? '#6B7280' }}"></span>
                                        {{ $paymentLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                        {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right text-gray-400 text-xs">{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada transaksi pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($payments->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
