<div data-order-id="{{ $order->id }}"
    class="bg-white border border-gray-100 rounded-lg p-3.5 shadow-sm hover:shadow-md transition-all duration-200 {{ $order->status === 'pending' ? 'animate-pulse-border' : '' }}">
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-[#FF8C42]/10 flex items-center justify-center font-bold text-[#FF8C42] text-sm">#</span>
            <div>
                <span class="font-bold text-sm text-gray-900">#{{ $order->order_number }}</span>
                <div class="flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-xs text-gray-500">{{ $order->table->name }}</span>
                </div>
            </div>
        </div>
        <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full whitespace-nowrap" data-time="{{ $order->created_at }}">
            {{ $order->created_at->diffForHumans() }}
        </span>
    </div>
    <div class="flex flex-col gap-1 mb-2 pl-10">
        @foreach ($order->items as $item)
            <span class="text-xs text-gray-600 flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-700 text-[10px] font-bold">{{ $item->quantity }}</span>
                <span>{{ $item->menuItem->name }}</span>
            </span>
        @endforeach
    </div>
    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
        <span class="font-bold text-sm text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                            {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                        </span>
    </div>
    @php
        $pendingLabel = $order->payment_method === 'cash' ? 'Konfirmasi & Selesai' : 'Konfirmasi';
        $statusActions = [
            'pending' => ['next' => 'confirmed', 'label' => $pendingLabel, 'gradient' => 'from-yellow-500 to-amber-500', 'hover' => 'from-yellow-600 to-amber-600'],
            'confirmed' => ['next' => 'processing', 'label' => 'Proses', 'gradient' => 'from-blue-500 to-indigo-500', 'hover' => 'from-blue-600 to-indigo-600'],
            'processing' => ['next' => 'ready', 'label' => 'Siap', 'gradient' => 'from-green-500 to-emerald-500', 'hover' => 'from-green-600 to-emerald-600'],
            'ready' => ['next' => 'completed', 'label' => 'Selesai', 'gradient' => 'from-gray-500 to-slate-500', 'hover' => 'from-gray-600 to-slate-600'],
        ];
    @endphp
    @if (isset($statusActions[$order->status]))
        <form action="{{ route('kasir.orders.status', $order) }}" method="POST" class="mt-2"
            onsubmit="event.preventDefault(); var btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<svg class=\'animate-spin h-4 w-4 mx-auto\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\' fill=\'none\'/><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'/></svg>'; this.submit()">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="{{ $statusActions[$order->status]['next'] }}">
            <button class="w-full bg-gradient-to-r {{ $statusActions[$order->status]['gradient'] }} text-white text-xs py-2.5 min-h-[44px] rounded-lg font-semibold hover:{{ $statusActions[$order->status]['hover'] }} transition-all shadow-sm hover:shadow-md active:scale-[0.98]">
                {{ $statusActions[$order->status]['label'] }}
            </button>
        </form>
    @endif
    @if ($order->payment_status !== 'paid')
        <form action="{{ route('kasir.orders.mark-paid', $order) }}" method="POST" class="mt-1">
            @csrf
            <button class="w-full text-xs py-2.5 min-h-[44px] rounded-lg font-semibold border border-green-400 text-green-600 hover:bg-green-50 transition-all">
                Tandai Lunas
            </button>
        </form>
    @endif
    <a href="{{ route('kasir.orders.show', $order) }}" class="block text-center text-xs text-[#FF8C42] mt-1.5 font-medium hover:underline">Lihat Detail →</a>
</div>
