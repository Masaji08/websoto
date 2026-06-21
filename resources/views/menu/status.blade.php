<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1,user-scalable=no">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>Pesanan #{{ $order->order_number }} - {{ setting('nama_warung') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFFFFF; margin:0; color:#1F2937; -webkit-font-smoothing:antialiased; }
        .wa-float {
            position:fixed; bottom:24px; right:16px; z-index:55;
            width:56px; height:56px; border-radius:50%;
            background:#25D366; color:white; border:none;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; box-shadow:0 4px 16px rgba(37,211,102,0.4);
            transition:all 0.2s; text-decoration:none;
        }
        .wa-float:hover { transform:scale(1.08); box-shadow:0 6px 20px rgba(37,211,102,0.5); }
        .wa-float:active { transform:scale(0.95); }
    </style>
</head>
<body>
<div x-data="paymentWatcher()" x-init="init()" style="max-width:480px;margin:0 auto;padding:32px 20px;min-height:100vh;text-align:center;">

    @if(session('payment_error'))
    <div style="background:#FCEBEB;border-radius:12px;padding:12px 16px;margin-bottom:20px;color:#A32D2D;font-size:13px;text-align:left;">
        {{ session('payment_error') }}
    </div>
    @endif
    @if(session('success'))
    <div style="background:#D1FAE5;border-radius:12px;padding:12px 16px;margin-bottom:20px;color:#065F46;font-size:13px;text-align:left;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div style="background:#DBEAFE;border-radius:12px;padding:12px 16px;margin-bottom:20px;color:#1E40AF;font-size:13px;text-align:left;">
        {{ session('info') }}
    </div>
    @endif

    {{-- Status icon --}}
    @php $isUnpaidQris = $order->payment_method === 'qris' && $order->payment_status === 'unpaid'; @endphp
    <div style="width:72px;height:72px;border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;
        @if($isUnpaidQris) background:#FEF3C7;
        @elseif($order->status === 'ready' || $order->status === 'completed') background:#D1FAE5;
        @elseif($order->status === 'processing') background:#DBEAFE;
        @else background:#FEF3C7;
        @endif">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            @if($isUnpaidQris) stroke="#6D4C41"
            @elseif($order->status === 'ready' || $order->status === 'completed') stroke="#065F46"
            @elseif($order->status === 'processing') stroke="#1E40AF"
            @else stroke="#6D4C41"
            @endif>
            @if($isUnpaidQris)
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @elseif($order->status === 'ready' || $order->status === 'completed')
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @elseif($order->status === 'processing')
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            @endif
        </svg>
    </div>

    <div style="font-size:22px;font-weight:800;color:#1F2937;">
        @if($order->payment_method === 'qris' && $order->payment_status === 'unpaid')
            Menunggu Pembayaran...
        @elseif($order->status === 'pending')       Pesanan Diterima!
        @elseif($order->status === 'confirmed')  Pesanan Dikonfirmasi
        @elseif($order->status === 'processing') Sedang Dimasak...
        @elseif($order->status === 'ready')      Pesanan Siap Diambil!
        @elseif($order->status === 'completed')  Selesai! Terima Kasih
        @else                                     Pesanan Masuk!
        @endif
    </div>
    <div style="color:#6B7280;font-size:13px;margin-top:4px;">{{ $table->name }}</div>

    {{-- Order Number --}}
    <div style="background:#FFF3E0;border-radius:16px;padding:16px 32px;margin:20px auto;display:inline-block;">
        <div style="font-size:11px;color:#6D4C41;font-weight:600;">NOMOR PESANAN</div>
        <div style="font-size:34px;font-weight:900;color:#FF8C42;">#{{ $order->order_number }}</div>
    </div>

    @unless($isUnpaidQris)
    {{-- Step Tracker --}}
    @php
        $steps = [
            ['key' => 'pending',    'label' => 'Diterima', 'icon' => '✓'],
            ['key' => 'processing', 'label' => 'Dimasak',  'icon' => '◇'],
            ['key' => 'ready',      'label' => 'Siap',     'icon' => '●'],
            ['key' => 'completed',  'label' => 'Selesai',  'icon' => '★'],
        ];
        $order_keys = ['pending', 'confirmed', 'processing', 'ready', 'completed'];
        $cur = array_search($order->status, $order_keys);
    @endphp

    <div style="display:flex;justify-content:center;align-items:center;margin:20px 0;gap:0;">
        @foreach($steps as $i => $step)
            @php
                $si = array_search($step['key'], $order_keys);
                $done = $cur >= $si;
            @endphp
            <div style="text-align:center;">
                <div style="width:38px;height:38px;border-radius:50%;margin:0 auto 4px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;
                    {{ $done ? 'background:#FF8C42;color:#FFF3E0;' : 'background:#F1EFE8;color:#6B7280;' }}">
                    {{ $step['icon'] }}
                </div>
                <div style="font-size:10px;font-weight:600;{{ $done ? 'color:#FF8C42;' : 'color:#6B7280;' }}">
                    {{ $step['label'] }}
                </div>
            </div>
            @if(!$loop->last)
                @php $next_done = $cur > $si; @endphp
                <div style="width:36px;height:2px;margin-bottom:18px;{{ $next_done ? 'background:#FF8C42;' : 'background:#F1EFE8;' }}"></div>
            @endif
        @endforeach
    </div>
    @endunless

    {{-- Order Detail --}}
    <div style="background:#fff;border-radius:16px;border:0.5px solid #E5E0D8;padding:16px;margin:16px 0;text-align:left;">
        <div style="font-size:14px;font-weight:700;color:#1F2937;margin-bottom:12px;">Detail Pesanan</div>
        @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:0.5px solid #F3F4F6;font-size:13px;">
            <span>
                <span style="font-weight:600;color:#1F2937;">{{ $item->menuItem->name }}</span>
                <span style="color:#6B7280;"> x{{ $item->quantity }}</span>
            </span>
            <span style="color:#FF8C42;font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div style="display:flex;justify-content:space-between;padding-top:10px;font-size:15px;font-weight:800;color:#1F2937;">
            <span>Total</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        @if($order->notes)
        <div style="margin-top:8px;font-size:12px;color:#6B7280;padding-top:8px;border-top:0.5px solid #F3F4F6;">
            {{ $order->notes }}
        </div>
        @endif
    </div>

    {{-- Payment Info --}}
    <div style="background:#FFF3E0;border-radius:12px;padding:10px 16px;font-size:13px;color:#FF8C42;font-weight:600;margin-bottom:20px;">
        @if($order->payment_method === 'qris') QRIS
        @else Tunai
        @endif
        @if($order->payment_status === 'paid')
            <span style="color:#3B6D11;"> — Lunas</span>
        @elseif($order->payment_method === 'qris')
            <span> — Menunggu Pembayaran</span>
        @elseif($order->payment_method === 'cash')
            <span> — Bayar ke kasir</span>
        @endif
    </div>

    @if($order->payment_method === 'qris' && $order->payment_status === 'unpaid')
    <div style="text-align:center;margin-bottom:12px;">
        <p style="color:#6B7280;font-size:13px;margin-bottom:12px;">Selesaikan pembayaran agar pesanan diproses.</p>
        <a href="{{ route('menu.order.pay', [$table->slug, $order->order_number]) }}"
           style="display:block;width:100%;background:#FF8C42;color:#FFF3E0;border:none;border-radius:14px;padding:15px;font-size:15px;font-weight:800;text-align:center;cursor:pointer;text-decoration:none;">
            Bayar Sekarang
        </a>

        <form action="{{ route('menu.order.check-payment', [$table->slug, $order->order_number]) }}" method="POST" style="margin-top:10px;">
            @csrf
            <button type="submit" id="btnCekBayar"
                style="display:block;width:100%;background:#6D4C41;color:#FFF3E0;border:none;border-radius:14px;padding:13px;font-size:14px;font-weight:700;text-align:center;cursor:pointer;text-decoration:none;">
                Cek Pembayaran
            </button>
        </form>

        <form action="{{ route('menu.order.cancel', [$table->slug, $order->order_number]) }}" method="POST" onsubmit="return confirm('Batalkan pesanan #{{ $order->order_number }}?')" style="margin-top:10px;">
            @csrf
            <button type="submit"
                style="display:block;width:100%;background:transparent;color:#6B7280;border:1.5px solid #E5E0D8;border-radius:14px;padding:13px;font-size:14px;font-weight:600;text-align:center;cursor:pointer;text-decoration:none;">
                Batalkan Pesanan
            </button>
        </form>
    </div>
    @else
    <a href="{{ route('menu.index', $table->slug) }}"
       style="display:block;background:#FF8C42;color:#FFF3E0;text-decoration:none;border-radius:14px;padding:15px;font-size:15px;font-weight:800;text-align:center;">
        + Pesan Lagi
    </a>
    @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('paymentWatcher', () => ({
        orderNumber: '{{ $order->order_number }}',
        isUnpaidQris: {{ $order->payment_method === 'qris' && $order->payment_status === 'unpaid' ? 'true' : 'false' }},

        init() {
            if (!this.isUnpaidQris) return;

            // 1) WebSocket auto-detect (kalau Pusher hidup)
            if (window.Echo) {
                window.Echo.channel('order-' + this.orderNumber)
                    .listen('.payment.updated', (e) => {
                        if (e.status === 'paid') {
                            window.location.reload();
                        }
                    });

                window.Echo.channel('order.' + this.orderNumber)
                    .listen('OrderStatusUpdated', (e) => {
                        window.location.reload();
                    });
            }

            // 2) Fallback: polling cerdas (2s, 5s, 10s) — query Midtrans API langsung
            //    Handle skenario di mana Midtrans callback gak nyampe server
            const checkPayment = (delay) => {
                setTimeout(() => {
                    fetch('{{ route('menu.order.check-payment', [$table->slug, $order->order_number]) }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    })
                        .then(r => r.json())
                        .then(d => {
                            if (d.payment_status === 'paid') window.location.reload();
                        })
                        .catch(() => {});
                }, delay);
            };
            checkPayment(2000);
            checkPayment(5000);
            checkPayment(10000);
        },
    }));
});
</script>

@php $waNum = setting('nomor_wa'); @endphp
@if($waNum)
<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waNum) }}?text=Halo%20{{ urlencode(setting('nama_warung')) }}%2C%20saya%20mau%20tanya%20pesanan%20nomor%20{{ urlencode($order->order_number) }}"
   target="_blank" rel="noopener" class="wa-float" aria-label="Chat WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
@endif
</body>
</html>
