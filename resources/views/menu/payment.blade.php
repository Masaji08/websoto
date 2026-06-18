<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>Pembayaran - {{ setting('nama_warung') }}</title>
    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 24px;
        }
        .loader {
            width: 44px; height: 44px;
            border: 4px solid #E5E0D8;
            border-top-color: #FF8C42;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto 24px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div>
        <div class="loader"></div>
        <h2 style="color: #1F2937; font-size: 20px; font-weight: 800; margin-bottom: 6px;">
            Pembayaran
        </h2>
        <p style="color: #6B7280; font-size: 14px; margin-bottom: 20px;">
            {{ $order->table->name }} &middot; #{{ $order->order_number }}
        </p>
        <div style="background: #fff; border-radius: 16px; border: 0.5px solid #E5E0D8; padding: 16px; margin-bottom: 20px; text-align: left; max-width: 320px;">
            @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 0.5px solid #F3F4F6; font-size: 13px;">
                <span style="color: #1F2937; font-weight: 600;">
                    {{ $item->menuItem->name }}
                    <span style="color: #6B7280; font-weight: 400;"> &times;{{ $item->quantity }}</span>
                </span>
                <span style="color: #FF8C42; font-weight: 700;">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
            <div style="display: flex; justify-content: space-between; padding-top: 10px; font-size: 16px; font-weight: 800; color: #1F2937;">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
        <p style="color: #6B7280; font-size: 13px;">Membuka halaman pembayaran...</p>
    </div>

    <script>
    setTimeout(function() {
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route('menu.order.status', [$table->slug, $order->order_number]) }}';
            },
            onPending: function(result) {
                window.location.href = '{{ route('menu.order.status', [$table->slug, $order->order_number]) }}';
            },
            onError: function(result) {
                window.location.href = '{{ route('menu.order.status', [$table->slug, $order->order_number]) }}';
            },
            onClose: function() {
                window.location.href = '{{ route('menu.order.status', [$table->slug, $order->order_number]) }}';
            }
        });
    }, 1000);
    </script>
</body>
</html>
