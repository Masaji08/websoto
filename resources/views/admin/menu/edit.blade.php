@php $title = 'Edit Menu'; @endphp

<x-layouts.admin>
    <div x-data="menuForm()" class="max-w-3xl">
        <form action="{{ route('admin.menu-items.update', $menuItem, false) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200">
            @csrf @method('PUT')

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">Edit Menu</h3>
                    <p class="text-xs text-gray-500 mt-0.5">#{{ $menuItem->name }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $menuItem->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $menuItem->is_available ? 'Tersedia' : 'Habis' }}
                </span>
            </div>

            <div class="p-6 space-y-5">
                {{-- Image Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Menu</label>
                    <div class="flex items-start gap-4">
                        <div class="w-28 h-28 rounded-xl border-2 border-dashed border-gray-200 overflow-hidden flex-shrink-0 bg-gray-50 flex items-center justify-center"
                            :class="{ 'border-[#FF8C42]': preview }">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <img src="{{ \App\Services\CloudinaryService::getImageUrl($menuItem->image_path) ?? 'https://placehold.co/300x200/FFFBF5/7A2E0E?text=Menu' }}" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div class="flex-1">
                            <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Ganti Gambar
                                <input type="file" name="image" accept="image/*" class="hidden" x-on:change="previewImage($event)">
                            </label>
                            <p class="text-xs text-gray-400 mt-1.5">Format: JPEG, PNG, WebP. Maks 2MB</p>
                        </div>
                    </div>
                </div>

                {{-- Name & Category --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $menuItem->name) }}" required
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $menuItem->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors resize-none"
                        placeholder="Deskripsi singkat tentang menu ini...">{{ old('description', $menuItem->description) }}</textarea>
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                        <input type="number" name="price" value="{{ old('price', $menuItem->price) }}" required min="0"
                            class="w-full border border-gray-200 rounded-lg pl-10 pr-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                            placeholder="0">
                    </div>
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1" {{ $menuItem->is_available ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </div>
                        <span class="text-sm text-gray-700 group-hover:text-gray-900">Tersedia</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="relative">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1" {{ $menuItem->is_featured ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-yellow-500"></div>
                        </div>
                        <span class="text-sm text-gray-700 group-hover:text-gray-900">Unggulan</span>
                    </label>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-b-xl">
                <a href="{{ route('admin.menu-items.index', [], false) }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">← Kembali</a>
                <div class="flex gap-2">
                    <a href="{{ route('admin.menu-items.index', [], false) }}" class="px-5 py-2 rounded-lg text-sm border border-gray-200 bg-white hover:bg-gray-50 transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-[#FF8C42] text-white rounded-lg text-sm font-medium hover:bg-[#6D4C41] transition-colors shadow-sm">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('menuForm', () => ({
                preview: null,
                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => { this.preview = e.target.result; };
                        reader.readAsDataURL(file);
                    }
                },
            }));
        });
    </script>
    @endpush
</x-layouts.admin>
