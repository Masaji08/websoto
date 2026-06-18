@php $title = 'Meja & QR'; @endphp

@push('actions')
    <a href="{{ route('admin.tables.create') }}" class="bg-[#FF8C42] text-white px-4 py-1.5 min-h-[44px] inline-flex items-center rounded-lg text-xs font-medium hover:bg-[#6D4C41] transition-colors">+ Tambah Meja</a>
@endpush

<x-layouts.admin>
    <div>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($tables as $table)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold">{{ $table->name }}</h3>
                            <p class="text-xs text-gray-500">Slug: {{ $table->slug }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $table->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $table->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if ($table->qr_code_path)
                        <div class="mb-3 bg-gray-50 rounded-lg p-2 flex justify-center">
                            @if (file_exists(public_path($table->qr_code_path)))
                                <img src="{{ asset($table->qr_code_path) }}" alt="QR {{ $table->name }}" class="w-32 h-32 object-contain">
                            @else
                                <p class="text-xs text-gray-400">QR tidak tersedia</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('admin.tables.edit', $table) }}" class="flex-1 text-center px-3 py-1.5 min-h-[44px] inline-flex items-center justify-center border border-gray-200 rounded-lg text-xs hover:bg-gray-50">Edit</a>
                        <a href="{{ route('admin.tables.qr', $table) }}" class="flex-1 text-center px-3 py-1.5 min-h-[44px] inline-flex items-center justify-center bg-[#FF8C42] text-white rounded-lg text-xs hover:bg-[#6D4C41] transition-colors">Download QR</a>
                        <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Hapus meja ini?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 min-h-[44px] inline-flex items-center justify-center border border-red-200 text-red-600 rounded-lg text-xs hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
