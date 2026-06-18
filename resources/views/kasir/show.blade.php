@php $title = 'Detail Pesanan #' . $order->order_number; @endphp

<x-layouts.admin>
    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Nomor Pesanan</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-0.5">#{{ $order->order_number }}</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                        {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            {{-- Table Info --}}
            <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm text-gray-700 font-medium">{{ $order->table?->name ?? '-' }}</span>
                <span class="text-xs text-gray-400 ml-auto">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>

            {{-- Items --}}
            <div class="px-6 py-4">
                <h4 class="font-semibold text-gray-900 text-sm mb-3">Item Pesanan</h4>
                <div class="space-y-2">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">{{ $item->quantity }}</span>
                                <span class="text-sm text-gray-700">{{ $item->menuItem->name }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($item->menuItem->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Total --}}
                <div class="flex items-center justify-between pt-4 mt-2 border-t border-gray-200">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-lg text-[#FF8C42]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Notes --}}
            @if ($order->notes)
                <div class="px-6 py-3 border-t border-gray-50 bg-gray-50/50">
                    <p class="text-xs text-gray-500 font-medium">Catatan</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- Actions --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                <a href="{{ route('kasir.orders') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    &larr; Kembali
                </a>
                <div class="flex items-center gap-2">
                    @if ($order->payment_status !== 'paid')
                        <form action="{{ route('kasir.orders.mark-paid', $order) }}" method="POST">
                            @csrf
                            <button class="text-sm px-4 py-2 rounded-lg font-semibold border-2 border-green-500 text-green-600 hover:bg-green-50 transition-all">
                                Tandai Lunas
                            </button>
                        </form>
                    @endif
                    @php
                        $pendingLabel = $order->payment_method === 'cash' ? 'Konfirmasi & Selesai' : 'Konfirmasi';
                        $statusActions = [
                            'pending' => ['next' => 'confirmed', 'label' => $pendingLabel, 'gradient' => 'from-yellow-500 to-amber-500'],
                            'confirmed' => ['next' => 'processing', 'label' => 'Proses', 'gradient' => 'from-blue-500 to-indigo-500'],
                            'processing' => ['next' => 'ready', 'label' => 'Siap', 'gradient' => 'from-green-500 to-emerald-500'],
                            'ready' => ['next' => 'completed', 'label' => 'Selesai', 'gradient' => 'from-gray-500 to-slate-500'],
                        ];
                    @endphp
                    @if (isset($statusActions[$order->status]))
                        <form action="{{ route('kasir.orders.status', $order) }}" method="POST"
                            onsubmit="event.preventDefault(); var btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<svg class=\'animate-spin h-4 w-4 mx-auto\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\' fill=\'none\'/><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'/></svg>'; this.submit()">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $statusActions[$order->status]['next'] }}">
                            <button class="bg-gradient-to-r {{ $statusActions[$order->status]['gradient'] }} text-white text-sm px-6 py-2 rounded-lg font-semibold hover:shadow-md transition-all shadow-sm active:scale-[0.98]">
                                {{ $statusActions[$order->status]['label'] }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
