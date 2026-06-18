<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TableController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index()
    {
        $tables = Table::orderBy('name')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tables,name',
            'slug' => 'nullable|string|max:255|unique:tables,slug',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $table = Table::create($validated);

        $this->qrCodeService->generate($table);

        return redirect()->route('admin.tables.index')->with('success', 'Table created with QR code.');
    }

    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'slug' => 'nullable|string|max:255|unique:tables,slug,' . $table->id,
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $table->update($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Table updated.');
    }

    public function destroy(Table $table)
    {
        if ($table->qr_code_path) {
            Storage::disk('public')->delete('qrcodes/' . basename($table->qr_code_path));
        }
        $deleted = $table->delete();
        if (!$deleted) {
            return redirect()->route('admin.tables.index')->with('error', 'Gagal menghapus meja.');
        }

        return redirect()->route('admin.tables.index')->with('success', 'Table deleted.');
    }

    public function downloadQr(Table $table)
    {
        $path = public_path($table->qr_code_path);

        if (!file_exists($path)) {
            $this->qrCodeService->generate($table);
            $path = public_path($table->qr_code_path);
        }

        return response()->download($path, 'qr-' . $table->slug . '.svg');
    }
}
