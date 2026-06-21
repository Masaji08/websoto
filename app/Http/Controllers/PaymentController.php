<?php

namespace App\Http\Controllers;

use App\Events\NewOrderReceived;
use App\Events\OrderStatusUpdated;
use App\Events\PaymentStatusUpdated;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        try {
            $order = app(MidtransService::class)->handleCallback();

            if ($order) {
                $order->load('table', 'items.menuItem');

                if ($order->payment_status === 'paid') {
                    \Log::info('PaymentController: broadcasting NewOrderReceived', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ]);
                    broadcast(new NewOrderReceived($order));
                    broadcast(new PaymentStatusUpdated($order->order_number, 'paid'));
                }

                broadcast(new OrderStatusUpdated($order));

                return response()->json(['status' => 'ok']);
            }

            return response()->json(['status' => 'order not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function finish(Request $request)
    {
        $orderNumber = $request->query('order') ?: $request->query('order_id');

        if ($orderNumber) {
            $parts = explode('-', $orderNumber);
            if (count($parts) >= 2) {
                $orderNumber = $parts[0] . '-' . $parts[1];
            }

            $order = Order::with('table')->where('order_number', $orderNumber)->first();

            if ($order) {
                return redirect()->route('menu.order.status', [
                    $order->table->slug,
                    $order->order_number,
                ]);
            }
        }

        return redirect('/');
    }
}
