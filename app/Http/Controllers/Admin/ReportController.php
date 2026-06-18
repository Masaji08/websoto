<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        $dateRange = match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };

        $revenue = Order::whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $orderCount = Order::whereBetween('created_at', $dateRange)->count();

        $paidCount = Order::whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->count();

        $avgPerOrder = $paidCount > 0 ? $revenue / $paidCount : 0;

        $chartData = $this->buildChartData();

        $topItems = OrderItem::select(
            'menu_item_id',
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', $dateRange)
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->with('menuItem.category')
            ->take(10)
            ->get();

        $paymentBreakdown = Order::whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $paymentLabels = [
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'cash' => 'Tunai',
        ];

        return view('admin.reports.index', compact(
            'revenue', 'orderCount', 'avgPerOrder', 'chartData',
            'topItems', 'paymentBreakdown', 'paymentLabels', 'period'
        ));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'today');

        $data = $this->getExportData($period);

        $pdf = Pdf::loadView('admin.reports.pdf', $data);

        $filename = 'laporan-pendapatan-' . $period . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'today');

        $data = $this->getExportData($period);

        $filename = 'laporan-pendapatan-' . $period . '-' . now()->format('Y-m-d') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $response = view('admin.reports.excel', $data)->render();

        return response("\xEF\xBB\xBF" . $response, 200, $headers);
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'today');

        $dateRange = $this->getDateRange($period);

        $orders = Order::with(['table', 'items.menuItem'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', $dateRange)
            ->orderBy('created_at', 'desc')
            ->get();

        $periodLabel = match ($period) {
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            default => 'Semua',
        };

        $filename = 'laporan-pendapatan-' . $period . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders, $periodLabel) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Laporan Pendapatan - ' . $periodLabel]);
            fputcsv($handle, ['']);
            fputcsv($handle, ['No. Order', 'Meja', 'Items', 'Total', 'Metode Bayar', 'Waktu']);

            foreach ($orders as $order) {
                $items = $order->items->map(fn ($i) => $i->menuItem->name . ' x' . $i->quantity)->implode(', ');
                fputcsv($handle, [
                    $order->order_number,
                    $order->table?->name ?? '-',
                    $items,
                    $order->total_amount,
                    $order->payment_method,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fputcsv($handle, ['']);
            fputcsv($handle, ['Total Pendapatan', number_format($orders->sum('total_amount'), 0, ',', '.')]);
            fputcsv($handle, ['Total Transaksi', $orders->count()]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildChartData()
    {
        $rawDaily = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [now()->subDays(29)->startOfDay(), now()->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $totals = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $totals[] = (int) ($rawDaily[$date] ?? 0);
        }

        return ['labels' => $labels, 'totals' => $totals];
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    private function getExportData(string $period): array
    {
        $dateRange = $this->getDateRange($period);

        $revenue = Order::whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $orderCount = Order::whereBetween('created_at', $dateRange)->count();

        $paidCount = Order::whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->count();

        $avgPerOrder = $paidCount > 0 ? $revenue / $paidCount : 0;

        $topItems = OrderItem::select(
            'menu_item_id',
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', $dateRange)
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->with('menuItem.category')
            ->take(10)
            ->get();

        $paymentBreakdown = Order::whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $paymentLabels = [
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'cash' => 'Tunai',
        ];

        $periodLabel = match ($period) {
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            default => 'Hari Ini',
        };

        return compact(
            'revenue', 'orderCount', 'avgPerOrder', 'topItems',
            'paymentBreakdown', 'paymentLabels', 'periodLabel', 'period'
        );
    }
}
