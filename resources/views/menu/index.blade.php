<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ setting('nama_warung') }} - {{ $table->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.12/dist/cdn.min.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#FFFFFF; color:#1F2937; margin:0; -webkit-font-smoothing:antialiased; }
        .cat-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none; }
        .cat-scroll::-webkit-scrollbar { display:none; }
        .menu-card { display:flex; gap:12px; background:white; border-radius:14px; padding:12px; border:1px solid #E5E0D8; transition:all 0.15s; position:relative; overflow:hidden; }
        .menu-card:active { transform:scale(0.99); }
        .menu-card.unavailable { opacity:0.6; }
        .menu-img { width:80px; height:80px; border-radius:12px; object-fit:cover; flex-shrink:0; }
        .menu-img-placeholder { width:80px; height:80px; border-radius:12px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:800; color:#1F2937; }
        .badge-featured { position:absolute; top:8px; left:8px; background:#FFF3E0; color:#6D4C41; font-size:10px; font-weight:700; padding:2px 8px; border-radius:999px; z-index:2; }
        .badge-habis { position:absolute; top:8px; left:8px; background:rgba(0,0,0,0.6); color:white; font-size:10px; font-weight:700; padding:2px 8px; border-radius:999px; z-index:2; }
        .add-btn { width:40px; height:40px; border-radius:999px; background:#FF8C42; color:white; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; box-shadow:0 2px 6px rgba(255,140,66,0.3); }
        .add-btn:active { transform:scale(0.92); }
        .add-btn:disabled { background:#ccc; cursor:not-allowed; box-shadow:none; }
        .qty-group { display:inline-flex; align-items:center; gap:0; border:1.5px solid #FF8C42; border-radius:999px; overflow:hidden; background:white; }
        .qty-group button { width:38px; height:38px; display:flex; align-items:center; justify-content:center; border:none; background:white; color:#FF8C42; font-size:18px; font-weight:700; cursor:pointer; min-width:44px; }
        .qty-group button:active { background:#FFF3E0; }
        .qty-group .qty-val { min-width:28px; text-align:center; font-weight:700; font-size:14px; color:#FF8C42; }
        @keyframes slideUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .anim-in { animation:slideUp 0.3s ease-out forwards; }
        .cart-item-btn { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; cursor:pointer; border:none; min-width:44px; min-height:44px; }
        .touch-btn { min-width:44px; min-height:44px; display:flex; align-items:center; justify-content:center; }
        .safe-bottom { padding-bottom:max(16px, env(safe-area-inset-bottom, 16px)); }
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
    <div x-data="cartApp()" x-init="init()">

        @php $mLogoPath = setting('logo'); @endphp
        {{-- Header --}}
        <div style="background:#FF8C42;color:white;position:sticky;top:0;z-index:40;padding-top:env(safe-area-inset-top,0);">
            <div style="padding:12px 14px 6px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div style="flex:1;min-width:0;">
                    <h1 style="font-size:18px;font-weight:800;margin:0;letter-spacing:-0.3px;">{{ setting('nama_warung') }}</h1>
                    <p style="margin:2px 0 0;font-size:12px;opacity:0.75;font-weight:500;">{{ setting('deskripsi') }}</p>
                    <div style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;background:rgba(255,255,255,0.12);border-radius:999px;padding:3px 10px 3px 6px;font-size:11px;font-weight:600;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $table->name }}</span>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    @if ($mLogoPath && (\App\Services\CloudinaryService::isCloudinaryUrl($mLogoPath) || \Illuminate\Support\Facades\Storage::disk('public')->exists($mLogoPath)))
                    <img src="{{ \App\Services\CloudinaryService::getImageUrl($mLogoPath) }}" alt="Logo" style="width:38px;height:38px;border-radius:10px;object-fit:contain;background:rgba(255,255,255,0.2);">
                    @endif
                    <button @click="showCart = true" x-show="totalCount > 0"
                        style="position:relative;width:40px;height:40px;border-radius:999px;background:rgba(255,255,255,0.15);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        <span style="position:absolute;top:-4px;right:-4px;background:#EF4444;color:white;font-size:9px;font-weight:700;width:18px;height:18px;border-radius:999px;display:flex;align-items:center;justify-content:center;border:2px solid #FF8C42;" x-text="totalCount"></span>
                    </button>
                </div>
            </div>
            <div style="padding:0 14px 8px;display:flex;align-items:center;gap:5px;font-size:10px;opacity:0.7;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ setting('jam_operasional') }}</span>
            </div>
        </div>

        @if($unpaidOrder)
        <div style="margin:8px 14px 0;background:#FFF3E0;border:1.5px solid #FF8C42;border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FF8C42" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div style="flex:1;font-size:13px;color:#1F2937;">
                <strong>Pesanan belum dibayar!</strong>
                <a href="{{ route('menu.order.status', [$table->slug, $unpaidOrder->order_number]) }}" style="color:#FF8C42;font-weight:700;text-decoration:underline;display:block;margin-top:2px;">
                    Bayar pesanan #{{ $unpaidOrder->order_number }}
                </a>
            </div>
        </div>
        @endif

        {{-- Packages Section --}}
        @if($packages->count() > 0)
        <div style="padding:6px 14px 0;">
            <div style="background:linear-gradient(135deg,#FF8C42,#6D4C41);border-radius:14px;padding:14px;margin-bottom:4px;">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#FFF3E0"><path d="M11.5 3l2.5 5 5 .5-3.5 3.5 1 5.5-5-2.5-5 2.5 1-5.5L4 8.5l5-.5L11.5 3z"/></svg>
                    <h2 style="font-size:16px;font-weight:800;color:#FFF3E0;margin:0;">Paket Promo</h2>
                    <span style="font-size:10px;color:#6D4C41;font-weight:600;margin-left:auto;">Hemat hingga 15%</span>
                </div>
                <p style="margin:0;font-size:11px;color:#E5E0D8;">Pilih paket hemat, semua item langsung masuk ke pesanan</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:4px;">
                @foreach($packages as $pkg)
                <div style="background:#FFF3E0;border:2px solid #6D4C41;border-radius:14px;overflow:hidden;position:relative;">
                    @if ($pkg->image_path)
                    <div style="height:100px;overflow:hidden;">
                        <img src="{{ \App\Services\CloudinaryService::getImageUrl($pkg->image_path) }}" alt="{{ $pkg->name }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    @endif
                    <div style="position:absolute;top:8px;right:10px;background:#6D4C41;color:#FF8C42;font-size:9px;font-weight:800;padding:3px 8px;border-radius:0 0 6px 6px;z-index:2;">HEMAT Rp {{ number_format($pkg->savings, 0, ',', '.') }}</div>
                    <div style="padding:10px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                        <div style="flex:1;min-width:0;">
                            <h3 style="margin:0;font-size:14px;font-weight:800;color:#1F2937;">{{ $pkg->name }}</h3>
                            <p style="margin:2px 0 0;font-size:10px;color:#6B7280;line-height:1.4;">{{ $pkg->description }}</p>
                        </div>
                    </div>
                    <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:6px;">
                        @foreach($pkg->items as $pi)
                        <span style="background:white;border:1px solid #E5E0D8;border-radius:999px;padding:2px 8px;font-size:9px;color:#FF8C42;font-weight:600;">{{ $pi->quantity }}x {{ $pi->menuItem->name }}</span>
                        @endforeach
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <div style="flex-shrink:0;">
                            <span style="font-size:11px;color:#6B7280;text-decoration:line-through;margin-right:6px;">{{ $pkg->formatted_original_price }}</span>
                            <span style="font-size:16px;font-weight:800;color:#FF8C42;">{{ $pkg->formatted_price }}</span>
                        </div>
                        @php $pkgItemsJson = json_encode($pkg->items->map(fn($i) => ['id' => $i->menu_item_id, 'name' => $i->menuItem->name, 'price' => $i->menuItem->price, 'qty' => $i->quantity])); @endphp
                        <button @click="addPackage('{{ $pkg->name }}', {{ $pkgItemsJson }})"
                                style="background:#FF8C42;color:#FFF3E0;border:none;border-radius:999px;padding:8px 14px;font-size:11px;font-weight:700;cursor:pointer;min-height:44px;white-space:nowrap;">
                            + Pilih Paket
                        </button>
                    </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Menu List --}}
        <div style="padding:6px 14px 100px;">
            @forelse($categories as $category)
                @if($category->menuItems->count() > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px;margin-bottom:6px;padding:0 2px;">
                        <h2 style="font-size:17px;font-weight:800;color:#1F2937;margin:0;">{{ $category->name }}</h2>
                        <span style="font-size:12px;color:#6B7280;">{{ $category->menuItems->count() }} item</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;" class="anim-in">
                        @foreach($category->menuItems as $item)
                            <div class="menu-card {{ !$item->is_available ? 'unavailable' : '' }}">
                                @if($item->is_featured)
                                    <div class="badge-featured">Unggulan</div>
                                @endif
                                @if(!$item->is_available)
                                    <div class="badge-habis">Habis</div>
                                @endif

                                @if($item->image_path)
                                    <img src="{{ \App\Services\CloudinaryService::getImageUrl($item->image_path) }}" alt="{{ $item->name }}" class="menu-img" loading="lazy">
                                @else
                                    @php
                                        $colors = ['#FFF3E0','#FED7AA','#FECACA','#FEF08A','#BFDBFE','#BBF7D0','#C4B5FD','#F9A8D4','#FDBA74','#D9F99D'];
                                        $hash = crc32($item->name);
                                        $color = $colors[abs($hash) % count($colors)];
                                        $lower = strtolower($item->name);
                                        if (str_contains($lower, 'soto')) $letter = 'S';
                                        elseif (str_contains($lower, 'sate')) $letter = 'S';
                                        elseif (str_contains($lower, 'nasi')) $letter = 'N';
                                        elseif (str_contains($lower, 'teh')) $letter = 'T';
                                        elseif (str_contains($lower, 'kopi')) $letter = 'K';
                                        elseif (str_contains($lower, 'air') || str_contains($lower, 'ades') || str_contains($lower, 'aqua') || str_contains($lower, 'le minerale')) $letter = 'A';
                                        else $letter = 'M';
                                    @endphp
                                    <div class="menu-img-placeholder" style="background:{{ $color }};"><span>{{ $letter }}</span></div>
                                @endif

                                <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center;gap:2px;">
                                    <h3 style="margin:0;font-size:13px;font-weight:700;color:#1F2937;line-height:1.3;">{{ $item->name }}</h3>
                                    <p style="margin:0;font-size:11px;color:#6B7280;line-height:1.3;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">{{ $item->description }}</p>
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:3px;">
                                        <span style="font-weight:800;font-size:14px;color:#FF8C42;">Rp {{ number_format($item->price, 0, ',', '.') }}</span>

                                        @if($item->is_available)
                                            <template x-if="!getItem({{ $item->id }})">
                                                <button class="add-btn touch-btn" @click="addItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                            </template>
                                            <template x-if="getItem({{ $item->id }})">
                                                <div class="qty-group">
                                                    <button @click="removeItem({{ $item->id }})">&minus;</button>
                                                    <span class="qty-val" x-text="getItem({{ $item->id }}).qty"></span>
                                                    <button @click="addItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})">+</button>
                                                </div>
                                            </template>
                                        @else
                                            <button class="add-btn" disabled>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @empty
                <div style="text-align:center;padding:48px 16px;color:#6B7280;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="1.5" style="margin:0 auto 12px;opacity:0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>
                    <p style="font-size:15px;font-weight:500;">Menu belum tersedia.</p>
                    <p style="font-size:13px;margin-top:4px;">Silakan hubungi kasir.</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div style="padding:16px 14px 100px;border-top:6px solid #F3F4F6;">
            <div style="text-align:center;font-size:13px;font-weight:800;color:#FF8C42;margin-bottom:10px;">{{ setting('nama_warung') }}</div>
            @php $alamat = setting('alamat'); @endphp
            @if($alamat)
            <div style="display:flex;justify-content:center;gap:6px;margin-bottom:6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF8C42" stroke-width="2" style="flex-shrink:0;margin-top:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <div style="font-size:11px;color:#6B7280;line-height:1.5;white-space:pre-line;text-align:center;">{{ $alamat }}</div>
            </div>
            @endif

            @php $penerimaan = setting('penerimaan_pesanan'); @endphp
            @if($penerimaan)
            <div style="display:flex;justify-content:center;gap:6px;margin-bottom:6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF8C42" stroke-width="2" style="flex-shrink:0;margin-top:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <div style="font-size:11px;color:#6B7280;line-height:1.5;white-space:pre-line;text-align:center;">{{ $penerimaan }}</div>
            </div>
            @endif

            @php $kontakArl = setting('kontak_arl'); $kontakSsb = setting('kontak_ssb'); @endphp
            @if($kontakArl || $kontakSsb)
            <div style="display:flex;justify-content:center;gap:6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FF8C42" stroke-width="2" style="flex-shrink:0;margin-top:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                <div style="font-size:11px;color:#6B7280;line-height:1.5;text-align:center;">
                    @if($kontakArl) <div>{{ $kontakArl }} (Arl)</div> @endif
                    @if($kontakSsb) <div>{{ $kontakSsb }} (SSB)</div> @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Floating Bar --}}
        <div x-show="totalCount > 0"
             style="position:fixed;bottom:0;left:0;right:0;z-index:45;padding:0 12px max(12px, env(safe-area-inset-bottom, 12px));pointer-events:none;">
            <div @click="showCart = true"
                 style="background:#FF8C42;border-radius:14px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;pointer-events:auto;box-shadow:0 -4px 16px rgba(0,0,0,0.12);gap:8px;">
                <div style="flex:1;min-width:0;">
                    <div style="color:rgba(255,255,255,0.7);font-size:11px;" x-text="totalCount + ' item dipilih'"></div>
                    <div style="color:#FFF3E0;font-size:16px;font-weight:800;" x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></div>
                </div>
                <div style="background:#6D4C41;color:#FF8C42;font-size:12px;font-weight:700;padding:8px 16px;border-radius:10px;white-space:nowrap;">
                    Lihat Pesanan
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="display:inline;margin-left:4px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </div>

        {{-- Cart Drawer --}}
        <div x-show="showCart" style="position:fixed;inset:0;z-index:50;">
             <div @click="showCart = false"
                 style="position:absolute;inset:0;background:rgba(31,41,55,0.65);"></div>

            <div style="position:absolute;bottom:0;left:0;right:0;background:#FFFFFF;border-radius:20px 20px 0 0;padding:16px 14px max(16px, env(safe-area-inset-bottom, 16px));max-height:90vh;overflow-y:auto;">

                <div style="width:36px;height:4px;background:#E5E0D8;border-radius:2px;margin:0 auto 14px;"></div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <div style="font-size:16px;font-weight:800;color:#1F2937;">Pesanan Kamu</div>
                    <button @click="showCart = false"
                            style="background:none;border:none;color:#6B7280;font-size:22px;cursor:pointer;min-width:44px;min-height:44px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Cart Items --}}
                <template x-for="(item, id) in cart" :key="id">
                    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:0.5px solid #F3F4F6;">
                        <div style="flex:1;min-width:0;">
                            <div x-text="item.name" style="font-size:13px;font-weight:600;color:#1F2937;"></div>
                            <div x-text="'Rp ' + item.price.toLocaleString('id-ID')" style="font-size:11px;color:#6B7280;margin-top:2px;"></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <button @click="removeItem(id)"
                                    class="cart-item-btn" style="background:#FFF3E0;color:#FF8C42;">−</button>
                            <span x-text="item.qty" style="font-size:14px;font-weight:700;color:#1F2937;min-width:20px;text-align:center;"></span>
                            <button @click="addItem(id, item.name, item.price)"
                                    class="cart-item-btn" style="background:#FF8C42;color:#FFF3E0;">+</button>
                        </div>
                        <div x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"
                             style="font-size:12px;font-weight:700;color:#FF8C42;min-width:68px;text-align:right;"></div>
                    </div>
                </template>

                {{-- Total --}}
                <div style="display:flex;justify-content:space-between;padding:12px 0 4px;font-size:15px;font-weight:800;color:#1F2937;border-top:1.5px solid #E5E0D8;margin-top:4px;">
                    <span>Total</span>
                    <span x-text="'Rp ' + totalPrice.toLocaleString('id-ID')"></span>
                </div>

                {{-- Notes --}}
                <textarea x-model="notes" rows="2"
                          placeholder="Catatan untuk dapur... (contoh: tidak pakai kecap)"
                          style="width:100%;border:1px solid #E5E0D8;border-radius:12px;padding:10px 12px;font-size:13px;color:#1F2937;background:#fff;resize:none;margin:12px 0;font-family:'Plus Jakarta Sans',sans-serif;box-sizing:border-box;outline:none;"></textarea>

                {{-- Payment Options --}}
                <div style="font-size:12px;font-weight:700;color:#1F2937;margin-bottom:8px;">Pilih cara bayar:</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                    <template x-for="method in payMethods" :key="method.value">
                        <div @click="selectedPayment = method.value"
                             :style="selectedPayment === method.value ? 'border-color:#FF8C42;background:#FFF3E0;' : 'border-color:#E5E0D8;background:#fff;'"
                             style="border:1.5px solid;border-radius:12px;padding:10px 12px;cursor:pointer;display:flex;align-items:center;gap:8px;min-height:44px;">
                            <span x-text="method.icon" style="font-size:18px;width:28px;text-align:center;"></span>
                            <span x-text="method.label" style="font-size:13px;font-weight:600;color:#1F2937;"></span>
                        </div>
                    </template>
                </div>

                <p style="font-size:11px;color:#6B7280;margin-bottom:12px;">
                    Pesanan langsung masuk ke kasir setelah kamu memesan
                </p>

                {{-- Order Form --}}
                <form id="orderForm"
                      action="{{ route('menu.order.store', $table->slug) }}"
                      method="POST">
                    @csrf
                    <input type="hidden" id="input-items"   name="items" value="">
                    <input type="hidden" id="input-payment" name="payment_method" value="">
                    <input type="hidden" id="input-notes"   name="notes" value="">

                    <button type="button"
                            @click="submitOrder()"
                            :disabled="submitting"
                            style="background:#FF8C42;color:#FFF3E0;border:none;border-radius:14px;width:100%;padding:14px;font-size:15px;font-weight:800;cursor:pointer;min-height:48px;">
                        <span x-show="!submitting">Pesan Sekarang</span>
                        <span x-show="submitting">Memproses...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function cartApp() {
        return {
            cart: {},
            showCart: false,
            selectedPayment: 'qris',
            notes: '',
            submitting: false,

            payMethods: [
                { value: 'qris', icon: 'QR', label: 'QRIS' },
                { value: 'cash', icon: 'TN', label: 'Tunai' },
            ],

            init() {
                const saved = localStorage.getItem('cart_{{ $table->slug }}');
                if (saved) {
                    try { this.cart = JSON.parse(saved); } catch(e) { this.cart = {}; }
                }
            },

            addItem(id, name, price) {
                id = String(id);
                if (this.cart[id]) {
                    this.cart[id].qty++;
                } else {
                    this.cart[id] = { name: name, price: price, qty: 1 };
                }
                this.cart = Object.assign({}, this.cart);
                this.save();
            },

            addPackage(pkgName, items) {
                items.forEach(i => {
                    for (let n = 0; n < i.qty; n++) {
                        this.addItem(i.id, i.name, i.price);
                    }
                });
            },

            removeItem(id) {
                id = String(id);
                if (!this.cart[id]) return;
                if (this.cart[id].qty > 1) {
                    this.cart[id].qty--;
                    this.cart = Object.assign({}, this.cart);
                } else {
                    const c = Object.assign({}, this.cart);
                    delete c[id];
                    this.cart = c;
                }
                this.save();
            },

            getItem(id) {
                return this.cart[String(id)] || null;
            },

            get totalCount() {
                return Object.values(this.cart).reduce((s, i) => s + i.qty, 0);
            },

            get totalPrice() {
                return Object.values(this.cart).reduce((s, i) => s + (i.price * i.qty), 0);
            },

            cartToArray() {
                return Object.entries(this.cart).map(function(entry) {
                    return { id: parseInt(entry[0]), qty: entry[1].qty };
                });
            },

            save() {
                localStorage.setItem('cart_{{ $table->slug }}', JSON.stringify(this.cart));
            },

            clearCart() {
                this.cart = {};
                localStorage.removeItem('cart_{{ $table->slug }}');
            },

            submitOrder() {
                if (this.totalCount === 0 || this.submitting) return;
                this.submitting = true;

                document.getElementById('input-items').value = JSON.stringify(this.cartToArray());
                document.getElementById('input-payment').value = this.selectedPayment;
                document.getElementById('input-notes').value = this.notes;
                document.getElementById('orderForm').submit();
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
