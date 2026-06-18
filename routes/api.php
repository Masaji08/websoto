<?php

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->get('/order/{order_number}/status', function (string $orderNumber) {
    $order = Order::where('order_number', $orderNumber)->firstOrFail();
    return response()->json([
        'status' => $order->status,
        'payment_status' => $order->payment_status,
    ]);
});
