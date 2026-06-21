<?php

namespace App\Http\Controllers;

use App\Events\NewOrderReceived;
use App\Events\PaymentStatusUpdated;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request, Table $table)
    {
        abort_if(!$table->is_active, 404);

        $existingUnpaid = Order::where('table_id', $table->id)
            ->where('payment_method', 'qris')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '>', now()->subMinutes(30))
            ->exists();

        if ($existingUnpaid) {
            $msg = 'Selesaikan pembayaran pesanan sebelumnya terlebih dahulu.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 409);
            }
            return back()->withErrors(['items' => $msg]);
        }

        $request->validate([
            'payment_method' => 'required|in:qris,cash',
            'notes'          => 'nullable|string|max:300',
        ]);

        $items = $request->input('items');
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (empty($items) || !is_array($items)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pilih menu terlebih dahulu.'], 422);
            }
            return back()->withErrors(['items' => 'Pilih menu terlebih dahulu.']);
        }

        $total      = 0;
        $orderItems = [];

        foreach ($items as $item) {
            $menuItem  = MenuItem::findOrFail($item['id']);
            $subtotal  = $menuItem->price * intval($item['qty']);
            $total    += $subtotal;
            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'quantity'     => intval($item['qty']),
                'unit_price'   => $menuItem->price,
                'subtotal'     => $subtotal,
            ];
        }

        $order = DB::transaction(function () use ($table, $total, $request, $orderItems) {
            $order = Order::create([
                'table_id'       => $table->id,
                'order_number'   => $this->generateOrderNumber(),
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $request->payment_method,
                'notes'          => $request->notes,
                'total_amount'   => $total,
            ]);

            foreach ($orderItems as $oi) {
                $order->items()->create($oi);
            }

            return $order;
        });

        if ($request->payment_method === 'cash') {
            broadcast(new NewOrderReceived($order->load('table', 'items.menuItem')));

            if ($request->wantsJson()) {
                return response()->json([
                    'redirect'     => route('menu.order.status', [$table->slug, $order->order_number]),
                    'order_number' => $order->order_number,
                ]);
            }
            return redirect()->route('menu.order.status', [$table->slug, $order->order_number]);
        }

        try {
            $snapToken = app(\App\Services\MidtransService::class)->createTransaction($order);

            if ($request->wantsJson()) {
                return response()->json([
                    'snap_token'   => $snapToken,
                    'order_number' => $order->order_number,
                ]);
            }

            return view('menu.payment', compact('table', 'order', 'snapToken'));
        } catch (\Exception $e) {
            \Log::error('Midtrans error untuk order ' . $order->order_number . ': ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'message'  => 'Pembayaran digital belum tersedia. Silakan bayar tunai ke kasir.',
                    'redirect' => route('menu.order.status', [$table->slug, $order->order_number]),
                ], 500);
            }

            return redirect()
                ->route('menu.order.status', [$table->slug, $order->order_number])
                ->with('payment_error', 'Pembayaran digital belum tersedia. Silakan bayar tunai ke kasir.');
        }
    }

    public function status(Request $request, Table $table, string $orderNumber)
    {
        $order = Order::with(['items.menuItem', 'table'])
            ->where('order_number', $orderNumber)
            ->where('table_id', $table->id)
            ->firstOrFail();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ]);
        }

        return view('menu.status', compact('table', 'order'));
    }

    public function cancelOrder(Request $request, Table $table, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('table_id', $table->id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('menu.order.status', [$table->slug, $order->order_number])
                ->with('payment_error', 'Pesanan sudah dibayar, tidak bisa dibatalkan.');
        }

        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);

        return redirect()->route('menu.index', $table->slug)
            ->with('success', 'Pesanan #' . $order->order_number . ' berhasil dibatalkan.');
    }

    public function retryPayment(Request $request, Table $table, string $orderNumber)
    {
        $order = Order::with(['items.menuItem', 'table'])
            ->where('order_number', $orderNumber)
            ->where('table_id', $table->id)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('menu.order.status', [$table->slug, $order->order_number]);
        }

        try {
            $snapToken = app(\App\Services\MidtransService::class)->createTransaction($order);
            return view('menu.payment', compact('table', 'order', 'snapToken'));
        } catch (\Exception $e) {
            \Log::error('Retry payment error: ' . $e->getMessage());
            return redirect()
                ->route('menu.order.status', [$table->slug, $order->order_number])
                ->with('payment_error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    public function checkPayment(Request $request, Table $table, string $orderNumber)
    {
        $order = Order::with('items.menuItem', 'table')
            ->where('order_number', $orderNumber)
            ->where('table_id', $table->id)
            ->firstOrFail();

        $order = app(\App\Services\MidtransService::class)->checkPaymentStatus($order);

        if ($order->payment_status === 'paid') {
            $order->load('items.menuItem');
            \Log::info('checkPayment: broadcasting NewOrderReceived', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
            broadcast(new \App\Events\NewOrderReceived($order));
            broadcast(new \App\Events\OrderStatusUpdated($order));
            broadcast(new PaymentStatusUpdated($order->order_number, 'paid'));

            if ($request->wantsJson()) {
                return response()->json([
                    'status'         => $order->status,
                    'payment_status' => $order->payment_status,
                    'message'        => 'Pembayaran berhasil dikonfirmasi!',
                ]);
            }

            return redirect()->route('menu.order.status', [$table->slug, $order->order_number])
                ->with('success', 'Pembayaran berhasil dikonfirmasi!');
        }

        if ($order->payment_status === 'failed') {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'         => $order->status,
                    'payment_status' => $order->payment_status,
                    'message'        => 'Pembayaran gagal atau dibatalkan.',
                ]);
            }

            return redirect()->route('menu.order.status', [$table->slug, $order->order_number])
                ->with('payment_error', 'Pembayaran gagal atau dibatalkan.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status'         => $order->status,
                'payment_status' => $order->payment_status,
                'message'        => 'Pembayaran belum diterima. Silakan coba lagi.',
            ]);
        }

        return redirect()->route('menu.order.status', [$table->slug, $order->order_number])
            ->with('info', 'Pembayaran belum diterima. Silakan coba lagi.');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = chr(rand(65, 90)) . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->whereDate('created_at', today())->exists());

        return $number;
    }
}
