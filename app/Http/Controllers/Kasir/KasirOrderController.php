<?php

namespace App\Http\Controllers\Kasir;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasirOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['table', 'items.menuItem'])
            ->whereDate('created_at', today())
            ->where(function ($q) {
                $q->where('payment_method', '!=', 'qris')
                  ->orWhere('payment_status', 'paid');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kasir.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.menuItem']);

        return view('kasir.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready,completed,cancelled',
        ]);

        $data = ['status' => $validated['status']];

        if ($order->payment_method === 'cash' && $validated['status'] === 'confirmed') {
            $data['status'] = 'completed';
            $data['payment_status'] = 'paid';
        } elseif ($validated['status'] === 'confirmed' && $order->payment_status !== 'paid') {
            $data['payment_status'] = 'paid';
        }

        $order->update($data);

        broadcast(new OrderStatusUpdated($order));

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    public function markPaid(Order $order)
    {
        $data = ['payment_status' => 'paid'];

        if ($order->payment_method === 'cash') {
            $data['status'] = 'completed';
        }

        $order->update($data);

        broadcast(new OrderStatusUpdated($order));

        return back()->with('success', 'Pembayaran berhasil ditandai lunas.');
    }
}
