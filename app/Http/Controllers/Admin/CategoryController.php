<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $updated = $category->update($validated);

        if (!$updated) {
            return redirect()->route('admin.categories.index')->with('error', 'Gagal memperbarui kategori.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->menuItems()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Hapus menu terkait terlebih dahulu.');
        }

        $deleted = $category->delete();
        if (!$deleted) {
            return redirect()->route('admin.categories.index')->with('error', 'Gagal menghapus kategori.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
