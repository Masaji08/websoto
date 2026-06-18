<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all');
        $paymentMethod = $request->get('method', '');

        $query = Order::with(['table', 'items.menuItem'])
            ->where('payment_status', 'paid');

        if ($period === 'today') {
            $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        } elseif ($period === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        $summaryQuery = Order::where('payment_status', 'paid');

        if ($period !== 'all') {
            $range = match ($period) {
                'today' => [now()->startOfDay(), now()->endOfDay()],
                'week' => [now()->startOfWeek(), now()->endOfWeek()],
                'month' => [now()->startOfMonth(), now()->endOfMonth()],
                default => [now()->startOfDay(), now()->endOfDay()],
            };
            $summaryQuery->whereBetween('created_at', $range);
        }

        $summary = (object) [
            'total_transactions' => (clone $summaryQuery)->count(),
            'total_revenue' => (clone $summaryQuery)->sum('total_amount'),
        ];

        $methodTotals = Order::where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.payments.index', compact('payments', 'summary', 'methodTotals', 'period', 'paymentMethod'));
    }
}
