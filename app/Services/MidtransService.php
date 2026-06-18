<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function createTransaction(Order $order): string
    {
        $order->load('table', 'items.menuItem');

        $midtransOrderId = $order->order_number . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name ?: 'Pelanggan',
                'last_name'  => $order->table->name,
                'email'      => config('midtrans.customer_email', 'pelanggan@soto.com'),
                'phone'      => config('midtrans.customer_phone', '08000000000'),
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id'       => (string) $item->menu_item_id,
                'price'    => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->menuItem->name, 0, 50),
            ])->toArray(),
            'callbacks' => [
                'finish' => route('payment.finish', ['order' => $order->order_number]),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $order->update([
            'payment_token'      => $snapToken,
            'midtrans_order_id'  => $midtransOrderId,
        ]);

        return $snapToken;
    }

    public function handleCallback(): ?Order
    {
        $notif = new Notification();

        $signatureKey = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . Config::$serverKey);
        if ($signatureKey !== $notif->signature_key) {
            throw new \Exception('Invalid Midtrans signature');
        }

        $transactionStatus = $notif->transaction_status;
        $fraudStatus       = $notif->fraud_status;

        $order = Order::where('midtrans_order_id', $notif->order_id)->first();

        if (!$order) {
            $parts = explode('-', $notif->order_id);
            $orderNumber = $parts[0] . '-' . $parts[1];
            $order = Order::where('order_number', $orderNumber)->first();
        }

        if (!$order) {
            return null;
        }

        $updateData = [
            'transaction_id'   => $notif->transaction_id,
            'payment_type'     => $notif->payment_type,
            'transaction_time' => $notif->transaction_time,
        ];

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $updateData['payment_status'] = 'paid';
                $updateData['status'] = 'confirmed';
            } else {
                $updateData['payment_status'] = 'failed';
            }
        } elseif ($transactionStatus === 'settlement') {
            $updateData['payment_status'] = 'paid';
            $updateData['status'] = 'confirmed';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $updateData['payment_status'] = 'failed';
        } elseif ($transactionStatus === 'pending') {
            $updateData['payment_status'] = 'unpaid';
        }

        $order->update($updateData);

        return $order;
    }

    public function checkPaymentStatus(Order $order): Order
    {
        $midtransOrderId = $order->midtrans_order_id;

        if (!$midtransOrderId) {
            return $order;
        }

        try {
            $status = Transaction::status($midtransOrderId);

            $updateData = [];

            if (isset($status->transaction_id)) {
                $updateData['transaction_id'] = $status->transaction_id;
            }
            if (isset($status->payment_type)) {
                $updateData['payment_type'] = $status->payment_type;
            }
            if (isset($status->transaction_time)) {
                $updateData['transaction_time'] = $status->transaction_time;
            }

            $transactionStatus = $status->transaction_status ?? '';
            $fraudStatus       = $status->fraud_status ?? '';

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $updateData['payment_status'] = 'paid';
                    $updateData['status'] = 'confirmed';
                } else {
                    $updateData['payment_status'] = 'failed';
                }
            } elseif ($transactionStatus === 'settlement') {
                $updateData['payment_status'] = 'paid';
                $updateData['status'] = 'confirmed';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $updateData['payment_status'] = 'failed';
            } elseif ($transactionStatus === 'pending') {
                $updateData['payment_status'] = 'unpaid';
            }

            if (!empty($updateData)) {
                $order->update($updateData);
                $order->refresh();
            }
        } catch (\Exception $e) {
            \Log::warning('Midtrans status check error: ' . $e->getMessage());
        }

        return $order;
    }
}
