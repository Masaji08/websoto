<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class NewOrderReceived implements ShouldBroadcastNow
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
        ];
    }

    public function broadcastWith(): array
    {
        \Log::info('NewOrderReceived broadcastWith called', [
            'order_id' => $this->order->id,
            'channel' => 'kasir-orders',
        ]);
        return [
            'order' => [
                'id' => $this->order->id,
                'table_id' => $this->order->table_id,
                'order_number' => $this->order->order_number,
                'table_name' => $this->order->table->name,
                'status' => $this->order->status,
                'payment_status' => $this->order->payment_status,
                'payment_method' => $this->order->payment_method,
                'total_amount' => $this->order->total_amount,
                'total_amount_formatted' => 'Rp ' . number_format($this->order->total_amount, 0, ',', '.'),
                'items_count' => $this->order->items->sum('quantity'),
                'created_at' => $this->order->created_at->diffForHumans(),
                'items' => $this->order->items->map(fn ($item) => [
                    'name' => $item->menuItem->name,
                    'quantity' => $item->quantity,
                ]),
            ],
        ];
    }
}
