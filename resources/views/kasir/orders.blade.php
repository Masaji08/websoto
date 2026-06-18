@php $title = 'Pesanan Masuk'; @endphp

<x-layouts.admin>
    <div x-data="kasirApp()" x-init="init()" class="relative">
        {{-- Toast Notification --}}
        <div x-show="notifShow" x-transition:enter="transition-all duration-500 ease-out"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition-all duration-300 ease-in" x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-4 right-4 z-50 bg-white rounded-xl shadow-2xl border-l-4 border-green-500 p-4 min-w-[320px] flex items-start gap-3"
            x-cloak>
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0" x-html="notifIcon"></div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm" x-text="notifTitle"></p>
                <p class="text-xs text-gray-500 mt-0.5" x-text="notifMsg"></p>
            </div>
            <button x-on:click="notifShow = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Audio notification --}}
        <audio id="notif-sound" preload="auto"
            src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAf39/f4B/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+A"></audio>

        {{-- Status Columns --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="orders-grid">
            {{-- Pending --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-yellow-200/60">
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50/50 px-4 py-3 rounded-t-xl border-b border-yellow-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                        <h3 class="font-semibold text-yellow-800 text-sm">Baru</h3>
                    </div>
                    <span id="count-pending"
                        class="inline-flex items-center justify-center min-w-[24px] h-6 px-1.5 rounded-full bg-yellow-200 text-yellow-800 text-xs font-bold"
                        x-text="counts.pending">{{ $orders->where('status', 'pending')->count() }}</span>
                </div>
                <div class="p-3 space-y-3 min-h-[300px] max-h-[calc(100vh-240px)] overflow-y-auto" id="orders-pending" x-ref="colPending">
                    @foreach ($orders->where('status', 'pending') as $order)
                        @include('kasir._order-card', ['order' => $order])
                    @endforeach
                    <template x-if="counts.pending === 0">
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-400 text-sm font-medium">Semua sudah ditangani</p>
                            <p class="text-gray-300 text-xs mt-1">Tidak ada pesanan baru</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Processing --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-blue-200/60">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50/50 px-4 py-3 rounded-t-xl border-b border-blue-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <h3 class="font-semibold text-blue-800 text-sm">Diproses</h3>
                    </div>
                    <span id="count-processing"
                        class="inline-flex items-center justify-center min-w-[24px] h-6 px-1.5 rounded-full bg-blue-200 text-blue-800 text-xs font-bold"
                        x-text="counts.processing">{{ $orders->whereIn('status', ['confirmed', 'processing'])->count() }}</span>
                </div>
                <div class="p-3 space-y-3 min-h-[300px] max-h-[calc(100vh-240px)] overflow-y-auto" id="orders-processing" x-ref="colProcessing">
                    @foreach ($orders->whereIn('status', ['confirmed', 'processing']) as $order)
                        @include('kasir._order-card', ['order' => $order])
                    @endforeach
                    <template x-if="counts.processing === 0">
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-400 text-sm font-medium">Tidak ada pesanan diproses</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Ready --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-green-200/60">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50/50 px-4 py-3 rounded-t-xl border-b border-green-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <h3 class="font-semibold text-green-800 text-sm">Siap</h3>
                    </div>
                    <span id="count-ready"
                        class="inline-flex items-center justify-center min-w-[24px] h-6 px-1.5 rounded-full bg-green-200 text-green-800 text-xs font-bold"
                        x-text="counts.ready">{{ $orders->where('status', 'ready')->count() }}</span>
                </div>
                <div class="p-3 space-y-3 min-h-[300px] max-h-[calc(100vh-240px)] overflow-y-auto" id="orders-ready" x-ref="colReady">
                    @foreach ($orders->where('status', 'ready') as $order)
                        @include('kasir._order-card', ['order' => $order])
                    @endforeach
                    <template x-if="counts.ready === 0">
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="text-gray-400 text-sm font-medium">Belum ada yang siap</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Completed --}}
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-gray-200/60">
                <div class="bg-gradient-to-r from-gray-50 to-slate-50/50 px-4 py-3 rounded-t-xl border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        <h3 class="font-semibold text-gray-800 text-sm">Selesai</h3>
                    </div>
                    <span id="count-completed"
                        class="inline-flex items-center justify-center min-w-[24px] h-6 px-1.5 rounded-full bg-gray-200 text-gray-800 text-xs font-bold"
                        x-text="counts.completed">{{ $orders->whereIn('status', ['completed', 'cancelled'])->count() }}</span>
                </div>
                <div class="p-3 space-y-3 min-h-[300px] max-h-[calc(100vh-240px)] overflow-y-auto" id="orders-completed" x-ref="colCompleted">
                    @foreach ($orders->whereIn('status', ['completed', 'cancelled']) as $order)
                        @include('kasir._order-card', ['order' => $order])
                    @endforeach
                    <template x-if="counts.completed === 0">
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-400 text-sm font-medium">Belum ada yang selesai</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Realtime notification fallback for non-Alpine Echo --}}
    <script>
    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('kasir-orders')
            .listen('NewOrderReceived', function(e) {
                var toast = document.createElement('div');
                toast.textContent = 'Pesanan baru dari ' + e.order.table_name + '!';
                toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#FF8C42;' +
                    'color:#FFF3E0;padding:14px 20px;border-radius:12px;font-weight:700;' +
                    'font-size:14px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.2);';
                document.body.appendChild(toast);
                setTimeout(function(){ toast.remove(); }, 4000);

                document.title = 'Pesanan Baru! | Kasir';
                setTimeout(function(){ document.title = 'Kasir | Soto Seger Boyolali Pak Antok'; }, 5000);
            });
    }
    </script>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kasirApp', () => ({
                notifShow: false,
                notifIcon: '',
                notifTitle: '',
                notifMsg: '',
                counts: { pending: 0, processing: 0, ready: 0, completed: 0 },
                timers: [],

                init() {
                    this.updateCounts();
                    this.startTimers();
                    this.requestNotifyPermission();

                    if (typeof Echo !== 'undefined') {
                        Echo.private('kasir-orders')
                            .listen('NewOrderReceived', (e) => {
                                this.addOrderCard(e.order, 'pending');
                                this.playNotification();
                                this.sendBrowserNotification(
                                    'Pesanan Baru',
                                    `Pesanan baru dari ${e.order.table_name} (${e.order.order_number}) masuk!`
                                );
                                this.showNotif('new', 'Pesanan Baru!',
                                    `#${e.order.order_number} dari ${e.order.table_name}`
                                );
                                this.updateCounts();
                            })
                            .listen('OrderStatusUpdated', (e) => {
                                this.moveOrderCard(e.order);
                                this.updateCounts();
                                const label = {
                                    pending: 'Baru',
                                    confirmed: 'Dikonfirmasi',
                                    processing: 'Diproses',
                                    ready: 'Siap',
                                    completed: 'Selesai',
                                    cancelled: 'Dibatalkan'
                                } [e.order.status] || e.order.status;
                                this.showNotif('update', 'Status Diperbarui',
                                    `#${e.order.order_number} → ${label}`
                                );
                            });
                    }
                },

                startTimers() {
                    this.timers.forEach(t => clearInterval(t));
                    this.timers = [];
                    document.querySelectorAll('[data-order-id] [data-time]').forEach(el => {
                        const id = setInterval(() => {
                            const createdAt = el.getAttribute('data-time');
                            if (createdAt) el.textContent = this.timeAgo(createdAt);
                        }, 10000);
                        this.timers.push(id);
                    });
                },

                timeAgo(dateStr) {
                    const now = new Date();
                    const date = new Date(dateStr);
                    const diff = Math.floor((now - date) / 1000);
                    if (diff < 60) return 'baru saja';
                    if (diff < 3600) return Math.floor(diff / 60) + 'm lalu';
                    if (diff < 86400) return Math.floor(diff / 3600) + 'j lalu';
                    return Math.floor(diff / 86400) + 'h lalu';
                },

                showNotif(type, title, msg) {
                    this.notifShow = true;
                    this.notifIcon = type === 'new'
                        ? '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
                        : '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>';
                    this.notifTitle = title;
                    this.notifMsg = msg;
                    let self = this;
                    setTimeout(() => { self.notifShow = false; }, 4000);
                },

                requestNotifyPermission() {
                    if ('Notification' in window && Notification.permission === 'default') {
                        Notification.requestPermission();
                    }
                },

                sendBrowserNotification(title, body) {
                    if (!('Notification' in window)) return;
                    if (Notification.permission === 'granted') {
                        const notif = new Notification(title, {
                            body: body,
                            icon: '{{ asset('favicon.ico') }}',
                            tag: 'kasir-order-' + Date.now(),
                            requireInteraction: true,
                        });
                        setTimeout(() => notif.close(), 8000);
                        notif.onclick = () => { window.focus(); notif.close(); };
                    } else if (Notification.permission === 'default') {
                        Notification.requestPermission();
                    }
                },

                playNotification() {
                    const sound = document.getElementById('notif-sound');
                    if (sound) {
                        sound.currentTime = 0;
                        sound.play().catch(() => {});
                    }
                },

                statusColumnId(status) {
                    const map = {
                        pending: 'pending',
                        confirmed: 'processing',
                        processing: 'processing',
                        ready: 'ready',
                        completed: 'completed',
                        cancelled: 'completed'
                    };
                    return 'orders-' + (map[status] || 'pending');
                },

                statusCountId(status) {
                    const map = {
                        pending: 'pending',
                        confirmed: 'processing',
                        processing: 'processing',
                        ready: 'ready',
                        completed: 'completed',
                        cancelled: 'completed'
                    };
                    return 'count-' + (map[status] || 'pending');
                },

                addOrderCard(order, status) {
                    const col = document.getElementById(this.statusColumnId(status));
                    if (!col) return;
                    const card = this.createOrderCard(order);
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = card;
                    wrapper.firstChild.style.opacity = '0';
                    wrapper.firstChild.style.transform = 'translateY(-20px) scale(0.95)';
                    col.insertBefore(wrapper.firstChild, col.firstChild);

                    requestAnimationFrame(() => {
                        const cardEl = col.querySelector('[data-order-id="' + order.id + '"]');
                        if (cardEl) {
                            cardEl.style.transition = 'all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
                            cardEl.style.opacity = '1';
                            cardEl.style.transform = 'translateY(0) scale(1)';
                            cardEl.classList.add('order-card-new');
                            setTimeout(() => cardEl.classList.remove('order-card-new'), 2000);
                        }
                    });
                    this.startTimers();
                },

                moveOrderCard(order) {
                    document.querySelectorAll(`[data-order-id="${order.id}"]`).forEach(el => {
                        el.style.transition = 'all 0.3s ease';
                        el.style.transform = 'scale(0.9)';
                        el.style.opacity = '0';
                        setTimeout(() => {
                            el.remove();
                            if (!['completed', 'cancelled'].includes(order.status)) {
                                this.addOrderCard(order, order.status);
                            }
                        }, 250);
                    });
                    this.startTimers();
                },

                escapeHtml(str) {
                    const div = document.createElement('div');
                    div.textContent = str;
                    return div.innerHTML;
                },

                createOrderCard(order) {
                    const cashLabel = order.payment_method === 'cash' ? 'Konfirmasi & Selesai' : 'Konfirmasi';
                    const statusActions = {
                        pending: `<form onsubmit="event.preventDefault(); this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<svg class=\\'animate-spin h-4 w-4 mx-auto\\' viewBox=\\'0 0 24 24\\'><circle class=\\'opacity-25\\' cx=\\'12\\' cy=\\'12\\' r=\\'10\\' stroke=\\'currentColor\\' stroke-width=\\'4\\' fill=\\'none\\'/><path class=\\'opacity-75\\' fill=\\'currentColor\\' d=\\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\\'/></svg>'; this.submit()" action="/kasir/orders/${order.id}/status" method="POST" class="mt-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="confirmed">
                            <button class="w-full bg-gradient-to-r from-yellow-500 to-amber-500 text-white text-xs py-2 rounded-lg font-semibold hover:from-yellow-600 hover:to-amber-600 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">${cashLabel}</button>
                        </form>`,
                        confirmed: `<form onsubmit="event.preventDefault(); this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<svg class=\\'animate-spin h-4 w-4 mx-auto\\' viewBox=\\'0 0 24 24\\'><circle class=\\'opacity-25\\' cx=\\'12\\' cy=\\'12\\' r=\\'10\\' stroke=\\'currentColor\\' stroke-width=\\'4\\' fill=\\'none\\'/><path class=\\'opacity-75\\' fill=\\'currentColor\\' d=\\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\\'/></svg>'; this.submit()" action="/kasir/orders/${order.id}/status" method="POST" class="mt-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="processing">
                            <button class="w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs py-2 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-600 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">Proses</button>
                        </form>`,
                        processing: `<form onsubmit="event.preventDefault(); this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<svg class=\\'animate-spin h-4 w-4 mx-auto\\' viewBox=\\'0 0 24 24\\'><circle class=\\'opacity-25\\' cx=\\'12\\' cy=\\'12\\' r=\\'10\\' stroke=\\'currentColor\\' stroke-width=\\'4\\' fill=\\'none\\'/><path class=\\'opacity-75\\' fill=\\'currentColor\\' d=\\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\\'/></svg>'; this.submit()" action="/kasir/orders/${order.id}/status" method="POST" class="mt-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs py-2 rounded-lg font-semibold hover:from-green-600 hover:to-emerald-600 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">Siap</button>
                        </form>`,
                        ready: `<form onsubmit="event.preventDefault(); this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<svg class=\\'animate-spin h-4 w-4 mx-auto\\' viewBox=\\'0 0 24 24\\'><circle class=\\'opacity-25\\' cx=\\'12\\' cy=\\'12\\' r=\\'10\\' stroke=\\'currentColor\\' stroke-width=\\'4\\' fill=\\'none\\'/><path class=\\'opacity-75\\' fill=\\'currentColor\\' d=\\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\\'/></svg>'; this.submit()" action="/kasir/orders/${order.id}/status" method="POST" class="mt-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button class="w-full bg-gradient-to-r from-gray-500 to-slate-500 text-white text-xs py-2 rounded-lg font-semibold hover:from-gray-600 hover:to-slate-600 transition-all shadow-sm hover:shadow-md active:scale-[0.98]">Selesai</button>
                        </form>`,
                    };

                    const itemsList = (order.items || []).map(i =>
                        `<span class="text-xs text-gray-600 flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-700 text-[10px] font-bold">${i.quantity}</span>
                            <span>${this.escapeHtml(i.menu_item?.name || i.name || '')}</span>
                        </span>`
                    ).join('');

                    const total = order.total_amount_formatted || 'Rp ' + (order.total_amount || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    const paymentLabel = order.payment_status === 'paid' ? 'Lunas' : 'Belum Bayar';
                    const paymentClass = order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';

                    const isNew = order.status === 'pending' ? ' animate-pulse-border' : '';

                    return `<div data-order-id="${order.id}" class="bg-white border border-gray-100 rounded-lg p-3.5 shadow-sm hover:shadow-md transition-all duration-200${isNew}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-[#FF8C42]/10 flex items-center justify-center font-bold text-[#FF8C42] text-sm">#</span>
                                <div>
                                    <span class="font-bold text-sm text-gray-900">${this.escapeHtml(order.order_number)}</span>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-xs text-gray-500">${order.table_name || order.table?.name || ''}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full whitespace-nowrap" data-time="${order.created_at}">
                                ${order.created_at ? this.timeAgo(order.created_at) : ''}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 mb-2 pl-10">${itemsList}</div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                            <span class="font-bold text-sm text-gray-900">${total}</span>
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full ${paymentClass}">
                                <span class="w-1.5 h-1.5 rounded-full ${order.payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500'}"></span>
                                ${paymentLabel}
                            </span>
                        </div>
                        ${statusActions[order.status] || ''}
                        <a href="/kasir/orders/${order.id}" class="block text-center text-xs text-[#FF8C42] mt-1.5 font-medium hover:underline">Lihat Detail →</a>
                    </div>`;
                },

                updateCounts() {
                    document.querySelectorAll('[id^="orders-"]').forEach(col => {
                        const count = col.querySelectorAll('[data-order-id]').length;
                        const countId = col.id.replace('orders-', 'count-');
                        const el = document.getElementById(countId);
                        if (el) {
                            el.textContent = count;
                            el.style.transition = 'all 0.3s ease';
                            el.style.transform = 'scale(1.3)';
                            setTimeout(() => { el.style.transform = 'scale(1)'; }, 200);
                        }
                    });
                },
            }));
        });
    </script>
    @endpush
</x-layouts.admin>
