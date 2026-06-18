<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with('category')->orderBy('category_id')->orderBy('name')->get();
        $categories = Category::orderBy('sort_order')->get();
        $itemsJson = $menuItems->map(fn($i) => [
            'id' => $i->id,
            'name' => $i->name,
            'description' => $i->description,
            'price' => $i->price,
            'price_formatted' => number_format($i->price, 0, ',', '.'),
            'image_url' => $i->image_path ? Storage::url($i->image_path) : null,
            'category_id' => $i->category_id,
            'category_name' => $i->category->name,
            'is_available' => $i->is_available,
            'is_featured' => $i->is_featured,
        ])->values();
        return view('admin.menu.index', compact('menuItems', 'categories', 'itemsJson'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('menu', 'public');
        }

        unset($validated['image']);

        MenuItem::create($validated);

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.menu.edit', compact('menuItem', 'categories'));
    }

    public function toggleAvailability(Request $request, MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => $request->boolean('is_available')]);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('menu', 'public');
        }

        unset($validated['image']);

        $menuItem->update($validated);

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }
        $deleted = $menuItem->delete();
        if (!$deleted) {
            return redirect()->route('admin.menu-items.index')->with('error', 'Gagal menghapus menu.');
        }

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item deleted.');
    }
}
