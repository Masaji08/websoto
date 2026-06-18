<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_warung' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'alamat' => 'nullable|string|max:500',
            'penerimaan_pesanan' => 'nullable|string|max:500',
            'kontak_arl' => 'nullable|string|max:30',
            'kontak_ssb' => 'nullable|string|max:30',
            'jam_operasional' => 'nullable|string|max:100',
            'nomor_wa' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        foreach (['nama_warung', 'deskripsi', 'alamat', 'penerimaan_pesanan', 'kontak_arl', 'kontak_ssb', 'jam_operasional', 'nomor_wa'] as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $validated[$key] ?? '']
            );
            settingRefresh($key);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');

            $old = Setting::where('key', 'logo')->value('value');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            Setting::updateOrCreate(['key' => 'logo'], ['value' => $path]);
            settingRefresh('logo');
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function removeLogo()
    {
        $old = Setting::where('key', 'logo')->value('value');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        Setting::where('key', 'logo')->update(['value' => null]);
        settingRefresh('logo');

        return redirect()->route('admin.settings')->with('success', 'Logo berhasil dihapus.');
    }
}
