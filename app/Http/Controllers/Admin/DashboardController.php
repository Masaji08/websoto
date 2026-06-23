<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = [now()->startOfDay(), now()->endOfDay()];

        // Revenue today (paid orders)
        $revenueToday = Order::whereBetween('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Total orders today
        $orderTodayCount = Order::whereBetween('created_at', $today)->count();

        // Active orders (not completed/cancelled)
        $activeOrders = Order::whereNotIn('status', ['completed', 'cancelled'])->count();

        // Available menu count
        $availableMenu = MenuItem::where('is_available', true)->count();
        $totalMenu = MenuItem::count();

        // Recent orders
        $recentOrders = Order::with(['table', 'items.menuItem'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Payment breakdown today
        $paymentBreakdown = Order::whereBetween('created_at', $today)
            ->where('payment_status', 'paid')
            ->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();
        $paymentTransactionCount = $paymentBreakdown->sum('count');

        // Top menu items (all time, paid orders only)
        $topMenuItems = OrderItem::select(
            'menu_item_id',
            DB::raw('SUM(order_items.quantity) as total_qty')
        )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->with('menuItem')
            ->take(5)
            ->get();

        // Table status
        $tables = Table::orderBy('name')->get();
        $activeOrderTableIds = Order::whereNotIn('status', ['completed', 'cancelled'])
            ->select('table_id')
            ->distinct()
            ->pluck('table_id')
            ->toArray();

        return view('dashboard', compact(
            'revenueToday',
            'orderTodayCount',
            'activeOrders',
            'availableMenu',
            'totalMenu',
            'recentOrders',
            'paymentBreakdown',
            'paymentTransactionCount',
            'topMenuItems',
            'tables',
            'activeOrderTableIds'
        ));
    }
}
