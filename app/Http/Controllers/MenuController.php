<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Package;
use App\Models\Table;

class MenuController extends Controller
{
    public function index(Table $table)
    {
        abort_if(!$table->is_active, 404, 'Meja tidak aktif');

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['menuItems' => function ($q) {
                $q->orderBy('sort_order');
            }])
            ->get();

        $packages = Package::where('is_active', true)
            ->orderBy('sort_order')
            ->with('items.menuItem')
            ->get();

        $unpaidOrder = Order::where('table_id', $table->id)
            ->where('payment_method', 'qris')
            ->where('payment_status', 'unpaid')
            ->first();

        return view('menu.index', compact('table', 'categories', 'packages', 'unpaidOrder'));
    }

    public function checkout(Table $table)
    {
        abort_if(!$table->is_active, 404);

        $unpaidOrder = Order::where('table_id', $table->id)
            ->where('payment_method', 'qris')
            ->where('payment_status', 'unpaid')
            ->first();

        return view('menu.checkout', compact('table', 'unpaidOrder'));
    }
}
