@php $title = 'Tambah Paket Promo'; @endphp

<x-layouts.admin>
    <div x-data="packageForm()" class="max-w-3xl">
        <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200">
            @csrf

            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Tambah Paket Promo</h3>
                <p class="text-xs text-gray-500 mt-0.5">Buat paket hemat dengan menggabungkan beberapa menu</p>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                            placeholder="Contoh: Paket Soto Komplit">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Promo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                                placeholder="0">
                        </div>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors resize-none"
                        placeholder="Contoh: 1 Soto Ayam Campur Nasi + 1 Perkedel + 1 Es Teh Manis + 1 Kerupuk">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Item dalam Paket <span class="text-red-500">*</span></label>
                    <div class="space-y-2.5">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                                <button type="button" @click="removeItem(index)"
                                    class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <select :name="'items[' + index + '][menu_item_id]'" x-model="item.menu_item_id" required
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none bg-white">
                                    <option value="">Pilih menu...</option>
                                    @foreach ($menuItems as $mi)
                                         <option value="{{ $mi->id }}">{{ $mi->name }} (Rp {{ number_format($mi->price, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs text-gray-500">x</span>
                                    <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" min="1" required
                                        class="w-16 border border-gray-200 rounded-lg px-2 py-2 text-sm text-center focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none">
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addItem()"
                        class="mt-2.5 px-4 py-2.5 min-h-[44px] text-sm text-[#FF8C42] font-medium hover:text-[#6D4C41] hover:bg-orange-50 rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Item
                    </button>
                    @error('items') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-24 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors">
                    <p class="text-xs text-gray-400 mt-1">Semakin kecil angka, semakin atas tampilannya</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Paket</label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#FF8C42]/10 file:text-[#FF8C42] hover:file:bg-[#FF8C42]/20 transition-colors outline-none">
                            <p class="text-xs text-gray-400 mt-1">Format: jpeg, png, jpg, webp. Maks 2MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-b-xl">
                <a href="{{ route('admin.packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">← Kembali</a>
                <div class="flex gap-2">
                    <a href="{{ route('admin.packages.index') }}" class="px-5 py-2.5 min-h-[44px] inline-flex items-center rounded-lg text-sm border border-gray-200 bg-white hover:bg-gray-50 transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2.5 min-h-[44px] bg-[#FF8C42] text-white rounded-lg text-sm font-medium hover:bg-[#6D4C41] transition-colors shadow-sm">Simpan Paket</button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('packageForm', () => ({
                items: [{ menu_item_id: '', quantity: 1 }],
                addItem() {
                    this.items.push({ menu_item_id: '', quantity: 1 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
            }));
        });
    </script>
    @endpush
</x-layouts.admin>
