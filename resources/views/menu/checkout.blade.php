<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>Checkout - {{ setting('nama_warung') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.12/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        :root { --primary: #FF8C42; --accent: #6D4C41; --highlight: #FFF3E0; --bg: #FFFFFF; --text: #1F2937; --muted: #6B7280; --border: #E5E0D8; }
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; -webkit-font-smoothing: antialiased; }
        .pay-option { border: 2px solid var(--border); border-radius: 14px; padding: 14px 12px; cursor: pointer; transition: all 0.2s; text-align: center; background: white; display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .pay-option.selected { border-color: var(--primary); background: #FFF3E0; }
        .pay-option input { display: none; }
        .pay-option .pay-icon { width: 40px; height: 40px; border-radius: 999px; display: flex; align-items: center; justify-content: center; }
        .pay-option .pay-label { font-size: 13px; font-weight: 700; }
        .pay-option .pay-desc { font-size: 11px; color: var(--muted); }
        .qty-btn { width: 32px; height: 32px; border-radius: 999px; border: 1.5px solid var(--border); background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: 700; font-size: 16px; color: var(--text); transition: all 0.15s; }
        .qty-btn:active { background: #f5f5f5; }
        .btn-primary { background: var(--primary); color: white; border: none; border-radius: 14px; padding: 16px; font-size: 16px; font-weight: 700; width: 100%; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-primary:active { transform: scale(0.98); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .spinner { width: 18px; height: 18px; border: 2.5px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
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
<body x-data="checkoutApp()" x-init="init()">
    @php $chkLogoPath = setting('logo'); @endphp
    {{-- Header --}}
    <div style="background:var(--primary); color:white; padding:14px 18px; display:flex; align-items:center; gap:12px; position:sticky; top:0; z-index:20;">
        <a href="{{ route('menu.index', $table->slug) }}" style="color:white; text-decoration:none; display:flex;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7l-7 7 7 7"/></svg>
        </a>
        <h1 style="font-size:18px; font-weight:700; margin:0; flex:1;">Checkout</h1>
        @if ($chkLogoPath && (\App\Services\CloudinaryService::isCloudinaryUrl($chkLogoPath) || \Illuminate\Support\Facades\Storage::disk('public')->exists($chkLogoPath)))
        <img src="{{ \App\Services\CloudinaryService::getImageUrl($chkLogoPath) }}" alt="Logo" style="width:34px;height:34px;border-radius:8px;object-fit:contain;background:rgba(255,255,255,0.2);">
        @endif
    </div>

    <div style="padding:16px; max-width:480px; margin:0 auto;">

        @if(!empty($unpaidOrder))
        <div style="background:#FFF3E0;border:1.5px solid #FF8C42;border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FF8C42" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div style="flex:1;font-size:13px;color:#1F2937;">
                <strong>Pesanan sebelumnya belum dibayar!</strong>
                <a href="{{ route('menu.order.status', [$table->slug, $unpaidOrder->order_number]) }}" style="color:#FF8C42;font-weight:700;text-decoration:underline;display:block;margin-top:2px;">
                    Bayar pesanan #{{ $unpaidOrder->order_number }}
                </a>
            </div>
        </div>
        @endif

        {{-- Empty cart --}}
        <template x-if="items.length === 0">
            <div style="text-align:center; padding:48px 16px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" style="margin:0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <p style="color:var(--muted); font-size:15px; margin:0 0 16px;">Belum ada item dipilih</p>
                <a href="{{ route('menu.index', $table->slug) }}" style="display:inline-block; background:var(--primary); color:white; padding:12px 28px; border-radius:999px; font-weight:700; font-size:14px; text-decoration:none;">Pilih Menu</a>
            </div>
        </template>

        {{-- Cart Content --}}
        <template x-if="items.length > 0">
            <div>

                {{-- Items Summary --}}
                <div style="background:white; border-radius:14px; border:1px solid var(--border); overflow:hidden;">
                    <div style="padding:14px 16px; border-bottom:1px solid var(--border);">
                        <h2 style="margin:0; font-size:15px; font-weight:700;">Pesanan Anda</h2>
                    </div>
                    <template x-for="(item, idx) in items" :key="item.id">
                        <div style="display:flex; align-items:center; gap:10px; padding:12px 16px; border-bottom:1px solid #E5E0D8;" x-show="item.quantity > 0">
                            <div style="flex:1; min-width:0;">
                                <p style="margin:0; font-size:14px; font-weight:600;" x-text="item.name"></p>
                                <p style="margin:2px 0 0; font-size:12px; color:var(--muted);" x-text="formatRupiah(item.price) + ' / pcs'"></p>
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <button class="qty-btn" @click="changeQty(idx, -1)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                </button>
                                <span style="font-weight:700; font-size:15px; min-width:20px; text-align:center;" x-text="item.quantity"></span>
                                <button class="qty-btn" @click="changeQty(idx, 1)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </button>
                                <button @click="removeItem(idx)" style="width:32px; height:32px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--muted); flex-shrink:0;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div style="display:flex; justify-content:space-between; padding:14px 16px; background:#FFFFFF; font-weight:800; font-size:16px;">
                        <span>Total</span>
                        <span style="color:var(--primary);" x-text="formatRupiah(total)"></span>
                    </div>
                </div>

                {{-- Payment Method Grid 2x2 --}}
                <div style="margin-top:20px;">
                    <h2 style="font-size:15px; font-weight:700; margin:0 0 10px;">Metode Pembayaran</h2>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <template x-for="pay in paymentMethods" :key="pay.value">
                            <div class="pay-option" :class="{ 'selected': paymentMethod === pay.value }" @click="paymentMethod = pay.value">
                                <div class="pay-icon" :style="'background:' + pay.color">
                                    <span x-html="pay.icon"></span>
                                </div>
                                <div class="pay-label" x-text="pay.label"></div>
                                <div class="pay-desc" x-text="pay.desc"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Notes --}}
                <div style="margin-top:20px;">
                    <label style="font-size:14px; font-weight:600; display:block; margin-bottom:6px;">Catatan (opsional)</label>
                    <textarea x-model="notes" rows="2" placeholder="Catatan untuk dapur... (contoh: tidak pakai kecap)"
                        style="width:100%; border:1.5px solid var(--border); border-radius:12px; padding:12px 14px; font-size:14px; font-family:inherit; outline:none; resize:none; background:white; box-sizing:border-box;"></textarea>
                </div>

                {{-- Submit --}}
                <button class="btn-primary" @click="submitOrder" :disabled="loading" style="margin-top:24px;">
                    <template x-if="loading">
                        <div class="spinner"></div>
                    </template>
                    <span x-text="loading ? 'Memproses...' : 'Pesan Sekarang'"></span>
                    <template x-if="!loading">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </template>
                </button>

                {{-- Error --}}
                <div x-show="error" x-text="error" style="margin-top:12px; color:#DC2626; font-size:13px; text-align:center;"></div>
            </div>
        </template>
    </div>

    <script>
        function checkoutApp() {
            return {
                items: [],
                paymentMethod: 'qris',
                notes: '',
                loading: false,
                error: '',
                paymentMethods: [
                    { value: 'qris', label: 'QRIS', desc: 'GoPay, OVO, DANA, dll', color: '#E8F5E9', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="#6D4C41"><rect x="2" y="2" width="20" height="20" rx="4"/><path d="M7 12l3 3 7-7" stroke="white" stroke-width="2.5" fill="none"/></svg>' },

                    { value: 'cash', label: 'Tunai', desc: 'Bayar di meja kasir', color: '#FFF3E0', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="#6D4C41"><rect x="2" y="6" width="20" height="12" rx="2" fill="#FF8C42"/><circle cx="12" cy="12" r="4" fill="white"/></svg>' },
                ],
                get total() {
                    return this.items.reduce((s, i) => s + (i.price * i.quantity), 0);
                },
                init() {
                    const params = new URLSearchParams(window.location.search);
                    const encoded = params.get('items');
                    if (encoded) {
                        try {
                            this.items = JSON.parse(decodeURIComponent(encoded));
                        } catch(e) {}
                    }
                },
                changeQty(idx, delta) {
                    const item = this.items[idx];
                    if (!item) return;
                    const newQty = item.quantity + delta;
                    if (newQty <= 0) {
                        this.items.splice(idx, 1);
                    } else {
                        item.quantity = newQty;
                    }
                },
                removeItem(idx) {
                    this.items.splice(idx, 1);
                },
                formatRupiah(amount) {
                    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },
                async submitOrder() {
                    if (this.items.length === 0) return;
                    this.loading = true;
                    this.error = '';

                    try {
                        const res = await fetch('{{ route('menu.order.store', $table->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                items: this.items.map(i => ({ id: i.id, quantity: i.quantity })),
                                payment_method: this.paymentMethod,
                                notes: this.notes,
                            }),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            this.error = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                            this.loading = false;
                            return;
                        }

                        sessionStorage.removeItem('cart_' + '{{ $table->slug }}');

                        if (data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function() {
                                    window.location.href = '{{ route('menu.order.status', [$table->slug, '']) }}' + data.order_number;
                                },
                                onPending: function() {
                                    window.location.href = '{{ route('menu.order.status', [$table->slug, '']) }}' + data.order_number;
                                },
                                onClose: function() {
                                    window.location.href = '{{ route('menu.order.status', [$table->slug, '']) }}' + data.order_number;
                                }
                            });
                        } else {
                            window.location.href = data.redirect;
                        }
                    } catch(e) {
                        this.error = 'Koneksi error. Silakan coba lagi.';
                        this.loading = false;
                    }
                }
            };
        }
    </script>

    @php $waNum = setting('nomor_wa'); @endphp
    @if($waNum)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waNum) }}?text=Halo%20{{ urlencode(setting('nama_warung')) }}%2C%20saya%20mau%20tanya%20menu"
       target="_blank" rel="noopener" class="wa-float" aria-label="Chat WhatsApp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
    @endif
</body>
</html>
