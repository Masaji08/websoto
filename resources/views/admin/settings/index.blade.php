@php $title = 'Pengaturan'; @endphp

<x-layouts.admin>
    <div x-data="settingsForm()" class="max-w-3xl">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200">
            @csrf

            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Pengaturan Warung</h3>
                <p class="text-xs text-gray-500 mt-0.5">Atur informasi profil warung</p>
            </div>

            <div class="p-6 space-y-5">
                {{-- Nama Warung --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Warung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_warung" value="{{ old('nama_warung', $settings['nama_warung'] ?? '') }}" required
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                        placeholder="Soto Seger Boyolali Pak Antok">
                    @error('nama_warung') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Warung</label>
                    <textarea name="deskripsi" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors resize-none"
                        placeholder="Warung Soto UMKM">{{ old('deskripsi', $settings['deskripsi'] ?? '') }}</textarea>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="3"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors resize-none"
                        placeholder="Jl. Raya Pasir Putih No. 11 RT-001/ RW-004, Pasir Putih, Sawangan, DEPOK 16519">{{ old('alamat', $settings['alamat'] ?? '') }}</textarea>
                    @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Penerimaan Pesanan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Menerima Pesanan</label>
                    <textarea name="penerimaan_pesanan" rows="3"
                        class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors resize-none"
                        placeholder="Syukuran/ Ulang Tahun, Lamaran/ Pernikahan, Arisan/ Bazar, Acara Kantor">{{ old('penerimaan_pesanan', $settings['penerimaan_pesanan'] ?? '') }}</textarea>
                    @error('penerimaan_pesanan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kontak HP --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak HP 1 (Arl)</label>
                        <input type="text" name="kontak_arl" value="{{ old('kontak_arl', $settings['kontak_arl'] ?? '') }}"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                            placeholder="0812 4623 3061">
                        @error('kontak_arl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak HP 2 (SSB)</label>
                        <input type="text" name="kontak_ssb" value="{{ old('kontak_ssb', $settings['kontak_ssb'] ?? '') }}"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                            placeholder="0852 1159 2205">
                        @error('kontak_ssb') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Jam Operasional & Nomor WA --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Operasional</label>
                        <input type="text" name="jam_operasional" value="{{ old('jam_operasional', $settings['jam_operasional'] ?? '') }}"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                            placeholder="08:00 - 21:00">
                        @error('jam_operasional') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <input type="text" name="nomor_wa" value="{{ old('nomor_wa', $settings['nomor_wa'] ?? '') }}"
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:border-[#FF8C42] focus:ring-1 focus:ring-[#FF8C42] outline-none transition-colors"
                            placeholder="628123456789">
                        @error('nomor_wa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Logo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Warung</label>
                    <div class="flex items-start gap-4">
                        <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-200 overflow-hidden flex-shrink-0 bg-gray-50 flex items-center justify-center">
                            @php $logoPath = $settings['logo'] ?? null; @endphp
                            @if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
                                <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Pilih Logo
                                <input type="file" name="logo" accept="image/*" class="hidden">
                            </label>
                            <p class="text-xs text-gray-400 mt-1.5">Format: JPEG, PNG, WebP. Maks 2MB</p>
                            @if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
                                <a href="{{ route('admin.settings.remove-logo') }}" class="inline-block text-xs text-red-600 hover:text-red-800 mt-1.5" onclick="return confirm('Hapus logo?')">Hapus logo</a>
                            @endif
                            @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-b-xl">
                <p class="text-xs text-gray-400">Semua perubahan akan diterapkan secara otomatis</p>
                <button type="submit" class="px-6 py-2 bg-[#FF8C42] text-white rounded-lg text-sm font-medium hover:bg-[#6D4C41] transition-colors shadow-sm">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
