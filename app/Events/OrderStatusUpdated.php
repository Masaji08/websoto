<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['table', 'items.menuItem']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('kasir-orders'),
            new PrivateChannel('order-' . $this->order->order_number),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'table_name' => $this->order->table->name,
                'status' => $this->order->status,
                'payment_status' => $this->order->payment_status,
                'payment_method' => $this->order->payment_method,
                'items_count' => $this->order->items->sum('quantity'),
                'total_amount_formatted' => 'Rp ' . number_format($this->order->total_amount, 0, ',', '.'),
            ],
        ];
    }
}
