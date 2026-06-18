@php $title = 'Tambah Kategori'; @endphp

<x-layouts.admin>
    <div class="max-w-xl">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none">
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="text-sm">Aktif</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-[#FF8C42] text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-[#6D4C41] transition-colors">Simpan</button>
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 rounded-lg text-sm border border-gray-200 hover:bg-gray-50 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
