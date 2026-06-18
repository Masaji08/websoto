@php $title = 'Kategori'; @endphp

@push('actions')
    <a href="{{ route('admin.categories.create') }}" class="bg-[#FF8C42] text-white px-4 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium hover:bg-[#6D4C41] transition-colors">+ Tambah Kategori</a>
@endpush

<x-layouts.admin>
    <div>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">Nama</th>
                        <th class="text-center px-4 py-3 font-medium">Urutan</th>
                        <th class="text-center px-4 py-3 font-medium">Aktif</th>
                        <th class="text-center px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $cat->name }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $cat->sort_order }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $cat->is_active ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
