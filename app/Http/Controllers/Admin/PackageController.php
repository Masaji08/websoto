<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Package;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('items.menuItem')->orderBy('sort_order')->get();
        $itemsJson = $packages->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'description' => $p->description,
            'price' => $p->price,
            'price_formatted' => number_format($p->price, 0, ',', '.'),
            'original_price' => $p->original_price,
            'original_price_formatted' => number_format($p->original_price, 0, ',', '.'),
            'savings' => $p->savings,
            'savings_formatted' => number_format($p->savings, 0, ',', '.'),
            'is_active' => $p->is_active,
            'image_url' => $p->image_url,
            'items' => $p->items->map(fn($i) => [
                'name' => $i->menuItem->name,
                'quantity' => $i->quantity,
            ]),
        ])->values();
        return view('admin.packages.index', compact('packages', 'itemsJson'));
    }

    public function create()
    {
        $menuItems = MenuItem::where('is_available', true)->orderBy('name')->get();
        return view('admin.packages.create', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'sort_order' => 'integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $originalPrice = collect($validated['items'])->sum(fn ($i) => MenuItem::find($i['menu_item_id'])->price * $i['quantity']);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'original_price' => $originalPrice,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = app(CloudinaryService::class)->upload($request->file('image'), 'websoto/packages');
        }

        $package = Package::create($data);

        foreach ($validated['items'] as $item) {
            $package->items()->create($item);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(Package $package)
    {
        $package->load('items.menuItem');
        $menuItems = MenuItem::where('is_available', true)->orderBy('name')->get();
        return view('admin.packages.edit', compact('package', 'menuItems'));
    }

    public function toggleActive(Request $request, Package $package)
    {
        $package->update(['is_active' => $request->boolean('is_active')]);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $originalPrice = collect($validated['items'])->sum(fn ($i) => MenuItem::find($i['menu_item_id'])->price * $i['quantity']);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'original_price' => $originalPrice,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ];

        if ($request->hasFile('image')) {
            $cloudinary = app(CloudinaryService::class);
            if ($package->image_path && CloudinaryService::isCloudinaryUrl($package->image_path)) {
                $cloudinary->delete($package->image_path);
            }
            $data['image_path'] = $cloudinary->upload($request->file('image'), 'websoto/packages');
        }

        DB::transaction(function () use ($package, $data, $validated) {
            $package->update($data);

            $package->items()->delete();
            foreach ($validated['items'] as $item) {
                $package->items()->create($item);
            }
        });

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        if ($package->image_path && CloudinaryService::isCloudinaryUrl($package->image_path)) {
            app(CloudinaryService::class)->delete($package->image_path);
        }
        $deleted = $package->delete();
        if (!$deleted) {
            return redirect()->route('admin.packages.index')->with('error', 'Gagal menghapus paket.');
        }

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil dihapus.');
    }
}
