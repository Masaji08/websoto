@php $title = 'Kelola Paket Promo'; @endphp

@push('actions')
    <a href="{{ route('admin.packages.create') }}" class="bg-[#FF8C42] text-white px-4 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium hover:bg-[#6D4C41] transition-colors">+ Tambah Paket</a>
@endpush

<x-layouts.admin>
    <div x-data="packageManager()" x-init="init()">
        {{-- Success Alert --}}
        <div x-show="alert.show" x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition-all duration-200 ease-in" x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-4 right-4 z-50 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl shadow-lg text-sm flex items-center gap-3" x-cloak>
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="alert.message"></span>
            <button x-on:click="alert.show = false" class="ml-2 text-green-600 hover:text-green-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-5">
            <div class="relative flex-1 w-full sm:max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="search" placeholder="Cari paket promo..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none">
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span x-text="filteredItems.length + ' paket'"></span>
            </div>
        </div>

        {{-- Package Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <template x-for="pkg in filteredItems" :key="pkg.id">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group">
                    {{-- Header with image or gradient --}}
                    <div class="relative h-28 overflow-hidden flex-shrink-0">
                        <template x-if="pkg.image_url">
                            <img :src="pkg.image_url" :alt="pkg.name"
                                class="w-full h-full object-cover">
                        </template>
                        <template x-if="!pkg.image_url">
                            <div class="w-full h-full bg-gradient-to-br from-[#FF8C42] to-[#6D4C41] flex items-center justify-center">
                                <span class="text-white font-black text-4xl select-none opacity-20" x-text="pkg.name.charAt(0).toUpperCase()"></span>
                            </div>
                        </template>
                        <div class="absolute top-2 right-2 flex gap-1">
                            <span x-show="!pkg.is_active"
                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Nonaktif</span>
                        </div>
                        <div class="absolute bottom-2 left-3 right-3 flex items-center gap-1 flex-wrap">
                            <template x-for="(item, idx) in pkg.items" :key="idx">
                                <span class="bg-white/20 text-white text-[9px] px-1.5 py-0.5 rounded-full font-medium"
                                    x-text="item.quantity + 'x ' + item.name"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900 text-sm truncate" x-text="pkg.name"></h3>
                            </div>
                            <span class="font-bold text-[#FF8C42] text-sm whitespace-nowrap" x-text="'Rp ' + pkg.price_formatted"></span>
                        </div>

                        <p x-show="pkg.description" class="text-xs text-gray-500 mt-1 line-clamp-2" x-text="pkg.description"></p>

                        {{-- Pricing info --}}
                        <div class="flex items-center gap-2 mt-2 text-xs">
                            <span class="text-gray-400 line-through" x-text="'Rp ' + pkg.original_price_formatted"></span>
                            <span class="text-green-600 font-semibold" x-text="'Hemat Rp ' + pkg.savings_formatted"></span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-50">
                            <button type="button" x-on:click="toggleActive(pkg.id, !pkg.is_active)"
                                class="inline-flex items-center gap-2 min-h-[44px] px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150"
                                :class="pkg.is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-red-50 text-red-700 hover:bg-red-100'">
                                <span x-text="pkg.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </button>

                            <div class="flex gap-1">
                                <a :href="'/admin/packages/' + pkg.id + '/edit'"
                                    class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form :action="'/admin/packages/' + pkg.id" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty state --}}
            <div x-show="filteredItems.length === 0" class="col-span-full">
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.5 3l2.5 5 5 .5-3.5 3.5 1 5.5-5-2.5-5 2.5 1-5.5L4 8.5l5-.5L11.5 3z"/>
                    </svg>
                    <p class="text-gray-400 text-sm font-medium">Tidak ada paket ditemukan</p>
                    <p class="text-gray-300 text-xs mt-1" x-text="search ? 'Coba kata kunci lain' : 'Tambahkan paket promo untuk memulai'"></p>
                    <a x-show="!search" href="{{ route('admin.packages.create') }}" class="mt-4 bg-[#FF8C42] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#6D4C41] transition-colors">+ Tambah Paket</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('packageManager', () => ({
                search: '',
                items: @json($itemsJson),
                alert: { show: false, message: '' },

                init() {
                    @if (session('success'))
                        this.showAlert('{{ session('success') }}');
                    @endif
                },

                get filteredItems() {
                    return this.items.filter(pkg => {
                        return !this.search ||
                            pkg.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            (pkg.description && pkg.description.toLowerCase().includes(this.search.toLowerCase()));
                    });
                },

                showAlert(message) {
                    this.alert = { show: true, message };
                    setTimeout(() => { this.alert.show = false; }, 3000);
                },

                toggleActive(id, newVal) {
                    const pkg = this.items.find(i => i.id === id);
                    if (!pkg) return;
                    const oldVal = pkg.is_active;
                    pkg.is_active = newVal;
                    fetch('/admin/packages/' + id + '/toggle', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ is_active: newVal })
                    }).then(r => r.json()).then(data => {
                        if (!data.success) {
                            pkg.is_active = oldVal;
                            this.showAlert('Gagal mengubah status');
                        }
                    }).catch(() => {
                        pkg.is_active = oldVal;
                        this.showAlert('Gagal mengubah status');
                    });
                },
            }));
        });
    </script>
    @endpush
</x-layouts.admin>
